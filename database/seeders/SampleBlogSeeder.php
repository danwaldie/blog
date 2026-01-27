<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class SampleBlogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstWhere('email', 'admin@example.com');

        if (!$admin) {
            $this->command->warn('Admin user not found. Please run AdminUserSeeder first.');
            return;
        }

        // 1. Create Tags
        $tags = collect(['Science', 'Nature', 'Space', 'Biology', 'Physics', 'History', 'Funny'])
            ->map(fn($name) => Tag::firstOrCreate([
                'name' => $name,
                'slug' => Str::slug($name),
            ]));

        // 2. Create Sample Posts
        $postsData = [
            [
                'title' => 'Why Wombats Have Cube-Shaped Poop',
                'excerpt' => 'The geometrical mystery of Australian wildlife that nobody asked for, but nature delivered anyway.',
                'body_markdown' => "Nature is a majestic architect, but sometimes it's just a prankster with a weird sense of humor. Case in point: the Wombat. These sturdy Australian marsupials are the only creatures on Earth that produce cube-shaped poop. 

Why? Is it for stacking? Is it a primitive form of Minecraft? Scientists actually spent years investigating this. It turns out, their intestines have uneven stretches of elasticity and stiffness. While our intestines are round like a normal person's plumber would expect, the wombat's last section of the gut stretches more on the sides than the corners.

The result? Biological dice. They use these blocks to mark territory without them rolling away. It's literally tactical stacking. Evolutionary perfection, or nature just showing off its geometry skills?",
                'body_html' => "<p>Nature is a majestic architect, but sometimes it's just a prankster with a weird sense of humor. Case in point: the Wombat. These sturdy Australian marsupials are the only creatures on Earth that produce cube-shaped poop.</p><p>Why? Is it for stacking? Is it a primitive form of Minecraft? Scientists actually spent years investigating this. It turns out, their intestines have uneven stretches of elasticity and stiffness. While our intestines are round like a normal person's plumber would expect, the wombat's last section of the gut stretches more on the sides than the corners.</p><p>The result? Biological dice. They use these blocks to mark territory without them rolling away. It's literally tactical stacking. Evolutionary perfection, or nature just showing off its geometry skills?</p>",
                'status' => PostStatus::Published,
                'published_at' => CarbonImmutable::now()->subDays(1),
                'tags' => ['Nature', 'Biology', 'Funny'],
                'comments' => [
                    ['name' => 'Steve B.', 'body' => 'I tried to stack my own to prove a point. 0/10 would not recommend.'],
                    ['name' => 'NatureLover42', 'body' => 'The ultimate Lego set.'],
                ]
            ],
            [
                'title' => 'The Great Emu War: Australia’s Most Humiliating Defeat',
                'excerpt' => 'That one time the military fought birds with machine guns and lost.',
                'body_markdown' => "If you think history is just dates and boring treaties, let me introduce you to the Great Emu War of 1932. Australia, a land known for everything trying to kill you, decided to wage war on... giant flightless birds.

The veterans were given machine guns. They had thousands of rounds of ammunition. They had trucks. The Emus, on the other hand, had long legs and a complete lack of military tact. 

The result? The Emus basically used guerrilla warfare. They split into small groups, zig-zagged, and outran the trucks. One commander noted that the Emus could take several bullets and just keep running like feathered tanks. The military withdrew in shame. The Emus? They just went back to eating crops. 1 - 0 to the birds.",
                'body_html' => "<p>If you think history is just dates and boring treaties, let me introduce you to the Great Emu War of 1932. Australia, a land known for everything trying to kill you, decided to wage war on... giant flightless birds.</p><p>The veterans were given machine guns. They had thousands of rounds of ammunition. They had trucks. The Emus, on the other hand, had long legs and a complete lack of military tact.</p><p>The result? The Emus basically used guerrilla warfare. They split into small groups, zig-zagged, and outran the trucks. One commander noted that the Emus could take several bullets and just keep running like feathered tanks. The military withdrew in shame. The Emus? They just went back to eating crops. 1 - 0 to the birds.</p>",
                'status' => PostStatus::Published,
                'published_at' => CarbonImmutable::now()->subDays(3),
                'tags' => ['History', 'Funny', 'Nature'],
                'comments' => [
                    ['name' => 'HistoryBuff', 'body' => 'Never fight a bird that can run 30mph and has a blank stare.'],
                ]
            ],
            [
                'title' => 'Space: It Smells Like Burnt Steak and Shame',
                'excerpt' => 'Astronauts report that the vacuum of space isn’t just empty; it’s pungent.',
                'body_markdown' => "You'd think space would smell like nothing, or maybe cold electricity. But astronauts returning from space walks have consistently reported a very specific aroma lingering on their suits: burnt steak, hot metal, and welding fumes.

So, the universe is basically a cosmic BBQ joint that forgot to turn the fan on. High-energy vibrations in particles returning to the airlock react with the oxygen inside, creating this meaty bouquet. 

Imagine floating in the infinite void, contemplating the meaning of existence, and all you can think about is a T-bone steak. It’s hard to be deep when the cosmos smells like a Tuesday night at the Outback Steakhouse.",
                'body_html' => "<p>You'd think space would smell like nothing, or maybe cold electricity. But astronauts returning from space walks have consistently reported a very specific aroma lingering on their suits: burnt steak, hot metal, and welding fumes.</p><p>So, the universe is basically a cosmic BBQ joint that forgot to turn the fan on. High-energy vibrations in particles returning to the airlock react with the oxygen inside, creating this meaty bouquet.</p><p>Imagine floating in the infinite void, contemplating the meaning of existence, and all you can think about is a T-bone steak. It’s hard to be deep when the cosmos smells like a Tuesday night at the Outback Steakhouse.</p>",
                'status' => PostStatus::Published,
                'published_at' => CarbonImmutable::now()->subDays(7),
                'tags' => ['Space', 'Science', 'Funny'],
                'comments' => [
                    ['name' => 'StarGazer', 'body' => 'Checking for space BBQ on my telescope tonight.'],
                    ['name' => 'Elon Maybe', 'body' => 'I plan to send A1 sauce to Mars by 2030.'],
                ]
            ],
        ];

        foreach ($postsData as $data) {
            $post = Post::create([
                'author_id' => $admin->id,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'excerpt' => $data['excerpt'],
                'body_markdown' => $data['body_markdown'],
                'body_html' => $data['body_html'],
                'status' => $data['status'],
                'published_at' => $data['published_at'],
            ]);

            // Attach Tags
            $tagIds = $tags->whereIn('name', $data['tags'])->pluck('id');
            $post->tags()->attach($tagIds);

            // Create Comments
            foreach ($data['comments'] as $commentData) {
                Comment::create([
                    'post_id' => $post->id,
                    'commenter_name' => $commentData['name'],
                    'body' => $commentData['body'],
                    'status' => CommentStatus::Published,
                    'published_at' => CarbonImmutable::now()->subHours(rand(1, 48)),
                ]);
            }
        }
    }
}
