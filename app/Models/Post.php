<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PostStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Post extends Model
{
    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body_markdown',
        'body_html',
        'status',
        'published_at',
    ];

    protected $casts = [
        'status' => PostStatus::class,
        'published_at' => 'immutable_datetime',
    ];

    /** 
     * @return BelongsTo<User, $this> 
     */
    public function author(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /** 
     * @return BelongsToMany<Tag, $this> 
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }


    public function isPubliclyVisible(?CarbonImmutable $now = null): bool
    {
        $now ??= CarbonImmutable::now();

        if ($this->published_at === null) {
            return false;
        }

        if ($this->status === \App\Enums\PostStatus::Published) {
            return $this->published_at->lte($now);
        }

        if ($this->status === \App\Enums\PostStatus::Scheduled) {
            return $this->published_at->lte($now);
        }

        return false;
    }
}
