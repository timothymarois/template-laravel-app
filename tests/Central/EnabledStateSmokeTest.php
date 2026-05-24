<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Events\TenantDeleted;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| Tenancy enabled-state smoke tests
|--------------------------------------------------------------------------
|
| These tests are the inverse of DisabledStateTest. They verify that
| when TENANCY_ENABLED=true, the tenancy machinery actually wires up.
| Each is the mirror of a disabled-state assertion.
|
| These run only under the Central testsuite, which CentralBaseTestCase
| auto-skips when tenancy is disabled — so they're inert in normal
| `pnpm check` runs and only exercise their assertions under the
| tenancy-enabled CI job (phpunit.tenancy.xml).
|
*/

uses(CentralBaseTestCase::class);

it('has tenancy enabled when running under the Central suite', function () {
    expect(config('tenancy.enabled'))->toBeTrue();
});

it('User getConnectionName returns the central connection when enabled', function () {
    $user = new App\Models\User;
    expect($user->getConnectionName())->toBe(config('tenancy.database.central_connection'));
});

it('binds the TenantCreated event pipeline', function () {
    expect(Event::hasListeners(TenantCreated::class))->toBeTrue();
});

it('binds the TenantDeleted event pipeline', function () {
    expect(Event::hasListeners(TenantDeleted::class))->toBeTrue();
});

it('binds ExistingDataMigrator to NullExistingDataMigrator by default', function () {
    expect(app()->bound(App\Tenancy\Contracts\ExistingDataMigrator::class))->toBeTrue();
    expect(app(App\Tenancy\Contracts\ExistingDataMigrator::class))
        ->toBeInstanceOf(App\Tenancy\NullExistingDataMigrator::class);
});

it('registers the path-mode tenant route group', function () {
    // routes/tenant.php is loaded only when tenancy is enabled.
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes());
    $hasTenantPrefixedRoute = $routes->contains(
        fn ($route) => str_starts_with($route->uri(), 't/{tenant}'),
    );
    expect($hasTenantPrefixedRoute)->toBeTrue();
});

it('User has a tenants() BelongsToMany relationship', function () {
    $user = new App\Models\User;
    expect($user->tenants())
        ->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
    expect($user->tenants()->getTable())->toBe('tenant_user');
    expect($user->tenants()->getRelatedPivotKeyName())->toBe('tenant_id');
    expect($user->tenants()->getForeignPivotKeyName())->toBe('user_id');
});

it('Tenant has a users() BelongsToMany relationship', function () {
    $tenant = new App\Models\Tenant;
    expect($tenant->users())
        ->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
    expect($tenant->users()->getTable())->toBe('tenant_user');
    expect($tenant->users()->getRelatedPivotKeyName())->toBe('user_id');
    expect($tenant->users()->getForeignPivotKeyName())->toBe('tenant_id');
});
