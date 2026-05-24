<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\Tenancy\TenantInviteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public-facing invite acceptance endpoints, served on the central domain.
 *
 * Routes:
 *   GET    /invites/{token}          show()    — landing page (logged-in or out)
 *   POST   /invites/{token}/accept   accept()  — auth required + email match
 *   POST   /invites/{token}/decline  decline() — auth not required
 *
 * The Vue pages this controller renders (`invites/Show`, `invites/Expired`,
 * `invites/Mismatch`) are NOT shipped by the template — they're fork-
 * specific UI. The controller alone is enough to wire up; forks add the
 * Inertia pages to match their design system.
 */
class InviteController extends Controller
{
    public function __construct(
        private readonly TenantInviteService $invites,
    ) {}

    public function show(string $token): Response
    {
        $invite = $this->invites->findPendingByToken($token);

        if ($invite === null) {
            return Inertia::render('invites/Expired');
        }

        /** @var Tenant $tenant */
        $tenant = $invite->tenant;

        return Inertia::render('invites/Show', [
            'token' => $invite->token,
            'email' => $invite->email,
            'role' => $invite->role->value,
            'tenant' => [
                'id' => $tenant->id,
                'name' => (string) ($tenant->data['name'] ?? $tenant->id),
            ],
            'expires_at' => $invite->expires_at->toIso8601String(),
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse|Response
    {
        $invite = $this->invites->findPendingByToken($token);

        if ($invite === null) {
            return Inertia::render('invites/Expired');
        }

        $user = $request->user();
        if ($user === null) {
            // Not authenticated — bounce to login with the invite token preserved
            // so the user can come back and accept after authenticating.
            return redirect()->route('login', ['invite' => $token]);
        }

        // Email mismatch: the invite was sent to a different address. Refuse
        // to silently attach — surface the mismatch so the user knows to log
        // out and re-authenticate as the invited address.
        if (! hash_equals(strtolower($invite->email), strtolower((string) $user->email))) {
            return Inertia::render('invites/Mismatch', [
                'invited_email' => $invite->email,
                'current_email' => $user->email,
            ]);
        }

        $this->invites->accept($invite, $user);

        // Re-resolve via Tenant::withTrashed() so a concurrent soft-delete
        // between accept() and redirect() can't yield a null tenant_url.
        // BelongsTo::withTrashed() exists at runtime but isn't visible on the
        // relation type — fetch via the model directly.
        $tenant = Tenant::withTrashed()->find($invite->tenant_id);

        if ($tenant === null) {
            return Inertia::render('invites/Expired');
        }

        return redirect()->away(tenant_url('/', $tenant));
    }

    public function decline(string $token): RedirectResponse|Response
    {
        $invite = $this->invites->findPendingByToken($token);

        if ($invite === null) {
            return Inertia::render('invites/Expired');
        }

        $this->invites->decline($invite);

        return redirect()->route('home')->with('status', 'Invitation declined.');
    }
}
