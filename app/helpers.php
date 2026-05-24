<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Authenticatable;
use Stancl\Tenancy\Contracts\Tenant;

/*
|--------------------------------------------------------------------------
| Template tenancy helpers
|--------------------------------------------------------------------------
|
| Helpers that complement what the package ships. The package itself
| autoloads helpers.php (vendor/stancl/tenancy/src/helpers.php) which
| already provides:
|   - tenancy()       — the Tenancy service singleton
|   - tenant($key)    — the current tenant model or one of its attributes
|   - tenant_asset()  — URL to a tenant-scoped asset (gated by config.routes)
|   - tenant_route()  — cross-tenant subdomain URL (signature: $domain, $route, ...)
|   - global_asset(), global_cache()
|
| The functions below add what's missing for our use case and provide the
| disabled-state contract: every helper short-circuits to a safe fallback
| when TENANCY_ENABLED=false.
|
*/

if (! function_exists('tenant_user')) {
    /**
     * The user resolved inside the current tenant context. Equivalent to
     * auth()->user() — the template stores the auth principal in the central
     * App\Models\User and uses the `tenant_user` pivot for membership/role,
     * so there is no separate tenant-side User model to swap to. Kept as a
     * helper for callers that want to express "the user inside this tenant"
     * explicitly.
     */
    function tenant_user(): ?Authenticatable
    {
        return auth()->user();
    }
}

if (! function_exists('central_user')) {
    /**
     * The central authenticated user. Equivalent to auth()->user() — exposed
     * as a helper for symmetry with tenant_user() so callers can be explicit
     * about which side they want.
     */
    function central_user(): ?Authenticatable
    {
        return auth()->user();
    }
}

if (! function_exists('current_actor')) {
    /**
     * The acting entity in the current request. Defaults to the auth user.
     * Future versions may return a Tenant\ConnectedApp instance when a
     * Passport token is used for the request.
     */
    function current_actor(): ?Authenticatable
    {
        return tenant_user();
    }
}

if (! function_exists('tenant_url')) {
    /**
     * Build a URL into the given tenant (or the current tenant if omitted).
     * Complements the package's tenant_route() which only handles subdomain
     * mode — this helper is primarily for path mode (the template's default).
     *
     * - Disabled state: equivalent to url($path).
     * - Path mode: returns "<central>/t/{slug}/<path>".
     * - Subdomain mode: delegates to url($path). For cross-tenant URL
     *   generation in subdomain mode, use the package's tenant_route($domain,
     *   $route, ...) helper instead — it does the proper host rewriting.
     *
     * The slug in path mode is either the explicit string passed as
     * $tenantOrSubdomain, or the tenant's primary key.
     */
    function tenant_url(string $path, mixed $tenantOrSubdomain = null): string
    {
        if (! config('tenancy.enabled')) {
            return url($path);
        }

        if (config('tenancy.identification', 'path') === 'subdomain') {
            return url($path);
        }

        $tenant = $tenantOrSubdomain ?? tenant();

        if ($tenant === null) {
            return url($path);
        }

        $slug = $tenant instanceof Tenant
            ? (string) $tenant->getTenantKey()
            : (string) $tenant;

        if ($slug === '') {
            return url($path);
        }

        return url('/t/'.$slug.'/'.ltrim($path, '/'));
    }
}
