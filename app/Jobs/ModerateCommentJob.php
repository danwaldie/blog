<?php

namespace App\Jobs;

use App\Actions\Comments\ModerateComment;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ModerateCommentJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Post $post,
        public Comment $comment,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ModerateComment $moderateAction): void
    {
        $moderateAction->handle($this->post, $this->comment);
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): int
    {
        return $this->comment->id;
    }
}
