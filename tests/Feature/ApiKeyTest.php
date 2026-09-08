<?php

declare(strict_types=1);

use App\Enums\ApiAbility;
use App\Models\User;
use App\Services\ApiKeyService;
use Laravel\Sanctum\PersonalAccessToken;

/*
|--------------------------------------------------------------------------
| API keys
|--------------------------------------------------------------------------
|
| The cases that matter here are the refusals. An API key system that issues
| and lists correctly but authenticates a browser session, a revoked key or a
| deactivated owner is worse than none, because it reads as a boundary.
|
*/

function issueKey(User $owner, array $abilities = [ApiAbility::Read->value], ?int $days = null): string
{
    return app(ApiKeyService::class)->issue($owner, 'test key', $abilities, $days)->plainText;
}

it('stores only a hash, and returns the plaintext once', function () {
    $user = User::factory()->admin()->create();

    $issued = app(ApiKeyService::class)->issue($user, 'ci', [ApiAbility::Read->value]);

    // Sanctum's plaintext is "{id}|{prefix}{random}{crc}" — the configured prefix
    // sits on the random half, which is the part a secret scanner matches.
    [$id, $secret] = explode('|', $issued->plainText, 2);
    expect($id)->toBe((string) $issued->key->getKey())
        ->and($secret)->toStartWith(config('sanctum.token_prefix'));
    $this->assertDatabaseMissing('personal_access_tokens', ['token' => $issued->plainText]);
    $this->assertDatabaseHas('personal_access_tokens', [
        'id' => $issued->key->getKey(),
        'token' => hash('sha256', $secret),
    ]);
});

it('gives every key an expiry', function () {
    $user = User::factory()->admin()->create();

    $issued = app(ApiKeyService::class)->issue($user, 'ci', [ApiAbility::Read->value]);

    expect($issued->key->expires_at)->not->toBeNull()
        ->and($issued->key->expires_at->diffInDays(now()))
        ->toBeLessThanOrEqual(ApiKeyService::DEFAULT_LIFETIME_DAYS);
});

it('issues a non-expiring key only when explicitly asked', function () {
    $user = User::factory()->admin()->create();

    $issued = app(ApiKeyService::class)->issue(
        $user, 'permanent', [ApiAbility::Read->value], ApiKeyService::NEVER_EXPIRES
    );

    expect($issued->key->expires_at)->toBeNull();

    // ...and it still authenticates far in the future.
    $this->travel(5)->years();
    $this->withHeader('Authorization', "Bearer {$issued->plainText}")
        ->getJson('/api/user')
        ->assertOk();
});

it('does not give a non-expiring key by omission', function () {
    // The dangerous default. Omitting the lifetime must mean "the default", never
    // "forever" — a permanent credential has to be asked for by name.
    $user = User::factory()->admin()->create();

    expect(app(ApiKeyService::class)->issue($user, 'a', [ApiAbility::Read->value])->key->expires_at)
        ->not->toBeNull()
        ->and(app(ApiKeyService::class)->issue($user, 'b', [ApiAbility::Read->value], null)->key->expires_at)
        ->not->toBeNull();
});

it('creates a never-expiring key from the admin screen', function () {
    $user = User::factory()->admin()->create();
    $this->withoutVite();

    $this->actingAs($user)->post(route('admin.api-keys.store'), [
        'name' => 'Permanent',
        'abilities' => [ApiAbility::Read->value],
        'lifetime_days' => ApiKeyService::NEVER_EXPIRES,
    ])->assertOk();

    // Guards the controller: `?: null` on the lifetime would coerce 0 back to the
    // default and silently issue an expiring key to somebody who asked for a
    // permanent one.
    expect(PersonalAccessToken::firstWhere('name', 'Permanent')->expires_at)->toBeNull();
});

it('refuses a negative lifetime', function () {
    $user = User::factory()->admin()->create();

    expect(fn () => app(ApiKeyService::class)->issue($user, 'ci', [ApiAbility::Read->value], -1))
        ->toThrow(InvalidArgumentException::class);
});

it('refuses an empty abilities list', function () {
    $user = User::factory()->admin()->create();

    expect(fn () => app(ApiKeyService::class)->issue($user, 'ci', []))
        ->toThrow(InvalidArgumentException::class);
});

it('deduplicates repeated abilities', function () {
    $user = User::factory()->admin()->create();

    $issued = app(ApiKeyService::class)->issue($user, 'ci', [
        ApiAbility::Read->value,
        ApiAbility::Read->value,
        ApiAbility::Write->value,
    ]);

    expect($issued->key->abilities)->toEqualCanonicalizing([
        ApiAbility::Read->value,
        ApiAbility::Write->value,
    ]);
});

it('never issues a wildcard ability', function () {
    // Sanctum's own createToken() defaults to ['*'], which grants every ability.
    // Nothing in this template may reach that default.
    $user = User::factory()->admin()->create();

    $issued = app(ApiKeyService::class)->issue($user, 'ci', [ApiAbility::Read->value]);

    expect($issued->key->abilities)->not->toContain('*');

    $this->actingAs($user)->post(route('admin.api-keys.store'), [
        'name' => 'Wild',
        'abilities' => ['*'],
    ])->assertSessionHasErrors('abilities.0');
});

it('refuses a malformed or absent bearer token', function () {
    foreach (['', 'garbage', 'apik_nope', '999|apik_nope'] as $bearer) {
        $this->withHeader('Authorization', "Bearer {$bearer}")
            ->getJson('/api/user')
            ->assertUnauthorized();
    }

    $this->getJson('/api/user')->assertUnauthorized();
});

it('does not accept a key id without its secret', function () {
    $user = User::factory()->admin()->create();
    issueKey($user);
    $id = PersonalAccessToken::first()->getKey();

    $this->withHeader('Authorization', "Bearer {$id}")
        ->getJson('/api/user')
        ->assertUnauthorized();
});

it('records last_used_at when a key authenticates', function () {
    $user = User::factory()->admin()->create();
    $key = issueKey($user);

    expect(PersonalAccessToken::first()->last_used_at)->toBeNull();

    $this->withHeader('Authorization', "Bearer {$key}")->getJson('/api/user')->assertOk();

    expect(PersonalAccessToken::first()->last_used_at)->not->toBeNull();
});

it('revokes every key for an owner without touching another owner', function () {
    $owner = User::factory()->admin()->create();
    $other = User::factory()->admin()->create();
    issueKey($owner);
    issueKey($owner);
    issueKey($other);

    expect(app(ApiKeyService::class)->revokeAll($owner))->toBe(2);
    $this->assertDatabaseCount('personal_access_tokens', 1);
    expect(PersonalAccessToken::first()->tokenable_id)->toBe($other->id);
});

it('does not leak the plaintext into the response of any later request', function () {
    $user = User::factory()->admin()->create();
    $this->withoutVite();

    $create = $this->actingAs($user)->post(route('admin.api-keys.store'), [
        'name' => 'Reporting',
        'abilities' => [ApiAbility::Read->value],
    ])->assertOk();

    $plain = $create->viewData('page')['props']['issuedKey']['plainText'];
    expect($plain)->toBeString()->not->toBeEmpty();

    // The index render that follows must not carry it, and it must not have been
    // written to the session on the way through.
    $index = $this->actingAs($user)->get(route('admin.api-keys.index'))->assertOk();
    expect($index->getContent())->not->toContain($plain);
    $this->assertNull(session('issuedKey'));
});

it('refuses an ability outside the enum', function () {
    $user = User::factory()->admin()->create();

    expect(fn () => app(ApiKeyService::class)->issue($user, 'ci', ['admin:everything']))
        ->toThrow(InvalidArgumentException::class);
});

it('refuses a lifetime beyond the maximum', function () {
    $user = User::factory()->admin()->create();

    expect(fn () => app(ApiKeyService::class)->issue($user, 'ci', [ApiAbility::Read->value], 999))
        ->toThrow(InvalidArgumentException::class);
});

it('authenticates an API request with a valid key', function () {
    $user = User::factory()->admin()->create();
    $key = issueKey($user);

    $this->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertOk()
        ->assertJson(['id' => $user->id, 'email' => $user->email]);
});

it('refuses a key that lacks the required ability', function () {
    $user = User::factory()->admin()->create();
    $key = issueKey($user, [ApiAbility::Write->value]);

    $this->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertForbidden();
});

it('refuses a revoked key', function () {
    $user = User::factory()->admin()->create();
    $key = issueKey($user);

    app(ApiKeyService::class)->revoke($user, (int) PersonalAccessToken::first()->getKey());

    $this->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertUnauthorized();
});

it('refuses an expired key', function () {
    $user = User::factory()->admin()->create();
    $key = issueKey($user, [ApiAbility::Read->value], 1);

    $this->travel(2)->days();

    $this->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertUnauthorized();
});

it('refuses a key whose owner has been deactivated', function () {
    // EnsureUserIsActive is on the `web` group only, so without EnsureApiKey a
    // deactivated user's key would keep working indefinitely.
    $user = User::factory()->admin()->create();
    $key = issueKey($user);

    $user->update(['is_active' => false]);

    $this->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertForbidden();
});

it('refuses a browser session carrying no key', function () {
    // config('sanctum.guard') includes `web`, so auth:sanctum accepts a logged-in
    // session and represents it as a TransientToken — whose can() returns true for
    // EVERY ability. Without EnsureApiKey the abilities: check below would pass for
    // any signed-in user holding no key at all.
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->getJson('/api/user')
        ->assertUnauthorized();
});

it('answers the ability check from the key, not from a session', function () {
    // Sanctum's guard attaches a TransientToken when it resolves a session, and
    // TransientToken::can() is true for every ability. If EnsureApiKey did not
    // re-attach the key it validated, `abilities:` would be answered by the session
    // and a Read-only key would pass a Write gate. The api group has no session
    // middleware today, so actingAs() is the only way to reach this — which is
    // precisely why the guard belongs in the middleware and not in the route list.
    $user = User::factory()->admin()->create();
    $key = issueKey($user, [ApiAbility::Write->value]);

    $this->actingAs($user)
        ->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertForbidden();
});

it('refuses an expired key even alongside a session', function () {
    $user = User::factory()->admin()->create();
    $key = issueKey($user, [ApiAbility::Read->value], 1);

    $this->travel(2)->days();

    $this->actingAs($user)
        ->withHeader('Authorization', "Bearer {$key}")
        ->getJson('/api/user')
        ->assertUnauthorized();
});

it('refuses a key belonging to a different user', function () {
    $owner = User::factory()->admin()->create();
    $other = User::factory()->admin()->create();
    issueKey($owner);

    $revoked = app(ApiKeyService::class)->revoke($other, (int) PersonalAccessToken::first()->getKey());

    expect($revoked)->toBeFalse();
    $this->assertDatabaseCount('personal_access_tokens', 1);
});

it('lets an operator issue a key from the admin screen', function () {
    $user = User::factory()->admin()->create();
    $this->withoutVite();

    $this->actingAs($user)->post(route('admin.api-keys.store'), [
        'name' => 'Reporting',
        'abilities' => [ApiAbility::Read->value],
    ])->assertOk()->assertInertia(fn ($page) => $page
        ->component('admin/api-keys/Index')
        ->where('issuedKey.name', 'Reporting')
        ->has('issuedKey.plainText'));

    $this->assertDatabaseHas('personal_access_tokens', ['name' => 'Reporting']);
});

it('refuses an unknown ability from the admin screen', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)->post(route('admin.api-keys.store'), [
        'name' => 'Reporting',
        'abilities' => ['admin:everything'],
    ])->assertSessionHasErrors('abilities.0');

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('refuses a non-admin the API key screens', function () {
    $user = User::factory()->create();
    $this->withoutVite();

    $this->actingAs($user)->get(route('admin.api-keys.index'))->assertForbidden();
    $this->actingAs($user)->post(route('admin.api-keys.store'), [
        'name' => 'Nope',
        'abilities' => [ApiAbility::Read->value],
    ])->assertForbidden();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('does not let an operator revoke another operator key', function () {
    $owner = User::factory()->admin()->create();
    $other = User::factory()->admin()->create();
    issueKey($owner);
    $key = PersonalAccessToken::first();

    $this->actingAs($other)
        ->delete(route('admin.api-keys.destroy', $key->getKey()))
        ->assertForbidden();

    $this->assertDatabaseCount('personal_access_tokens', 1);
});

it('issues a key from the console', function () {
    $user = User::factory()->admin()->create(['email' => 'ci@example.com']);

    $this->artisan('api-key:create', [
        '--user' => 'ci@example.com',
        '--name' => 'CI pipeline',
        '--ability' => [ApiAbility::Read->value],
        '--days' => 30,
    ])->assertSuccessful();

    $key = PersonalAccessToken::firstWhere('name', 'CI pipeline');

    expect($key)->not->toBeNull()
        ->and($key->tokenable_id)->toBe($user->id)
        ->and($key->abilities)->toBe([ApiAbility::Read->value])
        ->and($key->expires_at)->not->toBeNull();
});

it('issues a non-expiring key from the console only when asked', function () {
    User::factory()->admin()->create(['email' => 'ci@example.com']);

    $this->artisan('api-key:create', [
        '--user' => 'ci@example.com', '--name' => 'Default', '--ability' => [ApiAbility::Read->value],
    ])->assertSuccessful();

    $this->artisan('api-key:create', [
        '--user' => 'ci@example.com', '--name' => 'Forever',
        '--ability' => [ApiAbility::Read->value], '--never-expires' => true,
    ])->assertSuccessful();

    expect(PersonalAccessToken::firstWhere('name', 'Default')->expires_at)->not->toBeNull()
        ->and(PersonalAccessToken::firstWhere('name', 'Forever')->expires_at)->toBeNull();
});

it('refuses a console key for an unknown, deactivated, or badly scoped target', function () {
    $inactive = User::factory()->admin()->create(['email' => 'off@example.com', 'is_active' => false]);
    User::factory()->admin()->create(['email' => 'on@example.com']);

    $this->artisan('api-key:create', ['--user' => 'nobody@example.com', '--name' => 'X'])->assertFailed();

    // A key for a deactivated owner is refused by EnsureApiKey at request time,
    // so issuing one would hand over a credential that cannot authenticate.
    $this->artisan('api-key:create', ['--user' => $inactive->email, '--name' => 'X'])->assertFailed();

    $this->artisan('api-key:create', [
        '--user' => 'on@example.com', '--name' => 'X', '--ability' => ['admin:everything'],
    ])->assertFailed();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
