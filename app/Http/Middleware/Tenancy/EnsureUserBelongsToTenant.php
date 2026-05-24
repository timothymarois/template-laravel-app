<?php

declare(strict_types=1);

namespace App\Http\Middleware\Tenancy;

use App\Services\Tenancy\TenantMembershipService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Enforces that the authenticated user is a member of the currently-resolved
 * tenant. Must run AFTER InitializeTenancyBySlug (path mode) or
 * InitializeTenancyByDomain (subdomain mode) — both of which populate the
 * tenant context before any other middleware fires.
 *
 * For unauthenticated requests: redirects to login with `intended()` set,
 * so the user lands back in the tenant after auth.
 *
 * For authenticated non-members: throws 403. Forks that want a friendlier
 * "you're not a member of this tenant" landing page can extend this class
 * and override the unauthorized handling.
 */
class EnsureUserBelongsToTenant
{
    public function __construct(
        private readonly TenantMembershipService $membership,
    ) {}

    public function handle(Request $request, Closure $next): mixed
    {
        $tenant = tenant();

        if ($tenant === null) {
            throw new AccessDeniedHttpException('No tenant resolved.');
        }

        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('login'));
        }

        if (! $this->membership->isMember($user, $tenant)) {
            throw new AccessDeniedHttpException(
                'You do not have access to this tenant.',
            );
        }

        return $next($request);
    }
}
