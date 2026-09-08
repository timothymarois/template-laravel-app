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

    $response = $this->post('/logout');

    $response->assertRedirect('/');

    $this->refreshApplication();

    $this->assertGuest();
});

it('rejects invalid credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('lets a user log in with a password shorter than the current policy', function () {
    // Length policy belongs on the routes that SET a password. A `min:8` rule on
    // login locks out anyone whose password predates the policy, and reports it as
    // a validation error rather than a credentials failure.
    $user = User::factory()->create(['password' => bcrypt('old4')]);

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'old4',
    ])->assertSessionHasNoErrors()->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

it('throttles repeated failed logins', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    foreach (range(1, 5) as $ignored) {
        $this->post('/auth/login', ['email' => $user->email, 'password' => 'wrong']);
    }

    $this->post('/auth/login', ['email' => $user->email, 'password' => 'wrong'])
        ->assertSessionHasErrors('email');

    expect(session()->get('errors')->get('email')[0])->toContain('seconds');
    $this->assertGuest();
});

it('refuses to log in a deactivated user', function () {
    // EnsureUserIsActive only ejects on the NEXT request, which would leave one
    // fully authenticated request in between. The credentials are correct here.
    $user = User::factory()->inactive()->create(['password' => bcrypt('secret123')]);

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('regenerates the session on registration', function () {
    $this->get('/register');
    $before = session()->getId();

    $this->post('/auth/register', [
        'name' => 'Session Fixation',
        'email' => 'fixation@example.com',
        'password' => 'Password-1!',
        'password_confirmation' => 'Password-1!',
    ])->assertRedirect('/');

    expect(session()->getId())->not->toBe($before);
});

it('regenerates the session on login', function () {
    $user = User::factory()->create(['password' => bcrypt('secret123')]);

    $this->get('/login');
    $before = session()->getId();

    $this->post('/auth/login', [
        'email' => $user->email,
        'password' => 'secret123',
    ])->assertRedirect('/');

    expect(session()->getId())->not->toBe($before);
});
