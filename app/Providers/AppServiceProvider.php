<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Opcodes\LogViewer\Facades\LogViewer;

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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePasswordRules();
        $this->configureRateLimiting();
        $this->configureLogViewer();
    }

    /**
     * Configure Log Viewer authorization.
     */
    protected function configureLogViewer(): void
    {
        // Allow access in local/dev mode, require auth in production
        LogViewer::auth(function (Request $request) {
            return app()->environment('local', 'development')
                || $request->user()?->is_admin;
        });
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
