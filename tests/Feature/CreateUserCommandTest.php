<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| user:create
|--------------------------------------------------------------------------
|
| The supported way to make an operator. /admin is gated on the role, so this
| command is what stands between a fresh deploy and an admin surface nobody can
| reach — the failure modes below are the ones that would leave someone locked
| out, or leave a password somewhere it should not be.
|
*/

it('creates a plain user by default', function () {
    $this->artisan('user:create', [
        '--name' => 'Plain Person',
        '--email' => 'plain@example.com',
        '--password' => 'Str0ng-Password!',
    ])->assertSuccessful();

    $user = User::firstWhere('email', 'plain@example.com');

    expect($user->role)->toBe(UserRole::User)
        ->and($user->isAdmin())->toBeFalse();
});

it('says so when the created user cannot reach the admin area', function () {
    // Silence here is how somebody ends up wondering why /admin 403s.
    $this->artisan('user:create', [
        '--name' => 'Plain', '--email' => 'plain@example.com', '--password' => 'Str0ng-Password!',
    ])->expectsOutputToContain('cannot reach /admin')->assertSuccessful();
});

it('creates an operator with --admin', function () {
    $this->artisan('user:create', [
        '--name' => 'Ops',
        '--email' => 'ops@example.com',
        '--password' => 'Str0ng-Password!',
        '--admin' => true,
    ])->assertSuccessful();

    $user = User::firstWhere('email', 'ops@example.com');

    expect($user->role)->toBe(UserRole::SuperAdmin)
        ->and($user->isAdmin())->toBeTrue();
});

it('hashes the password rather than storing it', function () {
    $this->artisan('user:create', [
        '--name' => 'Ops', '--email' => 'ops@example.com', '--password' => 'Str0ng-Password!',
    ])->assertSuccessful();

    $user = User::firstWhere('email', 'ops@example.com');

    expect($user->password)->not->toBe('Str0ng-Password!')
        ->and(Hash::check('Str0ng-Password!', $user->password))->toBeTrue();
});

it('generates and prints a password when none is given', function () {
    // Generated so a caller never has to put a real password on a command line,
    // where it lands in shell history. Printed because it is the only chance.
    $this->artisan('user:create', ['--name' => 'Ops', '--email' => 'ops@example.com'])
        ->expectsOutputToContain('Generated password')
        ->assertSuccessful();

    expect(User::firstWhere('email', 'ops@example.com'))->not->toBeNull();
});

it('does not announce a password the caller supplied', function () {
    $this->artisan('user:create', [
        '--name' => 'Ops', '--email' => 'ops@example.com', '--password' => 'Str0ng-Password!',
    ])->doesntExpectOutputToContain('Generated password')->assertSuccessful();
});

it('refuses a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->artisan('user:create', [
        '--name' => 'Second', '--email' => 'taken@example.com', '--password' => 'Str0ng-Password!',
    ])->assertFailed();

    expect(User::where('email', 'taken@example.com')->count())->toBe(1);
});

it('refuses a malformed email', function () {
    $this->artisan('user:create', [
        '--name' => 'Ops', '--email' => 'not-an-email', '--password' => 'Str0ng-Password!',
    ])->assertFailed();

    $this->assertDatabaseCount('users', 0);
});

it('refuses a password below the shared policy', function () {
    // Password::defaults() — the same rule registration and reset use, so the
    // console cannot create an account the web forms would have rejected.
    $this->artisan('user:create', [
        '--name' => 'Ops', '--email' => 'ops@example.com', '--password' => 'short',
    ])->assertFailed();

    $this->assertDatabaseCount('users', 0);
});

it('prompts for anything not passed as an option', function () {
    // The interactive path exists so the command is usable without arguments;
    // an empty --name falls through to the prompt rather than failing.
    $this->artisan('user:create', ['--password' => 'Str0ng-Password!', '--admin' => true])
        ->expectsQuestion('Name', 'Prompted Operator')
        ->expectsQuestion('Email address', 'prompted@example.com')
        ->assertSuccessful();

    $user = User::firstWhere('email', 'prompted@example.com');

    expect($user->name)->toBe('Prompted Operator')
        ->and($user->isAdmin())->toBeTrue();
});
