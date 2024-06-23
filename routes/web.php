<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])
    ->name('home')
    ->middleware('guest');

Route::get('/about', [PageController::class, 'about'])
    ->name('about')
    ->middleware('guest');
