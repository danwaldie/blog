<?php

declare(strict_types=1);

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

it('generates slug, html, and excerpt when creating a post', function (): void {
    $user = User::factory()->create();

    $post = Post::create([
        'author_id' => $user->id,
        'title' => 'Hello World',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => "# Hi\n\nThis is **markdown**.",
        'body_html' => '',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    expect($post->slug)->toBe('hello-world');
    expect($post->body_html)->toContain('<h1>Hi</h1>');
    expect($post->excerpt)->not->toBeNull();
    expect($post->excerpt)->toContain('This is markdown');
});

it('bulk updates bypass model events (observer does not run)', function (): void {
    $user = User::factory()->create();

    $post = Post::create([
        'author_id' => $user->id,
        'title' => 'Bulk Update Demo',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => '# Old',
        'body_html' => '',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    $originalHtml = $post->body_html;
    $originalExcerpt = $post->excerpt;

    Post::query()->whereKey($post->id)->update([
        'body_markdown' => '# New',
    ]);

    $post->refresh();

    expect($post->body_html)->toBe($originalHtml);
    expect($post->excerpt)->toBe($originalExcerpt);
});