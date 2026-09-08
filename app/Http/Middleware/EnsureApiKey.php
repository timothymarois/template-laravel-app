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

        // Expiry is checked here, not left to Sanctum's guard: this middleware does
        // its own findToken() lookup, which does not go through the guard's expiry
        // path when the user was resolved some other way.
        if ($key->expires_at !== null && $key->expires_at->isPast()) {
            throw new AuthenticationException;
        }

        abort_unless($user->is_active, 403, 'Your account has been deactivated.');

        // Re-attach the key we just validated. The `abilities:` middleware does not
        // look at the token found here — it asks $request->user()->currentAccessToken(),
        // which is whatever Sanctum's guard attached first. With `web` in
        // config('sanctum.guard'), a resolved session attaches a TransientToken whose
        // can() is unconditionally true, so without this line the ability check would
        // still be answered by the session rather than by the key. That path is not
        // reachable through the current `api` group (it has no session middleware),
        // which is exactly why it must be closed here rather than depended upon.
        $user->withAccessToken($key);

        return $next($request);
    }
}
