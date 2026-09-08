<?php

use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\TrackLastSeen;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        channels: __DIR__.'/../routes/channels.php',
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Behind a TLS-terminating proxy (Coolify/Traefik, load balancers): trust it
        // so Laravel reads X-Forwarded-Proto/Host and generates HTTPS URLs. Without
        // this, the app sees the proxy's plain-HTTP hop and emits http:// links —
        // Ziggy/redirects then trigger mixed-content blocks on an HTTPS page. The
        // container is only reachable via the proxy, so trusting all proxies is safe.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO);

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        $middleware->web(append: [
            SecurityHeaders::class,
            TrackLastSeen::class,
            EnsureUserIsActive::class,
            HandleInertiaRequests::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // Health checks (spatie/laravel-health): run + store results every minute
        // so /health serves the latest snapshot. The two heartbeats feed the
        // Queue and Schedule checks — QueueCheck reads the queued heartbeat job's
        // last run; ScheduleCheck confirms the scheduler itself is firing.
        // onOneServer + withoutOverlapping: on a multi-instance deploy this keeps
        // a single scheduler from running health:check twice in a tick (which would
        // double every notification). Safe on single-server too.
        $schedule->command('health:check')->everyMinute()->withoutOverlapping()->onOneServer();
        $schedule->command('health:queue-check-heartbeat')->everyMinute()->onOneServer();
        $schedule->command('health:schedule-check-heartbeat')->everyMinute()->onOneServer();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);

        // Render custom error pages for common HTTP errors
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            $status = $response->getStatusCode();

            // Only render custom pages for specific error codes
            if (! in_array($status, [404, 500, 503])) {
                return $response;
            }

            // Don't render Inertia pages for API requests
            if ($request->is('api/*')) {
                return $response;
            }

            return Inertia::render("errors/{$status}")
                ->toResponse($request)
                ->setStatusCode($status);
        });
    })->create();
