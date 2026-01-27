<?php

use App\Jobs\ModerateCommentJob;
use App\Models\Post;
use App\Models\Comment;
use App\Actions\Comments\ModerateComment;
use Illuminate\Support\Facades\Http;

it('executes the moderate comment action', function () {
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
                            'text' => json_encode([
                                'should_reject' => false,
                                'confidence' => 0.85,
                                'explanation' => 'Job test explanation.',
                            ]),
                        ]
                    ]
                ]
            ]
        ], 200),
    ]);

    $post = Post::factory()->create();
    $comment = Comment::factory()->submitted()->create(['post_id' => $post->id]);

    $job = new ModerateCommentJob($post, $comment);
    $job->handle(new ModerateComment());

    $comment->refresh();
    expect($comment->status)->toBe(\App\Enums\CommentStatus::Published)
        ->and($comment->moderation_explanation)->toBe('Job test explanation.');
});
