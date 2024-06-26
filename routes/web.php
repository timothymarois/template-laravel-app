<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'index'])->name('index');

/**
 * Auth:
 * - Register
 * - Login
 */
Route::get('register', [RegisterController::class, 'registerView'])->name('register');
Route::get('login', [SessionController::class, 'loginView'])->name('login');
Route::post('auth/register', [RegisterController::class, 'store'])->name('auth.register');
Route::post('auth/login', [SessionController::class, 'authenticate'])->name('auth.login');
Route::get('logout', [SessionController::class, 'destroy'])->name('auth.logout');

/**
 * Example routes
 */
Route::get('/example/store', [PageController::class, 'exampleStore'])->name('example.store');
Route::resource('/admin/users', UserController::class)->middleware('auth:sanctum');
