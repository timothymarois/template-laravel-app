<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Events\TenantDeleted;

/*
|--------------------------------------------------------------------------
| Tenancy disabled-state contract
|--------------------------------------------------------------------------
|
| When TENANCY_ENABLED=false (the default), tenancy must behave as if it
| does not exist. These tests guard that contract — if anyone breaks it,
| CI catches it.
|
| Tests for enabled-state behavior live alongside, but skip when disabled.
|
*/

beforeEach(function () {
    // config/tenancy.php coerces the env via filter_var, so tenancy.enabled
    // is always a proper boolean regardless of how the env was set.
    expect(config('tenancy.enabled'))->toBeFalse(
        'TENANCY_ENABLED must be false for disabled-state tests.'
    );
});

it('does not bind the tenant-created event listener', function () {
    expect(Event::hasListeners(TenantCreated::class))->toBeFalse();
});

it('does not bind the tenant-deleted event listener', function () {
    expect(Event::hasListeners(TenantDeleted::class))->toBeFalse();
});

it('does not register the vendor tenancy.asset route', function () {
    expect(Route::has('stancl.tenancy.asset'))->toBeFalse();
});

it('does not register a route at /t/anything', function () {
    $this->get('/t/anything/dashboard')->assertNotFound();
});

it('keeps App\\Models\\User as the configured auth provider model', function () {
    expect(config('auth.providers.users.model'))->toBe(App\Models\User::class);
});

it('tenant() returns null when no tenant is initialized', function () {
    // The package ships tenant() — it returns null when no tenant is bound
    // (which is always true in disabled state because no middleware ever
    // initializes a tenant).
    expect(tenant())->toBeNull();
});

it('tenant_user() returns the authenticated central user when disabled', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);
    expect(tenant_user()?->getKey())->toBe($user->id);
});

it('central_user() returns the authenticated user', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);
    expect(central_user()?->getKey())->toBe($user->id);
});

it('current_actor() delegates to tenant_user() when disabled', function () {
    $user = App\Models\User::factory()->create();
    $this->actingAs($user);
    expect(current_actor()?->getKey())->toBe($user->id);
});

it('tenant_url() returns the same URL as url() when disabled', function () {
    expect(tenant_url('/dashboard'))->toBe(url('/dashboard'));
    expect(tenant_url('/dashboard', 'acme'))->toBe(url('/dashboard'));
});

it('config tenant_model points at App\\Models\\Tenant', function () {
    expect(config('tenancy.tenant_model'))->toBe(App\Models\Tenant::class);
});

it('config domain_model points at App\\Models\\Domain', function () {
    expect(config('tenancy.domain_model'))->toBe(App\Models\Domain::class);
});

it('User uses the CentralConnection trait', function () {
    $traits = class_uses_recursive(App\Models\User::class);
    expect($traits)->toContain(App\Models\Concerns\CentralConnection::class);
});

it('User getConnectionName returns null when tenancy is disabled', function () {
    // Trait short-circuits when tenancy is disabled — Eloquent default behavior.
    // Forks copying this template see identical query routing to non-tenancy apps.
    $model = new App\Models\User;
    expect($model->getConnectionName())->toBeNull();
});

it('Tenant model extends the package base and implements TenantWithDatabase', function () {
    expect(is_subclass_of(App\Models\Tenant::class, Stancl\Tenancy\Database\Models\Tenant::class))->toBeTrue();
    expect(in_array(Stancl\Tenancy\Contracts\TenantWithDatabase::class, class_implements(App\Models\Tenant::class), true))->toBeTrue();
});

it('Domain model extends the package base', function () {
    expect(is_subclass_of(App\Models\Domain::class, Stancl\Tenancy\Database\Models\Domain::class))->toBeTrue();
});

it('Tenant\\User model exists and does not pin a connection', function () {
    expect(class_exists(App\Models\Tenant\User::class))->toBeTrue();
    $model = new App\Models\Tenant\User;
    // Inherits the default connection; gets swapped to per-tenant DB by the
    // DatabaseTenancyBootstrapper once tenancy is initialized.
    expect($model->getConnectionName())->toBeNull();
});

it('registers the tenancy:enable artisan command', function () {
    $this->artisan('tenancy:enable', ['--help' => true])->assertSuccessful();
});

it('tenancy:provision fails with a helpful message when disabled', function () {
    $this->artisan('tenancy:provision', ['name' => 'acme'])
        ->expectsOutputToContain('Tenancy is disabled')
        ->assertFailed();
});

it('tenancy:migrate-existing fails with a helpful message when disabled', function () {
    $this->artisan('tenancy:migrate-existing')
        ->expectsOutputToContain('Tenancy is disabled')
        ->assertFailed();
});

it('does not bind ExistingDataMigrator when tenancy is disabled', function () {
    // The binding only registers in AppServiceProvider when tenancy is enabled,
    // keeping the disabled-state DI container clean.
    expect(app()->bound(App\Tenancy\Contracts\ExistingDataMigrator::class))->toBeFalse();
});

it('Inertia shared props do not include currentTenant or tenantUser when disabled', function () {
    $middleware = app(App\Http\Middleware\HandleInertiaRequests::class);
    $shared = $middleware->share(request());

    expect($shared)->not->toHaveKey('currentTenant');
    expect($shared)->not->toHaveKey('tenantUser');
});

it('runs only the original four migrations', function () {
    // RefreshDatabase is in effect via Pest.php; the migrations table reflects
    // exactly what Laravel scanned at `database/migrations/` (root, no subdirs).
    $names = \DB::table('migrations')->pluck('migration')->all();

    expect($names)->toContain('0001_01_01_000000_create_users_table');
    expect($names)->toContain('0001_01_01_000001_create_cache_table');
    expect($names)->toContain('0001_01_01_000002_create_jobs_table');
    expect($names)->toContain('2024_06_23_201915_create_personal_access_tokens_table');

    // No tenancy migrations should be present — they live in database/migrations/central/
    // and database/migrations/tenant/, which Laravel does not scan by default.
    expect($names)->not->toContain('2019_09_15_000010_create_tenants_table');
    expect($names)->not->toContain('2019_09_15_000020_create_domains_table');
});
