<?php

use App\Enums\ApiAbility;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API-key authenticated routes.
//
// The chain is deliberate and each link is load-bearing:
//   throttle:api   — a budget before anything expensive runs
//   auth:sanctum   — resolves the bearer to a user
//   api.key        — proves a REAL personal access token was presented, not a
//                    browser session (see EnsureApiKey: sanctum's `web` guard
//                    yields a TransientToken whose ability check passes for
//                    everything), and refuses a deactivated owner
//   abilities:...  — the key's own scope, which only means something once
//                    api.key has ruled the TransientToken out
Route::middleware(['throttle:api', 'auth:sanctum', 'api.key'])->group(function () {
    Route::middleware('abilities:'.ApiAbility::Read->value)->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user()->only(['id', 'name', 'email', 'role']);
        })->name('api.user');

        // A real collection endpoint, so a key's read scope can be exercised against
        // something with pagination and authorization rather than only the caller's
        // own record. Authorized per-request through UserPolicy: the ability says what
        // the key may do, the policy says whether its owner may.
        Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
    });
});
