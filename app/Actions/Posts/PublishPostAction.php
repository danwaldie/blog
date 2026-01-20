<?php

declare(strict_types=1);

namespace App\Actions\Posts;

use App\Enums\PostStatus;
use App\Models\Post;
use Carbon\CarbonImmutable;

final class PublishPostAction
{
    public function execute(Post $post): Post
    {
        $post->status = PostStatus::Published;
        $post->published_at ??= CarbonImmutable::now();
        $post->save();

        return $post;
    }
}