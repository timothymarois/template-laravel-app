<?php

declare(strict_types=1);

use App\Enums\UserRole;

/*
|--------------------------------------------------------------------------
| UserRole enum — central-level role capability rules
|--------------------------------------------------------------------------
|
| Pure-unit tests — no DB, no framework boot needed. UserRole is the
| central counterpart to TenantRole: it describes a User's central-system
| authority (admin / non-admin), distinct from tenant-scoped authority.
|
*/

it('SuperAdmin backs to the string "admin" (matches existing role conventions)', function () {
    expect(UserRole::SuperAdmin->value)->toBe('admin');
});

it('User backs to the string "user"', function () {
    expect(UserRole::User->value)->toBe('user');
});

it('only SuperAdmin can manage all tenants', function () {
    expect(UserRole::SuperAdmin->canManageAllTenants())->toBeTrue();
    expect(UserRole::User->canManageAllTenants())->toBeFalse();
});

it('only SuperAdmin can impersonate', function () {
    expect(UserRole::SuperAdmin->canImpersonate())->toBeTrue();
    expect(UserRole::User->canImpersonate())->toBeFalse();
});

it('can be reconstructed from its backing value', function () {
    expect(UserRole::from('admin'))->toBe(UserRole::SuperAdmin);
    expect(UserRole::from('user'))->toBe(UserRole::User);
});
