<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Http\Middleware\Tenancy\EnsureUserBelongsToTenant;
use App\Http\Middleware\Tenancy\InitializeTenancyBySlug;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| Path-mode routing — full integration test
|--------------------------------------------------------------------------
|
| Exercises the two custom middleware that make path-mode tenancy work end
| to end against real HTTP requests:
|
|   InitializeTenancyBySlug      — resolves /t/{slug}/ to a tenant via the
|                                  `domains` table (the package's stock
|                                  InitializeTenancyByPath looks up by primary
|                                  key, which doesn't match human-friendly slugs).
|   EnsureUserBelongsToTenant    — enforces pivot membership; 403 for non-
|                                  members, redirect-to-login for guests.
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class]);

    // The middleware calls tenancy()->initialize($tenant), which fires the
    // bootstrappers. DatabaseTenancyBootstrapper expects the per-tenant DB to
    // exist — but with faked TenantCreated the package's CreateDatabase job
    // never runs, so no SQLite file is created. Clear the bootstrappers for
    // routing tests so initialize() is a no-op beyond setting the tenant
    // context in the container.
    config(['tenancy.bootstrappers' => []]);

    // Register an inline test route on the same middleware stack we ship.
    Route::middleware(['web', InitializeTenancyBySlug::class])
        ->prefix('t/{tenant}')
        ->get('/echo', fn () => 'tenant: '.tenant('id'))
        ->name('test.tenant.echo');

    Route::middleware(['web', InitializeTenancyBySlug::class, 'auth', EnsureUserBelongsToTenant::class])
        ->prefix('t/{tenant}')
        ->get('/secret', fn () => 'secret: '.tenant('id'))
        ->name('test.tenant.secret');

    // Stub login route — EnsureUserBelongsToTenant redirects unauthenticated
    // users to route('login'), so it must exist for ALL tests, not just the
    // explicit redirect test. Otherwise any unauthenticated request to
    // /t/.../secret throws RouteNotFoundException instead of redirecting.
    Route::get('/login', fn () => 'login page')->name('login');
});

// ============================================================================
// InitializeTenancyBySlug
// ============================================================================

it('resolves /t/{slug}/ to the matching tenant by domain', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->domains()->create(['domain' => 'acme']);

    $response = $this->get('/t/acme/echo');

    $response->assertOk();
    expect($response->getContent())->toBe('tenant: '.$tenant->id);
});

it('returns 500 (TenantCouldNotBeIdentifiedByPathException) for unknown slug', function () {
    // Production forks should register an exception handler to render 404;
    // the template ships the raw package exception as the default behavior.
    $response = $this->get('/t/nonexistent/echo');

    expect($response->getStatusCode())->toBe(500);
});

it('isolates tenants — slug A and slug B resolve to different tenant ids', function () {
    $a = Tenant::create(['id' => (string) Str::uuid()]);
    $a->domains()->create(['domain' => 'acme']);
    $b = Tenant::create(['id' => (string) Str::uuid()]);
    $b->domains()->create(['domain' => 'globex']);

    $responseA = $this->get('/t/acme/echo');
    $responseB = $this->get('/t/globex/echo');

    expect($responseA->getContent())->toBe('tenant: '.$a->id);
    expect($responseB->getContent())->toBe('tenant: '.$b->id);
    expect($a->id)->not->toBe($b->id);
});

// ============================================================================
// EnsureUserBelongsToTenant
// ============================================================================

it('returns 403 when an authenticated user is not a member of the tenant', function () {
    $stranger = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->domains()->create(['domain' => 'acme']);
    // Stranger is NOT attached to the tenant.

    $response = $this->actingAs($stranger)->get('/t/acme/secret');

    expect($response->getStatusCode())->toBe(403);
});

it('allows authenticated members to access tenant routes', function () {
    $member = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->domains()->create(['domain' => 'acme']);
    $tenant->users()->attach($member->id, ['role' => TenantRole::Member->value]);

    $response = $this->actingAs($member)->get('/t/acme/secret');

    $response->assertOk();
    expect($response->getContent())->toBe('secret: '.$tenant->id);
});

it('redirects unauthenticated guests to login', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->domains()->create(['domain' => 'acme']);

    // We need a 'login' route to redirect to.
    Route::get('/login', fn () => 'login page')->name('login');

    $response = $this->get('/t/acme/secret');

    $response->assertRedirect('/login');
});

it('access control respects tenant pivot for the SAME user across tenants', function () {
    $user = User::factory()->create();

    $tenantA = Tenant::create(['id' => (string) Str::uuid()]);
    $tenantA->domains()->create(['domain' => 'acme']);
    $tenantA->users()->attach($user->id, ['role' => TenantRole::Owner->value]);

    $tenantB = Tenant::create(['id' => (string) Str::uuid()]);
    $tenantB->domains()->create(['domain' => 'globex']);
    // User NOT attached to globex.

    expect($this->actingAs($user)->get('/t/acme/secret')->getStatusCode())->toBe(200);
    expect($this->actingAs($user)->get('/t/globex/secret')->getStatusCode())->toBe(403);
});
