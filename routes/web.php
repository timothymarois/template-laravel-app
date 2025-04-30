<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'index'])->name('index');

Route::get('/theme', [PageController::class, 'pTheme']);
Route::get('/components/buttons', [PageController::class, 'pButtons']);
Route::get('/components/forms', [PageController::class, 'pForms']);

Route::middleware(['guest'])->group(function () {
    Route::get('register', [RegisterController::class, 'registerView'])->name('register');
    Route::get('login', [SessionController::class, 'loginView'])->name('login');
    Route::post('auth/register', [RegisterController::class, 'store'])->name('auth.register');
    Route::post('auth/login', [SessionController::class, 'authenticate'])->name('auth.login');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('/admin/users', UserController::class);
    Route::get('logout', [SessionController::class, 'destroy'])->name('auth.logout');
});
