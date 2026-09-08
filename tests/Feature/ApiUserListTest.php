<?php

declare(strict_types=1);

use App\Enums\ApiAbility;
use App\Models\User;
use App\Services\ApiKeyService;
use Illuminate\Testing\TestResponse;

/*
| GET /api/users is the collection endpoint an API key can actually be exercised
| against. Two independent gates guard it and both are asserted here: the key's
| ability says what the KEY may do, the policy says whether its OWNER may.
*/

function keyFor(User $owner, array $abilities = [ApiAbility::Read->value]): string
{
    return app(ApiKeyService::class)->issue($owner, 'test', $abilities)->plainText;
}

function getUsers(string $key, string $query = ''): TestResponse
{
    return test()->withHeader('Authorization', 'Bearer '.$key)->getJson('/api/users'.$query);
}

it('returns a paginated list to an admin key with the read ability', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(3)->create();

    $response = getUsers(keyFor($admin));

    $response->assertOk()
        ->assertJsonCount(4, 'data')
        ->assertJsonPath('meta.total', 4)
        ->assertJsonPath('meta.per_page', 15)
        ->assertJsonPath('meta.current_page', 1);
});

it('returns only the named fields, never the serialized model', function () {
    // A serialized User would publish every column added later — password hash,
    // remember_token, and whatever a fork adds next — to every integration.
    $admin = User::factory()->admin()->create();

    $response = getUsers(keyFor($admin));

    expect(array_keys($response->json('data.0')))
        ->toEqualCanonicalizing(['id', 'name', 'email', 'role', 'is_active', 'created_at']);
});

it('refuses a valid key whose owner is not an admin', function () {
    // The ability is present; the policy still says no.
    $response = getUsers(keyFor(User::factory()->create()));

    $response->assertForbidden();
});

it('refuses an admin key that lacks the read ability', function () {
    $admin = User::factory()->admin()->create();

    $response = getUsers(keyFor($admin, [ApiAbility::Write->value]));

    $response->assertForbidden();
});

it('refuses a request with no key at all', function () {
    $this->getJson('/api/users')->assertUnauthorized();
});

it('refuses a browser session presenting no key', function () {
    // sanctum's web guard yields a TransientToken whose ability check passes for
    // everything; api.key is what rules it out.
    $this->actingAs(User::factory()->admin()->create())
        ->getJson('/api/users')
        ->assertUnauthorized();
});

it('refuses a key whose owner has been deactivated', function () {
    $admin = User::factory()->admin()->create();
    $key = keyFor($admin);
    $admin->update(['is_active' => false]);

    getUsers($key)->assertForbidden();
});

it('bounds perPage so one key cannot ask for the whole table', function () {
    $admin = User::factory()->admin()->create();

    getUsers(keyFor($admin), '?perPage=101')->assertJsonValidationErrorFor('perPage');
    getUsers(keyFor($admin), '?perPage=100')->assertOk();
});

it('allow-lists sortField because it reaches orderBy', function () {
    $admin = User::factory()->admin()->create();

    getUsers(keyFor($admin), '?sortField=password')->assertJsonValidationErrorFor('sortField');
    getUsers(keyFor($admin), '?sortField=name&sortOrder=-1')->assertOk();
});

it('paginates', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(4)->create();

    $response = getUsers(keyFor($admin), '?perPage=2&page=2');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.last_page', 3);
});

it('searches by name and email', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->create(['name' => 'Ada Lovelace']);

    $response = getUsers(keyFor($admin), '?search=Lovelace');

    $response->assertOk()->assertJsonPath('meta.total', 1)->assertJsonPath('data.0.name', 'Ada Lovelace');
});
