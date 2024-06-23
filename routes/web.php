<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ExampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'index'])->name('index');

/**
 * Example routes
 *
 */
Route::get('/example', [ExampleController::class, 'index'])->name('example.index');
Route::get('/example/about', [ExampleController::class, 'about'])->name('example.about');
Route::get('/example/store', [ExampleController::class, 'store'])->name('example.store');
