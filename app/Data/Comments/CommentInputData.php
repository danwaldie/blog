<?php

declare(strict_types=1);

namespace App\Data\Comments;

use App\Enums\CommentStatus;
use Spatie\LaravelData\Data;

/**
 * Request DTO (validated in FormRequest)
 */
final class CommentInputData extends Data
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $commenter_name,
        public string $body,
    ) {}

        /**
     * @param array<string,mixed> $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            commenter_name: (string) $validated['commenter_name'],
            body: (string) $validated['body'],
        );
    }
}
