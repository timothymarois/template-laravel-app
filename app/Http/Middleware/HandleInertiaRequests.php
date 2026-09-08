<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Allow-listed on purpose. Sharing the model serializes every column not
            // in $hidden into page props AND into history.state on every visit —
            // including columns a fork adds later without thinking about this file.
            'user' => $request->user()?->only(['id', 'name', 'email', 'role']),
            // One-request session messages. Without this share every ->with('status')
            // and ->with('error') in the app is written to the session and dropped on
            // the floor — the UI has no way to read it.
            // Absolute base URL for the document head. The SSR bundle has no
            // window, so canonical/og:url/og:image must come from the server or
            // they ship empty in exactly the render crawlers read.
            'appUrl' => rtrim((string) config('app.url'), '/'),
            'seo' => [
                'siteName' => config('seo.site_name'),
                'description' => config('seo.description'),
                'image' => config('seo.image'),
            ],
            'flash' => [
                'status' => $request->session()->get('status'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }
}
