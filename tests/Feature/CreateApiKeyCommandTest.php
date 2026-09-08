<?php

declare(strict_types=1);

use App\Enums\ApiAbility;
use App\Models\User;
use App\Services\ApiKeyService;
use Laravel\Sanctum\PersonalAccessToken;

/*
|--------------------------------------------------------------------------
| api-key:create
|--------------------------------------------------------------------------
|
| For the cases with no browser — seeding, CI, a deploy script. The refusals
| matter most: a key issued to the wrong owner, with an unenforced ability, or
| with no expiry nobody asked for, is a credential that outlives its purpose.
|
*/

function makeOwner(array $attributes = []): User
{
    return User::factory()->admin()->create(array_merge(['email' => 'ops@example.com'], $attributes));
}

it('issues a key with the requested abilities and lifetime', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', [
        '--user' => $owner->email,
        '--name' => 'CI pipeline',
        '--ability' => [ApiAbility::Read->value, ApiAbility::Write->value],
        '--days' => 30,
    ])->assertSuccessful();

    $key = PersonalAccessToken::firstWhere('name', 'CI pipeline');

    expect($key->tokenable_id)->toBe($owner->id)
        ->and($key->abilities)->toEqualCanonicalizing([ApiAbility::Read->value, ApiAbility::Write->value])
        ->and($key->expires_at)->not->toBeNull()
        ->and($key->expires_at->isBetween(now()->addDays(29), now()->addDays(31)))->toBeTrue();
});

it('defaults to a read-only key with the default lifetime', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', ['--user' => $owner->email, '--name' => 'Default'])
        ->assertSuccessful();

    $key = PersonalAccessToken::firstWhere('name', 'Default');

    expect($key->abilities)->toBe([ApiAbility::Read->value])
        ->and($key->expires_at)->not->toBeNull();
});

it('issues a non-expiring key only when explicitly asked', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', [
        '--user' => $owner->email, '--name' => 'Forever', '--never-expires' => true,
    ])->assertSuccessful();

    expect(PersonalAccessToken::firstWhere('name', 'Forever')->expires_at)->toBeNull();
});

it('prints the plaintext once, and stores only its hash', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', ['--user' => $owner->email, '--name' => 'Printed'])
        ->expectsOutputToContain('shown once')
        ->assertSuccessful();

    $key = PersonalAccessToken::firstWhere('name', 'Printed');

    // 64 hex characters — a SHA-256 digest, not anything resembling the key.
    expect($key->token)->toHaveLength(64)
        ->and($key->token)->toMatch('/^[0-9a-f]{64}$/');
});

it('issues a key that actually authenticates', function () {
    // The property that matters: a key from the console is not a second-class
    // credential. Proves the command, the service and the guard agree.
    $owner = makeOwner();
    $issued = app(ApiKeyService::class)->issue($owner, 'Probe', [ApiAbility::Read->value]);

    $this->withHeader('Authorization', "Bearer {$issued->plainText}")
        ->getJson('/api/user')
        ->assertOk()
        ->assertJson(['email' => $owner->email]);
});

it('refuses an unknown owner', function () {
    $this->artisan('api-key:create', ['--user' => 'nobody@example.com', '--name' => 'X'])
        ->expectsOutputToContain('No user with the email')
        ->assertFailed();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('refuses a deactivated owner', function () {
    // EnsureApiKey rejects a key whose owner is inactive, so issuing one would
    // hand over a credential that cannot authenticate.
    $owner = makeOwner(['is_active' => false]);

    $this->artisan('api-key:create', ['--user' => $owner->email, '--name' => 'X'])
        ->expectsOutputToContain('deactivated')
        ->assertFailed();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('refuses an ability outside the enum and lists the valid ones', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', [
        '--user' => $owner->email, '--name' => 'X', '--ability' => ['admin:everything'],
    ])->expectsOutputToContain('Valid abilities')->assertFailed();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('refuses a lifetime outside the allowed range', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', ['--user' => $owner->email, '--name' => 'X', '--days' => 9999])
        ->assertFailed();

    $this->artisan('api-key:create', ['--user' => $owner->email, '--name' => 'X', '--days' => -1])
        ->assertFailed();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('prompts for the owner when not passed', function () {
    $owner = makeOwner();

    $this->artisan('api-key:create', ['--name' => 'Prompted'])
        ->expectsQuestion('User email', $owner->email)
        ->assertSuccessful();

    expect(PersonalAccessToken::firstWhere('name', 'Prompted'))->not->toBeNull();
});
