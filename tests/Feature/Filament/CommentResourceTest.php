<?php

use App\Models\User;
use App\Models\Comment;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can access the comment resource index as an admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin)
        ->get('/admin/comments')
        ->assertStatus(200);
});

it('cannot access the comment resource index as a guest', function () {
    get('/admin/comments')
        ->assertRedirect('/admin/login');
});
