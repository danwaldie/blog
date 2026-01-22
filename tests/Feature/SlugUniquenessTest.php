<?php

declare(strict_types=1);

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

it('generates unique slugs for identical titles', function (): void {
    $user = User::factory()->create();

    $a = Post::create([
        'author_id' => $user->id,
        'title' => 'Hello World',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => '# A',
        'body_html' => '',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    $b = Post::create([
        'author_id' => $user->id,
        'title' => 'Hello World',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => '# B',
        'body_html' => '',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    expect($a->slug)->toBe('hello-world');
    expect($b->slug)->toBe('hello-world-2');
});