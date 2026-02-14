<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');

// Guest routes (login/register)
Route::middleware(['guest'])->group(function () {
    Route::get('register', [RegisterController::class, 'registerView'])->name('register');
    Route::get('login', [SessionController::class, 'loginView'])->name('login');
    Route::post('auth/register', [RegisterController::class, 'store'])->name('auth.register');
    Route::post('auth/login', [SessionController::class, 'authenticate'])->name('auth.login');
});

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('logout', [SessionController::class, 'destroy'])->name('auth.logout');

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
