<?php

declare(strict_types=1);

use App\Models\User;

it('redirects guests to the admin login page', function (): void {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});

it('denies non-admin users from accessing the admin panel', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get('/admin');

    // Filament may redirect back to login OR return forbidden depending on config.
    expect(in_array($response->getStatusCode(), [302, 403], true))->toBeTrue();
});

it('allows admin users to access the admin panel', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)->get('/admin')
        ->assertOk();
});