<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use Inertia\Inertia;
use Inertia\Response;

final class PublicPostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::query()
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->orderByDesc('published_at')
            ->with('tags')
            ->get();

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->status === PostStatus::Published, 404);

        $post->load('tags', 'author');

        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }
}
