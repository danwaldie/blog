<?php

use App\Actions\Comments\ModerateComment;
use App\Models\Post;
use App\Models\Comment;
use App\Enums\CommentStatus;
use Illuminate\Support\Facades\Http;

function mockOpenAiResponse($status = 200, $data = null)
{
    if ($status !== 200) {
        return Http::response(['error' => 'Something went wrong'], $status);
    }

    $jsonResponse = json_encode($data ?? [
        'should_reject' => false,
        'confidence' => 0.95,
        'explanation' => 'Safe and constructive comment.',
    ]);

    return Http::response([
        'output' => [
            [
                'type' => 'message',
                'role' => 'assistant',
                'status' => 'completed',
                'content' => [
                    [
                        'type' => 'output_text',
                        'text' => $jsonResponse,
                    ]
                ]
            ]
        ]
    ], 200);
}

it('moderates a comment and marks it as published', function () {
    Http::fake([
        'api.openai.com/v1/responses' => mockOpenAiResponse(200, [
            'should_reject' => false,
            'confidence' => 0.9,
            'explanation' => 'Looks good.',
        ]),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $action = new ModerateComment();
    $result = $action->handle($post, $comment);

    expect($result['result'])->toBe('ok')
        ->and($comment->fresh()->status)->toBe(CommentStatus::Published)
        ->and($comment->fresh()->moderation_reject)->toBeFalse()
        ->and($comment->fresh()->moderation_confidence)->toBe(0.9)
        ->and($comment->fresh()->moderation_explanation)->toBe('Looks good.')
        ->and($comment->fresh()->moderation_error)->toBeNull()
        ->and($comment->fresh()->moderated_at)->not->toBeNull();
});

it('moderates a comment and marks it as rejected', function () {
    Http::fake([
        'api.openai.com/v1/responses' => mockOpenAiResponse(200, [
            'should_reject' => true,
            'confidence' => 0.99,
            'explanation' => 'Spam detected.',
        ]),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $action = new ModerateComment();
    $result = $action->handle($post, $comment);

    expect($result['result'])->toBe('ok')
        ->and($comment->fresh()->status)->toBe(CommentStatus::Rejected)
        ->and($comment->fresh()->moderation_reject)->toBeTrue()
        ->and($comment->fresh()->moderation_confidence)->toBe(0.99)
        ->and($comment->fresh()->moderation_explanation)->toBe('Spam detected.');
});

it('handles HTTP errors from OpenAI', function () {
    Http::fake([
        'api.openai.com/v1/responses' => Http::response(['error' => 'Internal Server Error'], 500),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $action = new ModerateComment();
    $result = $action->handle($post, $comment);

    expect($result['result'])->toBe('error')
        ->and($comment->fresh()->status)->toBe(CommentStatus::Submitted)
        ->and($comment->fresh()->moderation_error)->not->toBeNull();
});

it('handles malformed JSON from OpenAI', function () {
    Http::fake([
        'api.openai.com/v1/responses' => Http::response([
            'output' => [
                [
                    'type' => 'message',
                    'role' => 'assistant',
                    'status' => 'completed',
                    'content' => [
                        [
                            'type' => 'output_text',
                            'text' => '{invalid json',
                        ]
                    ]
                ]
            ]
        ], 200),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $action = new ModerateComment();
    $result = $action->handle($post, $comment);

    expect($result['result'])->toBe('error')
        ->and($comment->fresh()->moderation_error)->toContain('Failed to decode structured JSON');
});

it('handles missing keys in OpenAI response', function () {
    Http::fake([
        'api.openai.com/v1/responses' => mockOpenAiResponse(200, [
            'should_reject' => false,
            // 'confidence' is missing
            'explanation' => 'Missing confidence.',
        ]),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $action = new ModerateComment();
    $result = $action->handle($post, $comment);

    expect($result['result'])->toBe('error')
        ->and($comment->fresh()->moderation_error)->toContain('Structured JSON missing expected key: confidence');
});

it('handles missing assistant message', function () {
    Http::fake([
        'api.openai.com/v1/responses' => Http::response([
            'output' => []
        ], 200),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $action = new ModerateComment();
    $result = $action->handle($post, $comment);

    expect($result['result'])->toBe('error')
        ->and($comment->fresh()->moderation_error)->toContain('OpenAI response did not include an assistant message output');
});
