<?php

declare(strict_types=1);

use App\Models\User;

it('shows public home page', function () {
    $this->withoutVite();

    $response = $this->get('/');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Index'));
});

it('shows login page to guests', function () {
    $this->withoutVite();

    $response = $this->get('/login');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Login'));
});

it('shows register page to guests', function () {
    $this->withoutVite();

    $response = $this->get('/register');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('Register'));
});

it('redirects authenticated users from login page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect('/');
});

it('redirects authenticated users from register page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/register');

    $response->assertRedirect('/');
});

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
