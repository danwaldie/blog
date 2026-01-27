<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Comments\CreateComment;
use App\Data\Comments\CommentInputData;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

final class CommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(StoreCommentRequest $request, Post $post, CreateComment $action): RedirectResponse
    {
        $commentData = CommentInputData::from($request->validated());
        $action->handle($post, $commentData);

        return redirect()->back();
    }
}
