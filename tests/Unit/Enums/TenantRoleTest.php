<?php

declare(strict_types=1);

use App\Enums\TenantRole;

/*
|--------------------------------------------------------------------------
| TenantRole enum — capability rules
|--------------------------------------------------------------------------
|
| Defines who can do what within a tenant. These tests pin the default
| permission model:
|
|   - Owner cannot be demoted or removed (only via ownership transfer).
|   - Admins can manage members, but cannot promote/demote/remove other admins
|     (only owners can).
|   - Members cannot manage anyone.
|
| Pure-unit tests — no DB, no framework boot needed. Live in Unit/ rather
| than Central/ because they don't depend on tenancy being enabled.
|
*/

it('default role is Member', function () {
    expect(TenantRole::default())->toBe(TenantRole::Member);
});

it('owners and admins can manage members; members cannot', function () {
    expect(TenantRole::Owner->canManageMembers())->toBeTrue();
    expect(TenantRole::Admin->canManageMembers())->toBeTrue();
    expect(TenantRole::Member->canManageMembers())->toBeFalse();
});

it('only owners can transfer ownership', function () {
    expect(TenantRole::Owner->canTransferOwnership())->toBeTrue();
    expect(TenantRole::Admin->canTransferOwnership())->toBeFalse();
    expect(TenantRole::Member->canTransferOwnership())->toBeFalse();
});

it("an owner's role cannot be changed by anyone", function () {
    expect(TenantRole::Owner->canBeChangedBy(TenantRole::Owner))->toBeFalse();
    expect(TenantRole::Owner->canBeChangedBy(TenantRole::Admin))->toBeFalse();
    expect(TenantRole::Owner->canBeChangedBy(TenantRole::Member))->toBeFalse();
});

it('an owner cannot be removed by anyone', function () {
    expect(TenantRole::Owner->canBeRemovedBy(TenantRole::Owner))->toBeFalse();
    expect(TenantRole::Owner->canBeRemovedBy(TenantRole::Admin))->toBeFalse();
    expect(TenantRole::Owner->canBeRemovedBy(TenantRole::Member))->toBeFalse();
});

it('an admin can only be changed by an owner (not by another admin or a member)', function () {
    expect(TenantRole::Admin->canBeChangedBy(TenantRole::Owner))->toBeTrue();
    expect(TenantRole::Admin->canBeChangedBy(TenantRole::Admin))->toBeFalse();
    expect(TenantRole::Admin->canBeChangedBy(TenantRole::Member))->toBeFalse();
});

it('an admin can only be removed by an owner', function () {
    expect(TenantRole::Admin->canBeRemovedBy(TenantRole::Owner))->toBeTrue();
    expect(TenantRole::Admin->canBeRemovedBy(TenantRole::Admin))->toBeFalse();
    expect(TenantRole::Admin->canBeRemovedBy(TenantRole::Member))->toBeFalse();
});

it('a member can be changed by owners and admins, not by other members', function () {
    expect(TenantRole::Member->canBeChangedBy(TenantRole::Owner))->toBeTrue();
    expect(TenantRole::Member->canBeChangedBy(TenantRole::Admin))->toBeTrue();
    expect(TenantRole::Member->canBeChangedBy(TenantRole::Member))->toBeFalse();
});

it('a member can be removed by owners and admins, not by other members', function () {
    expect(TenantRole::Member->canBeRemovedBy(TenantRole::Owner))->toBeTrue();
    expect(TenantRole::Member->canBeRemovedBy(TenantRole::Admin))->toBeTrue();
    expect(TenantRole::Member->canBeRemovedBy(TenantRole::Member))->toBeFalse();
});

it('a member cannot remove themselves (members never have remove power)', function () {
    // Same code path as "member cannot remove another member", but documenting
    // the self-removal intent explicitly. Forks that want self-leave semantics
    // should add a separate `canLeaveTenantBy()` method rather than relax this.
    expect(TenantRole::Member->canBeRemovedBy(TenantRole::Member))->toBeFalse();
});

it('exposes string values matching the DB pivot column', function () {
    expect(TenantRole::Owner->value)->toBe('owner');
    expect(TenantRole::Admin->value)->toBe('admin');
    expect(TenantRole::Member->value)->toBe('member');
});

it('can be reconstructed from a string value', function () {
    expect(TenantRole::from('owner'))->toBe(TenantRole::Owner);
    expect(TenantRole::from('admin'))->toBe(TenantRole::Admin);
    expect(TenantRole::from('member'))->toBe(TenantRole::Member);
});
