<?php

namespace Database\Factories;

use App\Models\Post;
use App\Enums\CommentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'commenter_name' => $this->faker->name(),
            'body' => $this->faker->paragraph(),
            'status' => CommentStatus::Published,
            'published_at' => now(),
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CommentStatus::Submitted,
            'published_at' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => CommentStatus::Rejected,
            'published_at' => null,
            'moderation_reject' => true,
        ]);
    }
}
