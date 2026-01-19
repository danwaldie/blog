<?php

declare(strict_types=1);

namespace App\Contracts;

interface ExcerptGenerator
{
    public function fromHtml(string $html, int $maxLength = 240): string;
}
