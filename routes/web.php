<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\ComponentShowcaseController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
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
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('users/table', [UserController::class, 'simpleTable'])->name('users.table');
        Route::resource('users', UserController::class);
        Route::post('/users/filters', [UserController::class, 'prepareIndexFilters'])->name('users.index.filters');

        // Component Showcase
        Route::prefix('components')->name('components.')->group(function () {
            Route::get('/', [ComponentShowcaseController::class, 'index'])->name('index');

            // Forms
            Route::get('/forms', [ComponentShowcaseController::class, 'formsIndex'])->name('forms');
            Route::get('/forms/input', [ComponentShowcaseController::class, 'formsInput'])->name('forms.input');
            Route::get('/forms/select', [ComponentShowcaseController::class, 'formsSelect'])->name('forms.select');
            Route::get('/forms/checkbox', [ComponentShowcaseController::class, 'formsCheckbox'])->name('forms.checkbox');
            Route::get('/forms/fields', [ComponentShowcaseController::class, 'formsFields'])->name('forms.fields');

            // Actions
            Route::get('/actions', [ComponentShowcaseController::class, 'actionsIndex'])->name('actions');
            Route::get('/actions/button', [ComponentShowcaseController::class, 'actionsButton'])->name('actions.button');
            Route::get('/actions/menu', [ComponentShowcaseController::class, 'actionsMenu'])->name('actions.menu');
            Route::get('/actions/dialog', [ComponentShowcaseController::class, 'actionsDialog'])->name('actions.dialog');

            // Display
            Route::get('/display', [ComponentShowcaseController::class, 'displayIndex'])->name('display');
            Route::get('/display/card', [ComponentShowcaseController::class, 'displayCard'])->name('display.card');
            Route::get('/display/badge', [ComponentShowcaseController::class, 'displayBadge'])->name('display.badge');
            Route::get('/display/tooltip', [ComponentShowcaseController::class, 'displayTooltip'])->name('display.tooltip');

            // Data
            Route::get('/data', [ComponentShowcaseController::class, 'dataIndex'])->name('data');
            Route::get('/data/table', [ComponentShowcaseController::class, 'dataTable'])->name('data.table');
            Route::get('/data/actions', [ComponentShowcaseController::class, 'dataActions'])->name('data.actions');
            Route::get('/data/pagination', [ComponentShowcaseController::class, 'dataPagination'])->name('data.pagination');
        });
    });
});
