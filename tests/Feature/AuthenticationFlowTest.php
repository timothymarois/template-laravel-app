<?php

declare(strict_types=1);

use App\Models\User;

it('allows user to register', function () {
    $response = $this->post('/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

it('allows user to login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
});

it('allows user to logout', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ]);

    $response = $this->get('/logout');

    $response->assertRedirect('/');

    $this->refreshApplication();

    $this->assertGuest();
});
