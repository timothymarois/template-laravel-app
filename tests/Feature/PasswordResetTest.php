<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

/*
|--------------------------------------------------------------------------
| Forgot / reset password
|--------------------------------------------------------------------------
|
| The enumeration guards below are the point of this file: neither endpoint may
| reveal whether an address is registered. Breeze's stock controllers do reveal
| it; ours deliberately do not, so those cases are regression tests, not
| decoration.
|
*/

/**
 * Requests a reset link and returns the token from the notification, so each case
 * can act on it in the open. Asserting inside a truth-test closure hides both the
 * causal value and whether the assertions ran at all.
 */
function requestResetToken(User $user): string
{
    $token = null;

    test()->post('/auth/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
        $token = $notification->token;

        return true;
    });

    expect($token)->not->toBeNull();

    return $token;
}

it('shows the forgot-password page to guests', function () {
    $this->get('/forgot-password')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('ForgotPassword'));
});

it('redirects authenticated users away from the forgot-password page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/forgot-password')
        ->assertRedirect('/');
});

it('emails a reset link for a registered address', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/auth/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status');

    Notification::assertSentTo($user, ResetPassword::class);
    $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
});

it('does not reveal that an address is unregistered', function () {
    Notification::fake();

    $this->post('/auth/forgot-password', ['email' => 'nobody@example.com'])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', trans('passwords.sent'));

    Notification::assertNothingSent();
});

it('does not reveal a throttled resend', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/auth/forgot-password', ['email' => $user->email]);
    $this->post('/auth/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors()
        ->assertSessionHas('status', trans('passwords.sent'));

    Notification::assertSentToTimes($user, ResetPassword::class, 1);
});

it('shows the reset page with the token and email', function () {
    $user = User::factory()->create();

    $this->get('/reset-password/some-token?email='.urlencode($user->email))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('ResetPassword')
            ->where('token', 'some-token')
            ->where('email', $user->email));
});

it('resets the password with a valid token', function () {
    Notification::fake();
    $user = User::factory()->create();
    $token = requestResetToken($user);

    $this->post('/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ])->assertRedirect(route('login'))->assertSessionHas('status');

    expect(Hash::check('New-Password-1!', $user->fresh()->password))->toBeTrue();
});

it('does not log the user in after a reset', function () {
    Notification::fake();
    $user = User::factory()->create();
    $token = requestResetToken($user);

    $this->post('/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ]);

    $this->assertGuest();

    // ...and the new password does work at the login form.
    $this->post('/auth/login', ['email' => $user->email, 'password' => 'New-Password-1!'])
        ->assertRedirect('/');
    $this->assertAuthenticatedAs($user->fresh());
});

it('consumes the reset token', function () {
    Notification::fake();
    $user = User::factory()->create();
    $token = requestResetToken($user);

    $payload = [
        'token' => $token,
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ];

    $this->post('/auth/reset-password', $payload);
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

    // Replaying the same token must now fail.
    $this->post('/auth/reset-password', $payload)->assertSessionHasErrors('email');
});

it('rotates the remember token on reset', function () {
    Notification::fake();
    $user = User::factory()->create(['remember_token' => 'original-value']);
    $token = requestResetToken($user);

    $this->post('/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ]);

    expect($user->fresh()->remember_token)->not->toBe('original-value');
});

it('rejects an invalid token', function () {
    $user = User::factory()->create();

    $this->post('/auth/reset-password', [
        'token' => 'not-a-real-token',
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ])->assertSessionHasErrors('email');

    expect(Hash::check('New-Password-1!', $user->fresh()->password))->toBeFalse();
});

it('rejects an expired token', function () {
    Notification::fake();
    $user = User::factory()->create();
    $token = requestResetToken($user);

    // config/auth.php expires reset tokens after 60 minutes.
    $this->travel(61)->minutes();

    $this->post('/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ])->assertSessionHasErrors('email');

    expect(Hash::check('New-Password-1!', $user->fresh()->password))->toBeFalse();
});

it('reports an unknown address and a bad token identically', function () {
    $user = User::factory()->create();

    $unknown = $this->post('/auth/reset-password', [
        'token' => 'whatever',
        'email' => 'nobody@example.com',
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ])->assertSessionHasErrors('email');

    $badToken = $this->post('/auth/reset-password', [
        'token' => 'whatever',
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ])->assertSessionHasErrors('email');

    expect($unknown->getSession()->get('errors')->get('email'))
        ->toBe($badToken->getSession()->get('errors')->get('email'))
        ->toBe([trans('passwords.token')]);
});

it('requires the password confirmation to match', function () {
    $user = User::factory()->create();

    $this->post('/auth/reset-password', [
        'token' => 'whatever',
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'Different-Password-1!',
    ])->assertSessionHasErrors('password');
});

it('enforces the shared password policy on reset', function () {
    $user = User::factory()->create();

    $this->post('/auth/reset-password', [
        'token' => 'whatever',
        'email' => $user->email,
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertSessionHasErrors('password');
});

it('throttles repeated reset-link requests from one IP', function () {
    Notification::fake();

    // Distinct addresses, to sidestep the broker's own 60s per-user throttle and
    // isolate the throttle:auth limiter, which keys on the limiter name + IP.
    foreach (range(1, 5) as $i) {
        $this->post('/auth/forgot-password', ['email' => "user{$i}@example.com"]);
    }

    $this->post('/auth/forgot-password', ['email' => 'user6@example.com'])
        ->assertStatus(429);
});

it('shares the flash status with the login page', function () {
    Notification::fake();
    $user = User::factory()->create();
    $token = requestResetToken($user);

    $this->post('/auth/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'New-Password-1!',
        'password_confirmation' => 'New-Password-1!',
    ]);

    // Guards the `flash` share added to HandleInertiaRequests: without it the
    // broker's status string reaches the session and is dropped by the UI.
    $this->followingRedirects()
        ->get(route('login'))
        ->assertInertia(fn ($page) => $page->component('Login')->has('flash.status'));
});
