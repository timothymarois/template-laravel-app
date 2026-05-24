<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\TenantInvite;
use App\Models\User;
use App\Notifications\Tenancy\TenantInvitationNotification;
use App\Services\Tenancy\TenantInviteService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| TenantInviteService — full method coverage
|--------------------------------------------------------------------------
|
| Tests every public method: send (new + refresh + notification dispatched),
| findPendingByToken (found / not-found / expired / accepted),
| accept (happy path + idempotency + email mismatch + non-pending),
| decline, and revoke.
|
| TenantCreated is faked so per-tenant DB provisioning doesn't fire.
| Notifications are faked except where we assert dispatch.
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class]);
    Notification::fake();
    $this->service = app(TenantInviteService::class);
});

// ============================================================================
// send()
// ============================================================================

it('send() creates a new TenantInvite row', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'data' => ['name' => 'Acme']]);

    $invite = $this->service->send($tenant, 'newcomer@example.com');

    expect($invite)->toBeInstanceOf(TenantInvite::class);
    $this->assertDatabaseHas('tenant_invites', [
        'tenant_id' => $tenant->id,
        'email' => 'newcomer@example.com',
    ]);
});

it('send() defaults role to Member when not specified', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $invite = $this->service->send($tenant, 'newcomer@example.com');

    expect($invite->role)->toBe(TenantRole::Member);
});

it('send() honors an explicit role', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $invite = $this->service->send($tenant, 'admin@example.com', TenantRole::Admin);

    expect($invite->role)->toBe(TenantRole::Admin);
});

it('send() generates a fresh 64-char token', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $invite = $this->service->send($tenant, 'newcomer@example.com');

    expect($invite->token)->toBeString();
    expect(strlen($invite->token))->toBe(64);
});

it('send() sets expires_at to +14 days by default', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $invite = $this->service->send($tenant, 'newcomer@example.com');

    // Approximate — sub-second rounding makes exact equality flaky.
    expect((int) round(now()->diffInDays($invite->expires_at)))->toBe(14);
});

it('send() honors a custom expiry window', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $invite = $this->service->send($tenant, 'newcomer@example.com', expiresInDays: 3);

    expect((int) round(now()->diffInDays($invite->expires_at)))->toBe(3);
});

it('send() records the inviter when given', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $inviter = User::factory()->create();

    $invite = $this->service->send($tenant, 'newcomer@example.com', invitedBy: $inviter);

    expect($invite->invited_by_user_id)->toBe($inviter->id);
});

it('send() refreshes token + expiry when re-invited (upsert on tenant_id + email)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $first = $this->service->send($tenant, 'newcomer@example.com');
    $firstToken = $first->token;

    // Re-invite the same email — should refresh the existing row.
    $second = $this->service->send($tenant, 'newcomer@example.com', TenantRole::Admin);

    expect($second->id)->toBe($first->id); // same row
    expect($second->token)->not->toBe($firstToken); // new token
    expect($second->role)->toBe(TenantRole::Admin); // updated role
    expect(TenantInvite::where('tenant_id', $tenant->id)->where('email', 'newcomer@example.com')->count())->toBe(1);
});

it('send() clears accepted_at when re-inviting a previously accepted invite', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $invite->update(['accepted_at' => now()->subDay()]);

    $refreshed = $this->service->send($tenant, 'newcomer@example.com');

    expect($refreshed->accepted_at)->toBeNull();
});

it('send() dispatches a TenantInvitationNotification to the invitee', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'data' => ['name' => 'Acme']]);

    $this->service->send($tenant, 'newcomer@example.com');

    Notification::assertSentOnDemand(TenantInvitationNotification::class);
});

it('send() throws when the email belongs to an existing tenant member', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $existingMember = User::factory()->create(['email' => 'already@example.com']);
    $tenant->users()->attach($existingMember->id, ['role' => TenantRole::Member->value]);

    expect(fn () => $this->service->send($tenant, 'already@example.com'))
        ->toThrow(RuntimeException::class, 'already a member');
});

// ============================================================================
// findPendingByToken()
// ============================================================================

it('findPendingByToken() returns the invite for a valid pending token', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $found = $this->service->findPendingByToken($invite->token);

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($invite->id);
});

it('findPendingByToken() returns null for an unknown token', function () {
    $found = $this->service->findPendingByToken('this-token-does-not-exist');

    expect($found)->toBeNull();
});

it('findPendingByToken() returns null for an expired invite', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $invite->update(['expires_at' => now()->subDay()]);

    $found = $this->service->findPendingByToken($invite->token);

    expect($found)->toBeNull();
});

it('findPendingByToken() returns null for an already-accepted invite', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $invite->update(['accepted_at' => now()]);

    $found = $this->service->findPendingByToken($invite->token);

    expect($found)->toBeNull();
});

// ============================================================================
// accept()
// ============================================================================

it('accept() attaches the user to the tenant via pivot and marks invite accepted', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'newcomer@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com', TenantRole::Admin);

    $this->service->accept($invite, $user);

    $this->assertDatabaseHas('tenant_user', [
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'role' => 'admin',
    ]);
    expect($invite->fresh()->accepted_at)->not->toBeNull();
});

it('accept() does case-insensitive email comparison', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'NewComer@Example.COM']);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->service->accept($invite, $user);

    $this->assertDatabaseHas('tenant_user', [
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
    ]);
});

it('accept() throws when user email does not match invite email', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'someone-else@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    expect(fn () => $this->service->accept($invite, $user))
        ->toThrow(RuntimeException::class, 'Invite email does not match user email.');

    $this->assertDatabaseMissing('tenant_user', ['tenant_id' => $tenant->id, 'user_id' => $user->id]);
});

it('accept() throws when invite is already accepted', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'newcomer@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $invite->update(['accepted_at' => now()->subHour()]);

    expect(fn () => $this->service->accept($invite, $user))
        ->toThrow(RuntimeException::class, 'Invite is not pending');
});

it('accept() throws when invite is expired', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'newcomer@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $invite->update(['expires_at' => now()->subDay()]);

    expect(fn () => $this->service->accept($invite, $user))
        ->toThrow(RuntimeException::class, 'Invite is not pending');
});

it('accept() is idempotent w.r.t. an existing pivot row (no duplicate)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'newcomer@example.com']);

    // Manually craft the invite (not via send(), which would refuse to invite
    // an existing member). This simulates the race where a user accepts an
    // invite via two browser tabs simultaneously — the second tab still goes
    // through the accept() path with the same invite row.
    $invite = TenantInvite::create([
        'tenant_id' => $tenant->id,
        'email' => 'newcomer@example.com',
        'role' => TenantRole::Admin->value,
        'token' => Str::random(64),
        'expires_at' => now()->addDays(14),
    ]);
    // User is already a member from a prior attach (the "first tab" succeeded).
    $tenant->users()->attach($user->id, ['role' => TenantRole::Member->value, 'joined_at' => now()]);

    $this->service->accept($invite, $user);

    // No duplicate row + the invite is still marked accepted.
    expect(\DB::table('tenant_user')->where('tenant_id', $tenant->id)->where('user_id', $user->id)->count())->toBe(1);
    expect($invite->fresh()->accepted_at)->not->toBeNull();
});

// ============================================================================
// decline()
// ============================================================================

it('decline() deletes the invite row', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->service->decline($invite);

    $this->assertDatabaseMissing('tenant_invites', ['id' => $invite->id]);
});

it('decline() does not affect the tenant_user pivot', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->service->decline($invite);

    expect(\DB::table('tenant_user')->where('tenant_id', $tenant->id)->count())->toBe(0);
});

// ============================================================================
// revoke()
// ============================================================================

it('revoke() deletes the invite row (same as decline)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $invite = $this->service->send($tenant, 'newcomer@example.com');

    $this->service->revoke($invite);

    $this->assertDatabaseMissing('tenant_invites', ['id' => $invite->id]);
});

it('revoke() leaves accepted invites alone if called accidentally (since accepted are filtered by findPendingByToken)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $user = User::factory()->create(['email' => 'newcomer@example.com']);
    $invite = $this->service->send($tenant, 'newcomer@example.com');
    $this->service->accept($invite, $user);

    // Revoking after accept is allowed (deletes the invite row), but the
    // membership pivot row remains — revocation is about the invite, not
    // about retroactively kicking the member.
    $this->service->revoke($invite);

    $this->assertDatabaseMissing('tenant_invites', ['id' => $invite->id]);
    $this->assertDatabaseHas('tenant_user', ['tenant_id' => $tenant->id, 'user_id' => $user->id]);
});

// ============================================================================
// Cascade behavior
// ============================================================================

it('invites are cascade-deleted when their tenant is force-deleted', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $this->service->send($tenant, 'a@example.com');
    $this->service->send($tenant, 'b@example.com');
    expect(TenantInvite::where('tenant_id', $tenant->id)->count())->toBe(2);

    $tenant->forceDelete();

    expect(TenantInvite::where('tenant_id', $tenant->id)->count())->toBe(0);
});

it('inviter foreign key is nulled when the inviter user is deleted', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $inviter = User::factory()->create();
    $invite = $this->service->send($tenant, 'newcomer@example.com', invitedBy: $inviter);

    $inviter->delete();

    expect($invite->fresh()->invited_by_user_id)->toBeNull();
    // Invite itself is not deleted — invitee can still accept even though
    // the inviter is gone.
    expect($invite->fresh())->not->toBeNull();
});
