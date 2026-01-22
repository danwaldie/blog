<?php

declare(strict_types=1);

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Carbon\CarbonImmutable;

function makePost(array $overrides = []): Post {
    $user = $overrides['author'] ?? User::factory()->create();

    $defaults = [
        'author_id' => $user->id,
        'title' => 'My First Post',
        'slug' => '',
        'excerpt' => null,
        'body_markdown' => '# Hello',
        'body_html' => '',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ];

    unset($overrides['author']);

    return Post::create(array_merge($defaults, $overrides));
}

it('shows published posts on the public index', function (): void {
    $past = CarbonImmutable::now()->subDay();

    $published = makePost([
        'title' => 'Published',
        'status' => PostStatus::Published,
        'published_at' => $past,
    ]);

    $draft = makePost([
        'title' => 'Draft',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Published')
        ->assertDontSee('Draft');
});

it('returns 404 for draft posts', function (): void {
    $draft = makePost([
        'title' => 'Draft',
        'status' => PostStatus::Draft,
        'published_at' => null,
    ]);

    $this->get("/posts/{$draft->slug}")
        ->assertNotFound();
});

it('returns 404 for scheduled posts in the future', function (): void {
    $future = CarbonImmutable::now()->addDay();

    $scheduled = makePost([
        'title' => 'Future Scheduled',
        'status' => PostStatus::Scheduled,
        'published_at' => $future,
    ]);

    $this->get("/posts/{$scheduled->slug}")
        ->assertNotFound();
});

it('shows scheduled posts once the scheduled time has passed', function (): void {
    $past = CarbonImmutable::now()->subMinute();

    $scheduled = makePost([
        'title' => 'Past Scheduled',
        'status' => PostStatus::Scheduled,
        'published_at' => $past,
    ]);

    $this->get("/posts/{$scheduled->slug}")
        ->assertOk()
        ->assertSee('Past Scheduled');
});

it('orders the public index by published_at desc', function (): void {
    $older = CarbonImmutable::now()->subDays(2);
    $newer = CarbonImmutable::now()->subHour();

    makePost([
        'title' => 'Older',
        'status' => PostStatus::Published,
        'published_at' => $older,
    ]);

    makePost([
        'title' => 'Newer',
        'status' => PostStatus::Published,
        'published_at' => $newer,
    ]);

    $response = $this->get('/')->assertOk();

    // crude but effective ordering check in the rendered HTML
    $content = $response->getContent();
    expect(strpos($content, 'Newer'))->toBeLessThan(strpos($content, 'Older'));
});