<?php

declare(strict_types=1);

namespace App\Observers;

use App\Contracts\ExcerptGenerator;
use App\Contracts\MarkdownRenderer;
use App\Contracts\SlugGenerator;
use App\Enums\PostStatus;
use App\Models\Post;
use Carbon\CarbonImmutable;

final class PostObserver
{
    public function __construct(
        private readonly SlugGenerator $slugGenerator,
        private readonly MarkdownRenderer $markdownRenderer,
        private readonly ExcerptGenerator $excerptGenerator,
    ) {}

        public function creating(Post $post): void
    {
        if ($post->slug === null || $post->slug === '') {
            $post->slug = $this->slugGenerator->generate($post->title);
        }

        // Default to draft if unset
        if ($post->status === null) {
            $post->status = PostStatus::Draft;
        }
    }

    public function saving(Post $post): void
    {
        // Keep HTML in sync with markdown when markdown changes
        if ($post->isDirty('body_markdown')) {
            $post->body_html = $this->markdownRenderer->toHtml($post->body_markdown);
        }

        // Auto-generate excerpt only when excerpt is null/empty
        if (($post->excerpt === null || trim($post->excerpt) === '') && $post->body_html !== '') {
            $post->excerpt = $this->excerptGenerator->fromHtml($post->body_html);
        }

        // Enforce published_at invariant
        if ($post->status === PostStatus::Published) {
            if ($post->published_at === null) {
                $post->published_at = CarbonImmutable::now();
            }
        }

        // If draft, ensure published_at is null (avoid confusion)
        if ($post->status === PostStatus::Draft) {
            $post->published_at = null;
        }

        // For Scheduled, leave published_at as-is (FormRequest will validate it later)
    }
}
