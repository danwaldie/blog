<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use Carbon\CarbonImmutable;
use Inertia\Inertia;
use Inertia\Response;

final class PublicPostController extends Controller
{
    public function index(): Response
    {
        $now = CarbonImmutable::now();

        $posts = Post::query()
            ->where('status', [PostStatus::Published, PostStatus::Scheduled])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', $now)
            ->orderByDesc('published_at')
            ->with(['tags', 'author'])
            ->get();

        return Inertia::render('Blog/Index', [
            'posts' => $posts,
        ]);
    }

    public function show(Post $post): Response
    {
        abort_unless($post->isPubliclyVisible(), 404);

        $post->load([
            'tags', 
            'author', 
            'comments' => function ($query) {
                $query->published()->latest();
            }
        ]);

        return Inertia::render('Blog/Show', [
            'post' => $post,
        ]);
    }
}
