<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantMembershipService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| TenantMembershipService — full method coverage
|--------------------------------------------------------------------------
|
| Tests every public method: switchTo, leave, remove, changeRole,
| transferOwnership, isMember, roleOf. Authorization rules from
| TenantRole::canBeChangedBy / canBeRemovedBy are exercised via these
| public methods (the enum itself has its own unit tests).
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class]);
    $this->service = app(TenantMembershipService::class);
});

function makeTenantWith(array $roles): array
{
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $users = [];
    foreach ($roles as $key => $role) {
        $u = User::factory()->create();
        $tenant->users()->attach($u->id, ['role' => $role->value, 'joined_at' => now()]);
        $users[$key] = $u;
    }

    return [$tenant, $users];
}

// ============================================================================
// switchTo()
// ============================================================================

it('switchTo() returns the tenant URL when user is a member', function () {
    [$tenant, $users] = makeTenantWith(['m' => TenantRole::Member]);

    expect($this->service->switchTo($users['m'], $tenant))->toBe(tenant_url('/', $tenant));
});

it('switchTo() throws when user is not a member', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $stranger = User::factory()->create();

    expect(fn () => $this->service->switchTo($stranger, $tenant))
        ->toThrow(RuntimeException::class, 'not a member');
});

// ============================================================================
// leave()
// ============================================================================

it('leave() detaches a member from the tenant', function () {
    [$tenant, $users] = makeTenantWith(['m' => TenantRole::Member]);

    $this->service->leave($users['m'], $tenant);

    expect($this->service->isMember($users['m'], $tenant))->toBeFalse();
});

it('leave() detaches an admin from the tenant', function () {
    [$tenant, $users] = makeTenantWith(['a' => TenantRole::Admin]);

    $this->service->leave($users['a'], $tenant);

    expect($this->service->isMember($users['a'], $tenant))->toBeFalse();
});

it('leave() throws when the owner tries to leave', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner]);

    expect(fn () => $this->service->leave($users['o'], $tenant))
        ->toThrow(RuntimeException::class, 'owner cannot leave');

    expect($this->service->isMember($users['o'], $tenant))->toBeTrue();
});

it('leave() throws when the user is not a member at all', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $stranger = User::factory()->create();

    expect(fn () => $this->service->leave($stranger, $tenant))
        ->toThrow(RuntimeException::class, 'not a member');
});

// ============================================================================
// remove()
// ============================================================================

it('remove() lets an owner remove a member', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'm' => TenantRole::Member]);

    $this->service->remove($users['o'], $tenant, $users['m']);

    expect($this->service->isMember($users['m'], $tenant))->toBeFalse();
});

it('remove() lets an owner remove an admin', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'a' => TenantRole::Admin]);

    $this->service->remove($users['o'], $tenant, $users['a']);

    expect($this->service->isMember($users['a'], $tenant))->toBeFalse();
});

it('remove() lets an admin remove a member', function () {
    [$tenant, $users] = makeTenantWith(['a' => TenantRole::Admin, 'm' => TenantRole::Member]);

    $this->service->remove($users['a'], $tenant, $users['m']);

    expect($this->service->isMember($users['m'], $tenant))->toBeFalse();
});

it('remove() blocks an admin from removing another admin', function () {
    [$tenant, $users] = makeTenantWith(['a1' => TenantRole::Admin, 'a2' => TenantRole::Admin]);

    expect(fn () => $this->service->remove($users['a1'], $tenant, $users['a2']))
        ->toThrow(RuntimeException::class, 'cannot remove');

    expect($this->service->isMember($users['a2'], $tenant))->toBeTrue();
});

it('remove() blocks anyone from removing the owner', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'a' => TenantRole::Admin]);

    expect(fn () => $this->service->remove($users['a'], $tenant, $users['o']))
        ->toThrow(RuntimeException::class, 'cannot remove');

    expect($this->service->isMember($users['o'], $tenant))->toBeTrue();
});

it('remove() blocks a member from removing anyone', function () {
    [$tenant, $users] = makeTenantWith(['m1' => TenantRole::Member, 'm2' => TenantRole::Member]);

    expect(fn () => $this->service->remove($users['m1'], $tenant, $users['m2']))
        ->toThrow(RuntimeException::class, 'cannot remove');
});

it('remove() throws when actor is not a member', function () {
    [$tenant, $users] = makeTenantWith(['m' => TenantRole::Member]);
    $stranger = User::factory()->create();

    expect(fn () => $this->service->remove($stranger, $tenant, $users['m']))
        ->toThrow(RuntimeException::class, 'Actor is not a member');
});

it('remove() throws when target is not a member', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner]);
    $stranger = User::factory()->create();

    expect(fn () => $this->service->remove($users['o'], $tenant, $stranger))
        ->toThrow(RuntimeException::class, 'Target user is not a member');
});

// ============================================================================
// changeRole()
// ============================================================================

it('changeRole() lets an owner change a member to admin', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'm' => TenantRole::Member]);

    $this->service->changeRole($users['o'], $tenant, $users['m'], TenantRole::Admin);

    expect($this->service->roleOf($users['m'], $tenant))->toBe(TenantRole::Admin);
});

it('changeRole() blocks setting role to Owner (use transferOwnership)', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'm' => TenantRole::Member]);

    expect(fn () => $this->service->changeRole($users['o'], $tenant, $users['m'], TenantRole::Owner))
        ->toThrow(RuntimeException::class, 'transferOwnership');
});

it('changeRole() blocks an admin from changing the owner', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'a' => TenantRole::Admin]);

    expect(fn () => $this->service->changeRole($users['a'], $tenant, $users['o'], TenantRole::Member))
        ->toThrow(RuntimeException::class, 'cannot change');
});

it('changeRole() blocks an admin from changing another admin', function () {
    [$tenant, $users] = makeTenantWith(['a1' => TenantRole::Admin, 'a2' => TenantRole::Admin]);

    expect(fn () => $this->service->changeRole($users['a1'], $tenant, $users['a2'], TenantRole::Member))
        ->toThrow(RuntimeException::class, 'cannot change');
});

// ============================================================================
// transferOwnership()
// ============================================================================

it('transferOwnership() promotes new owner and demotes old to admin', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'a' => TenantRole::Admin]);

    $this->service->transferOwnership($users['o'], $tenant, $users['a']);

    expect($this->service->roleOf($users['a'], $tenant))->toBe(TenantRole::Owner);
    expect($this->service->roleOf($users['o'], $tenant))->toBe(TenantRole::Admin);
});

it('transferOwnership() works to promote a member directly', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'm' => TenantRole::Member]);

    $this->service->transferOwnership($users['o'], $tenant, $users['m']);

    expect($this->service->roleOf($users['m'], $tenant))->toBe(TenantRole::Owner);
});

it('transferOwnership() throws when caller is not the current owner', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner, 'a' => TenantRole::Admin, 'm' => TenantRole::Member]);

    expect(fn () => $this->service->transferOwnership($users['a'], $tenant, $users['m']))
        ->toThrow(RuntimeException::class, 'Only the current owner');
});

it('transferOwnership() throws when target is not already a member', function () {
    [$tenant, $users] = makeTenantWith(['o' => TenantRole::Owner]);
    $stranger = User::factory()->create();

    expect(fn () => $this->service->transferOwnership($users['o'], $tenant, $stranger))
        ->toThrow(RuntimeException::class, 'must already be a member');
});

// ============================================================================
// isMember() / roleOf()
// ============================================================================

it('isMember() returns true for an attached user', function () {
    [$tenant, $users] = makeTenantWith(['m' => TenantRole::Member]);

    expect($this->service->isMember($users['m'], $tenant))->toBeTrue();
});

it('isMember() returns false for a stranger', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $stranger = User::factory()->create();

    expect($this->service->isMember($stranger, $tenant))->toBeFalse();
});

it('roleOf() returns the TenantRole when user is a member', function () {
    [$tenant, $users] = makeTenantWith([
        'o' => TenantRole::Owner,
        'a' => TenantRole::Admin,
        'm' => TenantRole::Member,
    ]);

    expect($this->service->roleOf($users['o'], $tenant))->toBe(TenantRole::Owner);
    expect($this->service->roleOf($users['a'], $tenant))->toBe(TenantRole::Admin);
    expect($this->service->roleOf($users['m'], $tenant))->toBe(TenantRole::Member);
});

it('roleOf() returns null when user is not a member', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $stranger = User::factory()->create();

    expect($this->service->roleOf($stranger, $tenant))->toBeNull();
});
