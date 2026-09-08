<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Proves the caller presented a real API key, and that its owner is still active.
 *
 * Two holes this closes, neither of which `auth:sanctum` + `abilities:` covers on
 * its own:
 *
 *  1. `config('sanctum.guard')` includes `web`, so `auth:sanctum` also accepts a
 *     logged-in browser session. Sanctum represents that as a TransientToken, whose
 *     `can()` returns true for EVERY ability — so an `abilities:` check passes for
 *     any signed-in user carrying no key at all. Requiring a real
 *     PersonalAccessToken, owned by the authenticated user, is what makes the
 *     ability check mean anything.
 *
 *  2. EnsureUserIsActive is appended to the `web` group only, so a key belonging to
 *     a deactivated user would keep authenticating. Deactivating an account has to
 *     disable its keys in the same act.
 */
class EnsureApiKey
{
    /**
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $bearer = $request->bearerToken();

        $key = $bearer === null ? null : PersonalAccessToken::findToken($bearer);

        if (! $user instanceof User || $key === null || $key->tokenable?->is($user) !== true) {
            throw new AuthenticationException;
        }

        abort_unless($user->is_active, 403, 'Your account has been deactivated.');

        return $next($request);
    }
}
