<?php

declare(strict_types=1);

namespace App\Data\Posts;

use App\Enums\PostStatus;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Data;

/**
 * Request DTO (validated in FormRequest)
 */
final class PostInputData extends Data
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        public string $title,
        public ?string $slug,
        public ?string $excerpt,
        public string $bodyMarkdown,
        public PostStatus $status,
        public ?CarbonImmutable $publishedAt,
        public array $tagIds = [],
    ) {}

    /**
     * @param array<string,mixed> $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            title: (string) $validated['title'],
            slug: isset($validated['slug']) && $validated['slug'] !== '' ? (string) $validated['slug'] : null,
            excerpt: isset($validated['excerpt']) && $validated['excerpt'] !== '' ? (string) $validated['excerpt'] : null,
            bodyMarkdown: (string) $validated['body_markdown'],
            status: PostStatus::from((string) $validated['status']),
            publishedAt: isset($validated['published_at']) ? CarbonImmutable::parse((string) $validated['published_at']) : null,
            tagIds: array_map('intval', $validated['tag_ids'] ?? []),
        );
    }
}
