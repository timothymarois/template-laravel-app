<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse gate for the whole `admin` prefix, applied alongside — not instead of —
 * the policies on individual actions.
 *
 * The group's own `auth:sanctum` proves only that somebody is signed in. This
 * middleware exists so a route added to that group tomorrow is refused by default
 * rather than exposed until somebody remembers to authorize it; the policy is what
 * decides a specific resource.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user !== null && $user->role->canAccessAdmin(), 403);

        return $next($request);
    }
}
