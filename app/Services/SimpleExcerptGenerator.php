<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ExcerptGenerator;

final class SimpleExcerptGenerator implements ExcerptGenerator
{
    public function fromHtml(string $html, int $maxLength = 240): string
    {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text) ?? '';
        $text = trim($text);

        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = mb_substr($text, 0, $maxLength);
        return rtrim($truncated) . '…';
    }
}
