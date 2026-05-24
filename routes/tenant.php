<?php

declare(strict_types=1);

use App\Http\Middleware\Tenancy\EnsureTenantReady;
use App\Http\Middleware\Tenancy\EnsureUserBelongsToTenant;
use App\Http\Middleware\Tenancy\InitializeTenancyBySlug;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Routes registered here run in tenant context. The middleware stack is
| chosen by TENANCY_IDENTIFICATION:
|
|   path      — /t/{tenant}/...  (default; no wildcard DNS/SSL required)
|   subdomain — <tenant>.example.com/...  (requires wildcard DNS + SSL)
|
| This entire file is loaded only when TENANCY_ENABLED=true. In the disabled
| state, App\Providers\TenancyServiceProvider::boot() never reaches mapRoutes(),
| so none of these routes are registered.
|
| Add your tenant-scoped routes inside the appropriate group below. Forks
| typically replace the example "/" route with their own dashboard / API /
| etc., scoped to the tenant context.
|
*/

// The example "/" route below is unauthenticated so a new fork can verify
// tenancy resolution without setting up auth first. Real tenant routes
// (dashboard, projects, settings) should sit inside the auth-guarded group
// at the bottom of this file.

if (config('tenancy.identification', 'path') === 'subdomain') {
    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
        EnsureTenantReady::class,
    ])->group(function (): void {
        Route::get('/', function () {
            return 'This is your multi-tenant application. The id of the current tenant is '.tenant('id');
        });

        Route::middleware(['auth:sanctum', EnsureUserBelongsToTenant::class])->group(function (): void {
            // Add authenticated tenant-scoped routes here.
        });
    });
} else {
    // Path mode (default) — tenant slug lives in the URL prefix and resolves
    // via the `domains` table (the package's InitializeTenancyByPath looks up
    // by primary key, which doesn't match human-friendly slugs).
    Route::middleware([
        'web',
        InitializeTenancyBySlug::class,
        EnsureTenantReady::class,
    ])->prefix('t/{tenant}')->group(function (): void {
        Route::get('/', function () {
            return 'This is your multi-tenant application. The id of the current tenant is '.tenant('id');
        });

        Route::middleware(['auth:sanctum', EnsureUserBelongsToTenant::class])->group(function (): void {
            // Add authenticated tenant-scoped routes here. EnsureUserBelongsToTenant
            // enforces that the auth user has a row in `tenant_user` for this
            // tenant — non-members get 403; unauthenticated users get redirected
            // to login.
        });
    });
}
