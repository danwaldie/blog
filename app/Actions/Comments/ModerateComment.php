<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * @phpstan-type ModerationResult array{should_reject: bool, confidence: float|int, explanation: string}
 * @phpstan-type OpenAiContentItem array{type: string, text?: string}
 * @phpstan-type OpenAiMessageItem array{type: 'message', role?: string, status?: string, content?: array<int, OpenAiContentItem>}
 * @phpstan-type OpenAiOutputItem OpenAiMessageItem|array{type: string}
 * @phpstan-type OpenAiOutput array<int, OpenAiOutputItem>
 */
class ModerateComment
{
    /**
     * @return array{result: 'ok', data: array{should_reject: bool, confidence: float|int, explanation: string}}
     *     | array{result: 'error', error: string}
     */
    public function handle(Post $post, Comment $comment): array
    {
        $instructions = $this->buildInstructions();
        $prompt = $this->buildPrompt($post, $comment);
        $structuredFormat = $this->buildStructuredFormat();

        try {
            $modelResponse = $this->callOpenAi($instructions, $prompt, $structuredFormat);
            $decoded = $this->extractModerationJson($modelResponse);
        } catch (RequestException|RuntimeException $e) {
            $comment->moderation_error = $e->getMessage();
            $comment->save();
            return ['result' => 'error', 'error' => $e->getMessage()];
        }

        $this->applyModeration($comment, $decoded);

        return ['result' => 'ok', 'data' => $decoded];
    }

    private function buildInstructions(): string
    {
        return <<<TEXT
        You are tasked with determining if comments made on blog posts should be published or rejected.
        Reasons for rejecting include obvious spam unrelated to the post content, sketchy links, or inappropriate and rude comments.
        Err on the side of rejecting if a comment is ambiguous and/or adds no value to the discussion.
        TEXT;
    }

    private function buildPrompt(Post $post, Comment $comment): string
    {
        return <<<TEXT
        Post title: {$post->title}
        Post excerpt: {$post->excerpt}
        Comment: {$comment->body}
        TEXT;
    }

    /** @return array<string, mixed> */
    private function buildStructuredFormat(): array
    {
        return [
            'format' => [
                'type' => 'json_schema',
                'name' => 'comment_moderation',
                'description' => 'Determines if blog comment is spam or violates guidelines and should be published or rejected.',
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'should_reject' => [
                            'type' => 'boolean',
                            'description' => 'Indicates if comment should be rejected due to spam or inappropriate content.',
                        ],
                        'confidence' => [
                            'type' => 'number',
                            'description' => 'Likelihood of the comment being spam given as decimal between 0 and 1.',
                            'minimum' => 0,
                            'maximum' => 1,
                        ],
                        'explanation' => [
                            'type' => 'string',
                            'description' => 'Text explanation for acceptance or rejection and the certainty given.'
                        ],
                    ],
                    'required' => [
                        'should_reject',
                        'confidence',
                        'explanation',
                    ],
                    'additionalProperties' => false,
                ],
                'strict' => true,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $format
     * @return OpenAiOutput
     */
    private function callOpenAi(string $instructions, string $prompt, array $format): array
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withToken(config('services.openai.key'))
            ->post('https://api.openai.com/v1/responses', [
                'instructions' => $instructions,
                'model' => config('services.openai.model'),
                'input' => $prompt,
                'text' => $format,
            ]);
        $response->throw();

        /** @var OpenAiOutput $output */
        $output = $response->json('output') ?? [];
        return $output;
    }

    /**
     * @param OpenAiOutput $responseJson
     * @return ModerationResult
     */
    private function extractModerationJson(array $responseJson): array
    {
        /** @var OpenAiOutput $responseJson */
        /** @var OpenAiMessageItem|null $message */
        $message = collect($responseJson)->first(function (array $item) {
            return ($item['type']) === 'message'
                && ($item['role'] ?? null) === 'assistant'
                && ($item['status'] ?? null) === 'completed';
        });

        if (! $message) {
            throw new RuntimeException('OpenAI response did not include an assistant message output.');
        }

        /** @var array<int, OpenAiContentItem> $content */
        $content = $message['content'] ?? [];

        $outputText = collect($content)->first(function (array $c) {
            return ($c['type']) === 'output_text' && isset($c['text']);
        });

        if (! $outputText) {
            throw new RuntimeException('OpenAI message output did not include output_text content.');
        }

        $text = $outputText['text'];

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            throw new RuntimeException('Failed to decode structured JSON from OpenAI: ' . json_last_error_msg());
        }

        foreach (['should_reject', 'confidence', 'explanation'] as $key) {
            if (! array_key_exists($key, $decoded)) {
                throw new RuntimeException("Structured JSON missing expected key: {$key}");
            }
        }

        // Successful result
        return $decoded;
    }

    /** @param ModerationResult $decoded */
    private function applyModeration(Comment $comment, array $decoded): void
    {
        $comment->moderation_reject = (bool) $decoded['should_reject'];
        $comment->moderation_confidence = (float) $decoded['confidence'];
        $comment->moderation_explanation = (string) $decoded['explanation'];
        $comment->moderated_at = now();

        $comment->status = $decoded['should_reject']
            ? CommentStatus::Rejected
            : CommentStatus::Published;

        $comment->moderation_error = null; // clear old error if present
        $comment->save();
    }
}
