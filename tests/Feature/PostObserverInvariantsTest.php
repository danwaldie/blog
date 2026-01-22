<?php

declare(strict_types=1);

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Carbon\CarbonImmutable;

it('sets published_at when status becomes Published', function (): void {
    $user = User::factory()->create();

    $post = Post::create([
        'author_id' => $user->id,
        'title' => 'Publish Me',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => '# Hello',
        'body_html' => '',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    expect($post->published_at)->toBeNull();

    $post->status = PostStatus::Published;
    $post->save();

    $post->refresh();

    expect($post->published_at)->not->toBeNull();
    expect($post->published_at->lte(CarbonImmutable::now()))->toBeTrue();
});

it('clears published_at when status becomes Draft', function (): void {
    $user = User::factory()->create();

    $post = Post::create([
        'author_id' => $user->id,
        'title' => 'Unpublish Me',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => '# Hello',
        'body_html' => '',
        'status' => PostStatus::Published,
        'published_at' => CarbonImmutable::now()->subHour(),
    ]);

    expect($post->published_at)->not->toBeNull();

    $post->status = PostStatus::Draft;
    $post->save();

    $post->refresh();

    expect($post->published_at)->toBeNull();
});