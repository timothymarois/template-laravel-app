<?php

declare(strict_types=1);

namespace App\Providers;

use App\Health\Checks\ReverbCheck;
use App\Health\DiscordHealthChannel;
use App\Health\Listeners\NotifyOnHealthRecovery;
use App\Health\Listeners\NotifyOnMaintenanceMode;
use App\Policies\ApiKeyPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Events\MaintenanceModeDisabled;
use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Horizon\Horizon;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Events\CheckEndedEvent;
use Spatie\Health\Facades\Health;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * PersonalAccessToken lives in the Sanctum namespace, so Laravel's convention
     * of finding App\Policies\{Model}Policy for App\Models\{Model} does not reach
     * it. Without this the policy is silently absent and every ability denies.
     */
    private function configurePolicies(): void
    {
        Gate::policy(PersonalAccessToken::class, ApiKeyPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePolicies();
        $this->configurePasswordRules();
        $this->configureRateLimiting();
        $this->configureHealthChecks();
    }

    /**
     * Register application health checks (surfaced at the /health endpoint and
     * run on a schedule). Each optional check is gated with ->if() on whether the
     * fork actually uses that dependency, so it's skipped — not failed — elsewhere
     * (config: treat_skipped_as_failure = false). Reverb + Horizon are the two the
     * full-variant stack cares about; the rest are cheap infra coverage.
     */
    protected function configureHealthChecks(): void
    {
        // Discord notification channel for failed checks (spatie ships only
        // mail + slack). Selected via config when HEALTH_DISCORD_WEBHOOK_URL is set.
        Notification::extend('discord', fn () => new DiscordHealthChannel);

        // spatie notifies on failure only — this adds a "recovered" Discord ping
        // when a check flips back to ok.
        Event::listen(CheckEndedEvent::class, NotifyOnHealthRecovery::class);

        // Discord pings when the app enters/leaves maintenance mode (artisan down/up).
        Event::listen(MaintenanceModeEnabled::class, [NotifyOnMaintenanceMode::class, 'enabled']);
        Event::listen(MaintenanceModeDisabled::class, [NotifyOnMaintenanceMode::class, 'disabled']);

        Health::checks([
            UsedDiskSpaceCheck::new(),

            // Skipped on DB-less forks (no database name configured).
            DatabaseCheck::new()
                ->if(fn () => filled(config('database.connections.'.config('database.default', '').'.database'))),

            // Only when Redis actually backs cache, queue, or sessions.
            RedisCheck::new()
                ->if(fn () => in_array('redis', [config('cache.default'), config('queue.default'), config('session.driver')], true)),

            // Horizon master supervisor is running (needs the queue worker stack).
            HorizonCheck::new()
                ->if(fn () => class_exists(Horizon::class)),

            // Jobs are actually being processed (heartbeat job — see schedule).
            QueueCheck::new()
                ->if(fn () => config('queue.default') !== 'sync'),

            // Scheduler is firing (heartbeat — see schedule). Tolerate 2 min: the
            // heartbeat runs once a minute, so the default 1-min window false-fails
            // on the slightest scheduler jitter (and flaps → spurious recovery pings).
            ScheduleCheck::new()->heartbeatMaxAgeInMinutes(2),

            // Reverb websocket server is accepting connections (full-variant only).
            ReverbCheck::new()
                ->if(fn () => config('broadcasting.default') === 'reverb'),
        ]);
    }

    /**
     * Configure the default password validation rules.
     */
    protected function configurePasswordRules(): void
    {
        Password::defaults(function () {
            $rule = Password::min(8);

            // In production, require stronger passwords
            if (app()->isProduction()) {
                $rule = $rule
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols();
            }

            return $rule;
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // API rate limit: 60 requests per minute per user/IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Stricter limit for auth endpoints: 5 per minute
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Uploads: 10 per minute
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });
    }
}
