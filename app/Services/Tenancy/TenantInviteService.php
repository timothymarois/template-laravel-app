<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\TenantInvite;
use App\Models\User;
use App\Notifications\Tenancy\TenantInvitationNotification;
use Illuminate\Contracts\Notifications\Dispatcher as NotificationDispatcher;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Send, accept, decline, and revoke tenant invitations.
 *
 * Design notes:
 *  - Tokens are 64-char random strings (Str::random(64)). The URL signing is
 *    intentionally not used here — explicit tokens let us track expiry,
 *    revocation, and single-use independently from URL signatures.
 *  - send() upserts on (tenant_id, email): resending an invite to the same
 *    address refreshes the token + expiry instead of creating a duplicate row.
 *  - accept() is idempotent w.r.t. the pivot — if the user is already a
 *    member (manually added or accepted via another invite), it returns
 *    silently. The invite row is still marked accepted.
 *
 * Construct via DI: Laravel auto-resolves; or inject in a controller via
 * constructor injection per AGENTS.md.
 */
class TenantInviteService
{
    public function __construct(
        private readonly ConnectionResolverInterface $db,
        private readonly NotificationDispatcher $notifications,
    ) {}

    /**
     * Issue (or refresh) an invitation for $email to join $tenant with $role.
     *
     * Returns the persisted TenantInvite. Sends the notification email
     * synchronously by default; callers wanting async should wrap the
     * notification in a queued job.
     */
    public function send(
        Tenant $tenant,
        string $email,
        TenantRole $role = TenantRole::Member,
        ?User $invitedBy = null,
        ?int $expiresInDays = null,
    ): TenantInvite {
        // Refuse to re-invite a user who's already an active member. Without
        // this guard, a fresh invite link gets emailed for someone whose
        // pivot row already exists — confusing UX and a minor security smell
        // (the email implies they're not a member when they are).
        $existingMember = $tenant->users()->where('email', $email)->exists();
        if ($existingMember) {
            throw new RuntimeException(
                "Cannot invite {$email} — they are already a member of this tenant.",
            );
        }

        $expires = now()->addDays($expiresInDays ?? 14);

        $invite = TenantInvite::updateOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $email],
            [
                'role' => $role->value,
                'token' => Str::random(64),
                'invited_by_user_id' => $invitedBy?->id,
                'expires_at' => $expires,
                'accepted_at' => null,
            ],
        );

        $recipient = (new AnonymousNotifiable)->route('mail', $email);
        $this->notifications->send($recipient, new TenantInvitationNotification($invite, $tenant));

        return $invite;
    }

    /**
     * Find an invite by its token. Returns null if the token doesn't match
     * any row OR if the matched row is expired / already accepted (caller
     * gets a single null to handle in all three "not pending" cases).
     */
    public function findPendingByToken(string $token): ?TenantInvite
    {
        $invite = TenantInvite::where('token', $token)->first();

        if ($invite === null || ! $invite->isPending()) {
            return null;
        }

        return $invite;
    }

    /**
     * Accept the invite for $user. The user's email MUST match the invite's
     * email — caller is responsible for resolving the User by the invite's
     * email (typically via the auth flow on the central domain).
     *
     * Idempotent: if the user is already a member of the tenant, the invite
     * row is still marked accepted but the pivot row is not duplicated.
     */
    public function accept(TenantInvite $invite, User $user): void
    {
        if (! hash_equals(strtolower($invite->email), strtolower($user->email))) {
            throw new RuntimeException('Invite email does not match user email.');
        }

        if (! $invite->isPending()) {
            throw new RuntimeException('Invite is not pending (expired or already accepted).');
        }

        $this->db->connection(config('tenancy.database.central_connection'))
            ->transaction(function () use ($invite, $user): void {
                /** @var Tenant $tenant */
                $tenant = $invite->tenant;

                // Idempotent attach — skip if already a member, otherwise insert.
                if (! $tenant->users()->where('user_id', $user->id)->exists()) {
                    $tenant->users()->attach($user->id, [
                        'role' => $invite->role->value,
                        'joined_at' => now(),
                    ]);
                }

                $invite->update(['accepted_at' => now()]);
            });
    }

    /**
     * Decline (= delete) the invite. No record kept — invitee can be re-invited
     * later if needed.
     */
    public function decline(TenantInvite $invite): void
    {
        $invite->delete();
    }

    /**
     * Revoke (= delete) an invite — typically called by the tenant owner/admin
     * who originally sent it. Same effect as decline().
     */
    public function revoke(TenantInvite $invite): void
    {
        $invite->delete();
    }
}
