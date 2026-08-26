<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

final class ReleaseController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()
            ->json(['version' => config('release.version')])
            ->header('Cache-Control', 'no-store, max-age=0');
    }
}
