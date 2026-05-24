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
            'user' => $request->user(),
            ...$this->tenancyShared(),
        ]);
    }

    /**
     * Tenancy props are emitted only when a tenant is active. In the disabled
     * state (and in central-domain requests when tenancy is enabled), the
     * helper returns an empty array — Inertia props look identical to v4.4.0.
     *
     * @return array<string, mixed>
     */
    protected function tenancyShared(): array
    {
        $tenant = tenant();

        if ($tenant === null) {
            return [];
        }

        return [
            'currentTenant' => $tenant,
            'tenantUser' => tenant_user(),
        ];
    }
}
