<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * Checks if the authenticated user is active. If not, logs them out
     * and redirects to login with an error message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! $request->user()->is_active) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(403, 'Your account has been deactivated.');
            }

            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated.');
        }

        return $next($request);
    }
}
