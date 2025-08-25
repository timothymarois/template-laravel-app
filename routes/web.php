<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ComponentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('register', [RegisterController::class, 'registerView'])->name('register');
    Route::get('login', [SessionController::class, 'loginView'])->name('login');
    Route::post('auth/register', [RegisterController::class, 'store'])->name('auth.register');
    Route::post('auth/login', [SessionController::class, 'authenticate'])->name('auth.login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [PageController::class, 'index'])->name('index');
    Route::get('users/table', [UserController::class, 'simpleTable'])->name('users.table');

    Route::resource('users', UserController::class);
    Route::post('/users/filters', [UserController::class, 'prepareIndexFilters'])->name('users.index.filters');

    Route::get('logout', [SessionController::class, 'destroy'])->name('auth.logout');
});
