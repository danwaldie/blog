<?php

use App\Actions\Comments\CreateComment;
use App\Data\Comments\CommentInputData;
use App\Models\Post;
use App\Enums\CommentStatus;
use App\Jobs\ModerateCommentJob;
use Illuminate\Support\Facades\Queue;

it('creates a comment in submitted state and dispatches moderation job', function () {
    Queue::fake();
    $post = Post::factory()->create();
    $action = new CreateComment();

    $data = CommentInputData::from([
        'commenter_name' => 'Jane Doe',
        'body' => 'I love this action!',
    ]);

    $comment = $action->handle($post, $data);

    expect($comment->status)->toBe(CommentStatus::Submitted)
        ->and($comment->commenter_name)->toBe('Jane Doe')
        ->and($comment->body)->toBe('I love this action!')
        ->and($comment->post_id)->toBe($post->id);

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'status' => CommentStatus::Submitted->value,
    ]);

    Queue::assertPushed(ModerateCommentJob::class, function ($job) use ($post, $comment) {
        return $job->post->is($post) && $job->comment->is($comment);
    });
});
