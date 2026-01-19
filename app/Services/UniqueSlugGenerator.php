<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SlugGenerator;
use App\Models\Post;
use Illuminate\Support\Str;

final class UniqueSlugGenerator implements SlugGenerator
{
    public function generate(string $title): string
    {
        $base = Str::slug($title);
        $base = $base !== '' ? $base : 'post';

        $slug = $base;
        $i = 2;

        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }
}
