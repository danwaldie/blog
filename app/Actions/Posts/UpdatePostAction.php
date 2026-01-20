<?php

declare(strict_types=1);

namespace App\Actions\Posts;

use App\Data\Posts\PostInputData;
use App\Models\Post;

final class UpdatePostAction
{
    public function execute(Post $post, PostInputData $data): Post
    {
        $post->title = $data->title;
        $post->slug = $data->slug ?? $post->slug; // if null, keep existing
        $post->excerpt = $data->excerpt;
        $post->body_markdown = $data->bodyMarkdown;
        $post->status = $data->status;
        $post->published_at = $data->publishedAt;
        $post->save();

        $post->tags()->sync($data->tagIds);

        return $post;
    }
}
