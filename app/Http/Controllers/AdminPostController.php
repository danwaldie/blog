<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Posts\CreatePostAction;
use App\Actions\Posts\DeletePostAction;
use App\Actions\Posts\PublishPostAction;
use App\Actions\Posts\UpdatePostAction;
use App\Data\Posts\PostInputData;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class AdminPostController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::query()
            ->with('tags')
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Admin/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return Inertia::render('Admin/Posts/Create');
    }

    public function store(StorePostRequest $request, CreatePostAction $action): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $data = PostInputData::fromValidated($request->validated());
        $post = $action->execute($request->user(), $data);

        return redirect()->route('admin.posts.edit', $post);
    }

    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        $post->load('tags');

        return Inertia::render('Admin/Posts/Edit', [
            'post' => $post,
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post, UpdatePostAction $action): RedirectResponse
    {
        $this->authorize('update', $post);

        $data = PostInputData::fromValidated($request->validated());
        $action->execute($post, $data);

        return redirect()->route('admin.posts.edit', $post);
    }

    public function publish(Post $post, PublishPostAction $action): RedirectResponse
    {
        $this->authorize('publish', $post);

        $action->execute($post);

        return redirect()->route('admin.posts.edit', $post);
    }

    public function destroy(Post $post, DeletePostAction $action): RedirectResponse
    {
        $this->authorize('delete', $post);

        $action->execute($post);

        return redirect()->route('admin.posts.index');
    }
}
