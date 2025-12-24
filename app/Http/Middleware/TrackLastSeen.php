<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackLastSeen
{
    /**
     * Cache duration in minutes before updating again.
     * Prevents excessive database writes on every request.
     */
    protected int $cacheMinutes = 5;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $this->updateLastSeen($request, $request->user());
        }

        return $next($request);
    }

    /**
     * Update the user's last seen timestamp and request info.
     */
    protected function updateLastSeen(Request $request, $user): void
    {
        $cacheKey = "user-last-seen-{$user->id}";

        // Only update if not recently updated (prevents DB writes on every request)
        if (! Cache::has($cacheKey)) {
            $user->update([
                'last_seen_at' => now(),
                'last_ip_address' => $request->ip(),
                'last_user_agent' => $request->userAgent(),
            ]);

            Cache::put($cacheKey, true, now()->addMinutes($this->cacheMinutes));
        }
    }
}
