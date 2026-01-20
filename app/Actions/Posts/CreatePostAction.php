<?php

declare(strict_types=1);

namespace App\Actions\Posts;

use App\Data\Posts\PostInputData;
use App\Models\Post;
use App\Models\User;

final class CreatePostAction
{
    public function execute(User $author, PostInputData $data): Post
    {
        $post = new Post();
        $post->author_id = $author->id;
        $post->title = $data->title;
        $post->slug = $data->slug ?? '';
        $post->excerpt = $data->excerpt;
        $post->body_markdown = $data->bodyMarkdown;
        $post->body_html = '';
        $post->status = $data->status;
        $post->published_at = $data->publishedAt;
        $post->save();

        // tags
        if ($data->tagIds !== []) {
            $post->tags()->sync($data->tagIds);
        }

        return $post;
    }
}
