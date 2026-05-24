<?php

declare(strict_types=1);

namespace App\Http\Middleware\Tenancy;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Blocks requests to tenants whose provisioning hasn't completed
 * (`Tenant::isReady()` returns false). Returns 503 by default — forks that
 * want a friendlier "your workspace is still being provisioned, hang tight"
 * landing page can extend and override the unready response.
 *
 * Why this matters: when the TenantCreated pipeline (CreateDatabase →
 * MigrateDatabase) runs asynchronously, there's a window where the tenant
 * row exists but the per-tenant DB is empty or missing. A user clicking
 * their tenant URL during that window hits SQL errors. This middleware
 * intercepts and returns a clean status instead.
 *
 * Apply AFTER InitializeTenancyBySlug / InitializeTenancyByDomain so
 * tenant() resolves. Apply BEFORE auth/membership middleware so unready
 * tenants are rejected before any DB work fires.
 */
class EnsureTenantReady
{
    public function handle(Request $request, Closure $next): mixed
    {
        $tenant = tenant();

        if ($tenant === null) {
            // Defensive — should be unreachable if middleware ordering is correct.
            throw new ServiceUnavailableHttpException(message: 'No tenant resolved.');
        }

        // The package's stock Tenant doesn't have isReady() — only our
        // App\Models\Tenant does. Defensive method_exists() check keeps the
        // middleware safe if a fork swaps in a different tenant model.
        if (method_exists($tenant, 'isReady') && ! $tenant->isReady()) {
            throw new ServiceUnavailableHttpException(
                retryAfter: 30,
                message: 'Tenant is still being provisioned. Please retry shortly.',
            );
        }

        return $next($request);
    }
}
