<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class InertiaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Response::macro('inertiaOrJson', function ($component, $props = []) {
            if (request()->wantsJson()) {
                return response()->json($props);
            } else {
                return Inertia::render($component, $props);
            }
        });
    }
}
