<?php

declare(strict_types=1);

namespace App\Contracts;

interface MarkdownRenderer
{
    public function toHtml(string $markdown): string;
}
