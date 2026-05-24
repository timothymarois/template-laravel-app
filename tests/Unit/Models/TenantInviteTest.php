<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Models\TenantInvite;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| TenantInvite — predicate unit tests
|--------------------------------------------------------------------------
|
| Pure model-state tests. No DB. Constructs an in-memory model instance
| with date attributes set, then asserts the isExpired / isAccepted /
| isPending predicates behave consistently.
|
*/

function makeInvite(array $attrs = []): TenantInvite
{
    $invite = new TenantInvite;
    $invite->setRawAttributes(array_merge([
        'id' => 1,
        'tenant_id' => 'tenant-uuid',
        'email' => 'invitee@example.com',
        'role' => TenantRole::Member->value,
        'token' => 'abcd1234',
        'invited_by_user_id' => null,
        'expires_at' => Carbon::now()->addDays(14)->toDateTimeString(),
        'accepted_at' => null,
        'created_at' => Carbon::now()->toDateTimeString(),
        'updated_at' => Carbon::now()->toDateTimeString(),
    ], $attrs));

    return $invite;
}

// ============================================================================
// isExpired()
// ============================================================================

it('isExpired() returns false when expires_at is in the future', function () {
    expect(makeInvite()->isExpired())->toBeFalse();
});

it('isExpired() returns true when expires_at is in the past', function () {
    $invite = makeInvite(['expires_at' => Carbon::now()->subDay()->toDateTimeString()]);
    expect($invite->isExpired())->toBeTrue();
});

// ============================================================================
// isAccepted()
// ============================================================================

it('isAccepted() returns false when accepted_at is null', function () {
    expect(makeInvite()->isAccepted())->toBeFalse();
});

it('isAccepted() returns true when accepted_at is set', function () {
    $invite = makeInvite(['accepted_at' => Carbon::now()->toDateTimeString()]);
    expect($invite->isAccepted())->toBeTrue();
});

// ============================================================================
// isPending()
// ============================================================================

it('isPending() returns true for a fresh, unexpired, unaccepted invite', function () {
    expect(makeInvite()->isPending())->toBeTrue();
});

it('isPending() returns false when the invite has been accepted', function () {
    $invite = makeInvite(['accepted_at' => Carbon::now()->toDateTimeString()]);
    expect($invite->isPending())->toBeFalse();
});

it('isPending() returns false when the invite has expired', function () {
    $invite = makeInvite(['expires_at' => Carbon::now()->subDay()->toDateTimeString()]);
    expect($invite->isPending())->toBeFalse();
});

it('isPending() returns false when the invite is both expired AND accepted', function () {
    $invite = makeInvite([
        'expires_at' => Carbon::now()->subDay()->toDateTimeString(),
        'accepted_at' => Carbon::now()->subHours(2)->toDateTimeString(),
    ]);
    expect($invite->isPending())->toBeFalse();
});

// ============================================================================
// Casts
// ============================================================================

it('casts role to the TenantRole enum', function () {
    $invite = makeInvite(['role' => 'admin']);
    expect($invite->role)->toBe(TenantRole::Admin);
});

it('casts expires_at and accepted_at to Carbon instances', function () {
    $invite = makeInvite(['accepted_at' => Carbon::now()->toDateTimeString()]);
    expect($invite->expires_at)->toBeInstanceOf(Carbon::class);
    expect($invite->accepted_at)->toBeInstanceOf(Carbon::class);
});
