<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\ExcerptGenerator;
use App\Contracts\MarkdownRenderer;
use App\Contracts\SlugGenerator;
use App\Models\Post;
use App\Observers\PostObserver;
use App\Services\CommonMarkMarkdownRenderer;
use App\Services\SimpleExcerptGenerator;
use App\Services\UniqueSlugGenerator;
use Illuminate\Support\ServiceProvider;

final class BlogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SlugGenerator::class, UniqueSlugGenerator::class);
        $this->app->bind(MarkdownRenderer::class, CommonMarkMarkdownRenderer::class);
        $this->app->bind(ExcerptGenerator::class, SimpleExcerptGenerator::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Post::observe(PostObserver::class);
    }
}
