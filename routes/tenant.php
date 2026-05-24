<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;
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

if (config('tenancy.identification', 'path') === 'subdomain') {
    Route::middleware([
        'web',
        InitializeTenancyByDomain::class,
        PreventAccessFromCentralDomains::class,
    ])->group(function (): void {
        Route::get('/', function () {
            return 'This is your multi-tenant application. The id of the current tenant is '.tenant('id');
        });
    });
} else {
    // Path mode (default) — tenant slug lives in the URL prefix.
    Route::middleware([
        'web',
        InitializeTenancyByPath::class,
    ])->prefix('t/{tenant}')->group(function (): void {
        Route::get('/', function () {
            return 'This is your multi-tenant application. The id of the current tenant is '.tenant('id');
        });
    });
}
