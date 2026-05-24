<?php

declare(strict_types=1);

namespace App\Http\Middleware\Tenancy;

use App\Models\Domain;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedByPathException;
use Stancl\Tenancy\Tenancy;

/**
 * Path-mode tenant resolver that looks up the tenant by DOMAIN SLUG, not by
 * primary key.
 *
 * The package's stock InitializeTenancyByPath middleware expects the URL
 * segment to BE the tenant id (e.g. `/t/8ccabba5-fd25-4a9f-8d9e-77ed.../...`).
 * For human-friendly URLs like `/t/acme/...` we need to translate the slug
 * `acme` to the matching Tenant via the `domains` table.
 *
 * Usage in routes/tenant.php (path mode):
 *
 *     Route::middleware(['web', InitializeTenancyBySlug::class])
 *         ->prefix('t/{tenant}')
 *         ->group(function () { ... });
 *
 * After resolution, the route's {tenant} parameter is forgotten (so it
 * doesn't pollute controller signatures), and `tenant()` returns the
 * resolved Tenant for the duration of the request.
 */
class InitializeTenancyBySlug
{
    public function __construct(
        private readonly Tenancy $tenancy,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $route = $request->route();
        $slug = $route?->parameter('tenant');

        if (! is_string($slug) || $slug === '') {
            throw new TenantCouldNotBeIdentifiedByPathException((string) $slug);
        }

        $domain = Domain::where('domain', $slug)->first();

        if ($domain === null) {
            throw new TenantCouldNotBeIdentifiedByPathException($slug);
        }

        $tenant = $this->tenancy->find($domain->tenant_id);

        if ($tenant === null) {
            throw new TenantCouldNotBeIdentifiedByPathException($slug);
        }

        // Remove the {tenant} param so controllers don't receive it as an argument.
        // $route is guaranteed non-null here: a null route would have made $slug
        // null above and thrown before reaching this line.
        $route->forgetParameter('tenant');

        $this->tenancy->initialize($tenant);

        return $next($request);
    }
}
