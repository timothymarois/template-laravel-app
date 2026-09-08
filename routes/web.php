<?php

use App\Http\Controllers\Admin\ApiKeyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReleaseController;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;
use Spatie\Health\Http\Middleware\RequiresSecretToken;

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');

// Health checks (spatie/laravel-health) — JSON snapshot of the last scheduled
// run, for uptime monitors / external probes. Distinct from Laravel's lightweight
// `/up` (which gates the container). Returns 503 when any check fails (see config: json_results_failure_status). Optionally
// lock down with HEALTH_SECRET_TOKEN (sent as the X-Secret-Token header).
// RequiresSecretToken is a no-op until HEALTH_SECRET_TOKEN is set, and a 403 gate
// once it is. It has to be attached explicitly: the package only auto-wires its own
// Oh Dear route, which is disabled here — so before this the documented protection
// did nothing at all.
Route::get('health', HealthCheckJsonResultsController::class)
    ->middleware(RequiresSecretToken::class)
    ->name('health');

// Deployed release version. Read by scripts/publish-production-release to prove a
// production deploy is live before the tag is published, and by any external
// deployment monitor. Never cached. See docs/concepts/deployment-endpoints.md.
Route::get('release', ReleaseController::class)->name('release.version');

// Guest routes (login/register/password reset)
Route::middleware(['guest'])->group(function () {
    Route::get('register', [RegisterController::class, 'registerView'])->name('register');
    Route::get('login', [SessionController::class, 'loginView'])->name('login');
    Route::post('auth/register', [RegisterController::class, 'store'])
        ->middleware('throttle:auth')
        ->name('auth.register');
    Route::post('auth/login', [SessionController::class, 'authenticate'])->name('auth.login');

    // Password reset. Route NAMES follow Laravel's convention rather than the local
    // auth.* scheme, deliberately: Illuminate\Auth\Notifications\ResetPassword builds
    // its link with route('password.reset', ...), so the framework notification needs
    // no customization; GenerateSitemap already excludes `password.*`. URIs follow the
    // local scheme — bare paths for views, auth/ for state-changing POSTs.
    //
    // Both POSTs carry throttle:auth. Named limiters key on the limiter NAME + IP only
    // (ThrottleRequests::handleRequestUsingNamedLimiter), so every route using
    // throttle:auth shares ONE 5/min/IP bucket. That is the intended budget for
    // unauthenticated writes. Login stays out of it — LoginRequest runs its own
    // per-email limiter, and sharing this bucket would let failed logins lock a user
    // out of password reset, which is exactly backwards.
    Route::get('forgot-password', [PasswordResetController::class, 'requestView'])
        ->name('password.request');
    Route::post('auth/forgot-password', [PasswordResetController::class, 'sendLink'])
        ->middleware('throttle:auth')
        ->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'resetView'])
        ->name('password.reset');
    Route::post('auth/reset-password', [PasswordResetController::class, 'update'])
        ->middleware('throttle:auth')
        ->name('password.store');
});

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [SessionController::class, 'destroy'])->name('auth.logout');

    // Admin routes
    // `admin` is the coarse gate: a route added here is refused by default rather
    // than exposed until somebody remembers to authorize it. Per-resource decisions
    // still go through the policies on the controllers and Form Requests.
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('users/table', [UserController::class, 'simpleTable'])->name('users.table');
        Route::resource('users', UserController::class);
        Route::post('/users/filters', [UserController::class, 'prepareIndexFilters'])->name('users.index.filters');

        // API keys. Issuance renders rather than redirects so the plaintext never
        // reaches the session store — see ApiKeyController::store().
        Route::get('api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
        Route::post('api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
        Route::delete('api-keys/{apiKey}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');

        // Component Showcase
        Route::prefix('components')->name('components.')->group(base_path('routes/components.php'));
    });
});
