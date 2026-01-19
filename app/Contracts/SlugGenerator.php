<?php

declare(strict_types=1);

namespace App\Contracts;

interface SlugGenerator
{
    public function generate(string $title): string;
}
