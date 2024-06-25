<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SessionController;

use App\Http\Controllers\PageController;
use App\Http\Controllers\Example\ExampleController;
use App\Http\Controllers\Example\PostController;
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
Route::get('/example', [ExampleController::class, 'index'])->name('example.index');
Route::get('/example/about', [ExampleController::class, 'about'])->name('example.about');
Route::get('/example/store', [ExampleController::class, 'store'])->name('example.store');
Route::get('/example/signup', [ExampleController::class, 'signup'])->name('example.signup');

Route::resource('/example/posts', PostController::class);
