<?php

declare(strict_types=1);

namespace App\Actions\Comments;

use App\Data\Comments\CommentInputData;
use App\Enums\CommentStatus;
use App\Jobs\ModerateCommentJob;
use App\Models\Comment;
use App\Models\Post;
use Carbon\CarbonImmutable;

final class CreateComment
{
    /**
     * Create a new comment. Always in Submitted status to undergo moderation.
     */
    public function handle(Post $post, CommentInputData $data): Comment
    {
        $comment = new Comment();

        $comment->post_id = $post->id;
        $comment->commenter_name = $data->commenter_name;
        $comment->body = $data->body;
        $comment->status = CommentStatus::Submitted;
        $comment->published_at = CarbonImmutable::now();

        $comment->save();

        ModerateCommentJob::dispatch($post, $comment);

        return $comment;
    }
}
