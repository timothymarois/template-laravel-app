<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

it('allows active users to access the application', function () {
    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $this->actingAs($user)->get('/');

    $this->assertAuthenticatedAs($user);
});

it('logs out inactive users and redirects to login', function () {
    $user = User::factory()->inactive()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

it('shows error message when inactive user is logged out', function () {
    $user = User::factory()->inactive()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'Your account has been deactivated.');
});

it('allows guests to access public routes', function () {
    $response = $this->get('/');

    // Should not throw any errors for guests
    $response->assertStatus(200);
});

it('redirects a deactivated user off an auth:sanctum route instead of erroring', function () {
    // Regression: every other case here uses `/`, a session-only route. On a route behind
    // `auth:sanctum`, Authenticate has already called shouldUse('sanctum'), so logging out
    // of the DEFAULT guard reaches Sanctum's RequestGuard — which has no logout() — and the
    // deactivated user gets a 500 rather than being sent to login.
    $user = User::factory()->inactive()->create(['role' => UserRole::SuperAdmin]);

    $response = $this->actingAs($user)->get(route('admin.users.index'));

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error', 'Your account has been deactivated.');
    $this->assertGuest('web');
});

it('deactivated user cannot log back in while deactivated', function () {
    $user = User::factory()->inactive()->create();

    // Simulate login attempt (user is logged in by test, then middleware catches it)
    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
