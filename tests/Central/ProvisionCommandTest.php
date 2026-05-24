<?php

declare(strict_types=1);

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| tenancy:provision integration tests
|--------------------------------------------------------------------------
|
| Exercises the full provision flow: creating tenant + domain rows, attaching
| an owner via the pivot, the not-found-user failure path, and the duplicate-
| domain failure path.
|
| The TenantCreated event pipeline (CreateDatabase → MigrateDatabase) runs
| synchronously per the package's stock provider config. In these tests the
| pipeline still fires but acts against in-memory SQLite — the per-tenant DB
| creation is exercised by the package, we just verify the central-side
| pivot/domain/tenant rows are correct.
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    // Fake TenantCreated so the package's CreateDatabase → MigrateDatabase
    // pipeline doesn't fire — we're testing the central-side provisioning
    // logic, not per-tenant DB creation (which would write actual SQLite
    // files into database/). The pipeline is the package's responsibility
    // and is tested by the package itself.
    Event::fake([TenantCreated::class]);
});

it('creates a tenant + domain without an owner', function () {
    $this->artisan('tenancy:provision', ['name' => 'Acme'])
        ->assertSuccessful();

    expect(Tenant::count())->toBe(1);
    expect(Domain::where('domain', 'acme')->exists())->toBeTrue();
});

it('attaches the owner via the pivot when --owner=<email> matches an existing user', function () {
    $owner = User::factory()->create(['email' => 'owner@example.com']);

    $this->artisan('tenancy:provision', [
        'name' => 'Acme',
        '--owner' => 'owner@example.com',
    ])->assertSuccessful();

    $tenant = Tenant::first();
    expect($tenant->users)->toHaveCount(1);
    expect($tenant->users->first()->id)->toBe($owner->id);
    expect($tenant->users->first()->pivot->role)->toBe('owner');
});

it('fails fast when --owner=<email> does not match any user (no tenant created)', function () {
    $this->artisan('tenancy:provision', [
        'name' => 'Acme',
        '--owner' => 'ghost@example.com',
    ])
        ->expectsOutputToContain("No user with email 'ghost@example.com' found")
        ->assertFailed();

    expect(Tenant::count())->toBe(0);
    expect(Domain::count())->toBe(0);
});

it('fails when the subdomain is already taken', function () {
    Tenant::create(['id' => 'existing-id', 'data' => ['name' => 'Acme']])
        ->domains()->create(['domain' => 'acme']);

    $this->artisan('tenancy:provision', ['name' => 'Acme'])
        ->expectsOutputToContain("A tenant with domain 'acme' already exists")
        ->assertFailed();

    expect(Tenant::count())->toBe(1); // The existing one, no second tenant created.
});

it('respects --subdomain override', function () {
    User::factory()->create(['email' => 'owner@example.com']);

    $this->artisan('tenancy:provision', [
        'name' => 'Acme Corp',
        '--subdomain' => 'acme-corp-2',
        '--owner' => 'owner@example.com',
    ])->assertSuccessful();

    expect(Domain::where('domain', 'acme-corp-2')->exists())->toBeTrue();
    expect(Domain::where('domain', 'acme')->exists())->toBeFalse();
});

it('records joined_at on the pivot when attaching an owner', function () {
    User::factory()->create(['email' => 'owner@example.com']);

    $this->artisan('tenancy:provision', [
        'name' => 'Acme',
        '--owner' => 'owner@example.com',
    ])->assertSuccessful();

    $pivot = Tenant::first()->users->first()->pivot;
    expect($pivot->joined_at)->not->toBeNull();
});
