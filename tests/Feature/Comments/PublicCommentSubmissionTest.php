<?php

use App\Models\Post;
use App\Models\Comment;
use App\Enums\CommentStatus;
use App\Jobs\ModerateCommentJob;
use Illuminate\Support\Facades\Queue;

it('allows a user to submit a comment', function () {
    Queue::fake();
    $post = Post::factory()->create();

    $response = $this->post("/posts/{$post->slug}/comments", [
        'commenter_name' => 'John Doe',
        'body' => 'This is a great post!',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'post_id' => $post->id,
        'commenter_name' => 'John Doe',
        'body' => 'This is a great post!',
        'status' => CommentStatus::Submitted->value,
    ]);

    Queue::assertPushed(ModerateCommentJob::class);
});

it('validates comment submission', function ($data, $errors) {
    $post = Post::factory()->create();

    $response = $this->post("/posts/{$post->slug}/comments", $data);

    $response->assertSessionHasErrors($errors);
    $this->assertDatabaseCount('comments', 0);
})->with([
            'missing name' => [['body' => 'Some body'], ['commenter_name']],
            'missing body' => [['commenter_name' => 'John'], ['body']],
            'name too long' => [['commenter_name' => str_repeat('a', 51), 'body' => 'Body'], ['commenter_name']],
            'body too long' => [['commenter_name' => 'John', 'body' => str_repeat('a', 2001)], ['body']],
        ]);

it('only shows published comments on post page', function () {
    $post = Post::factory()->create();

    $publishedComment = Comment::factory()->create([
        'post_id' => $post->id,
        'body' => 'Visible Comment',
        'status' => CommentStatus::Published,
    ]);

    $submittedComment = Comment::factory()->submitted()->create([
        'post_id' => $post->id,
        'body' => 'Hidden Comment',
    ]);

    $rejectedComment = Comment::factory()->rejected()->create([
        'post_id' => $post->id,
        'body' => 'Bad Comment',
    ]);

    $response = $this->get("/posts/{$post->slug}");

    $response->assertStatus(200);
    $response->assertSee('Visible Comment');
    $response->assertDontSee('Hidden Comment');
    $response->assertDontSee('Bad Comment');
});
