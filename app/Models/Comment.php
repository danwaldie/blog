<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'commenter_name',
        'body',
    ];

    protected $casts = [
        'status' => CommentStatus::class,
        'published_at' => 'immutable_datetime',
        'moderated_at' => 'datetime',
        'moderation_reject' => 'boolean',
        'moderation_confidence' => 'float',
    ];

    /** 
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === CommentStatus::Published;
    }

    /**
     * @param Builder<Comment> $query
     * @return Builder<Comment>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', CommentStatus::Published)
            ->whereNotNull('published_at');
    }
}
