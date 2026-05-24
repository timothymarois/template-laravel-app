<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Tenancy\InviteController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');

// Tenant invite acceptance endpoints — registered only when tenancy is enabled
// (otherwise hitting them would query a non-existent tenant_invites table).
// Forks in disabled state see /invites/anything → 404 from the routing layer.
// Throttled (60/min/IP) to discourage token brute-force.
if (config('tenancy.enabled')) {
    Route::middleware(['throttle:60,1'])->group(function (): void {
        Route::get('invites/{token}', [InviteController::class, 'show'])->name('invites.show');
        Route::post('invites/{token}/accept', [InviteController::class, 'accept'])->name('invites.accept');
        Route::post('invites/{token}/decline', [InviteController::class, 'decline'])->name('invites.decline');
    });
}

// Guest routes (login/register)
Route::middleware(['guest'])->group(function () {
    Route::get('register', [RegisterController::class, 'registerView'])->name('register');
    Route::get('login', [SessionController::class, 'loginView'])->name('login');
    Route::post('auth/register', [RegisterController::class, 'store'])->name('auth.register');
    Route::post('auth/login', [SessionController::class, 'authenticate'])->name('auth.login');
});

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [SessionController::class, 'destroy'])->name('auth.logout');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('users/table', [UserController::class, 'simpleTable'])->name('users.table');
        Route::resource('users', UserController::class);
        Route::post('/users/filters', [UserController::class, 'prepareIndexFilters'])->name('users.index.filters');

        // Component Showcase
        Route::prefix('components')->name('components.')->group(base_path('routes/components.php'));
    });
});
