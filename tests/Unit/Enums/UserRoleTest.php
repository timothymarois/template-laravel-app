<?php

declare(strict_types=1);

use App\Enums\UserRole;

/*
|--------------------------------------------------------------------------
| UserRole enum — role capability rules
|--------------------------------------------------------------------------
|
| Pure-unit tests — no DB, no framework boot needed. UserRole describes a
| User's application-level authority (admin / non-admin).
|
*/

it('SuperAdmin backs to the string "admin" (matches existing role conventions)', function () {
    expect(UserRole::SuperAdmin->value)->toBe('admin');
});

it('User backs to the string "user"', function () {
    expect(UserRole::User->value)->toBe('user');
});

it('only SuperAdmin can manage all users', function () {
    expect(UserRole::SuperAdmin->canManageAllUsers())->toBeTrue();
    expect(UserRole::User->canManageAllUsers())->toBeFalse();
});

it('only SuperAdmin can impersonate', function () {
    expect(UserRole::SuperAdmin->canImpersonate())->toBeTrue();
    expect(UserRole::User->canImpersonate())->toBeFalse();
});

it('can be reconstructed from its backing value', function () {
    expect(UserRole::from('admin'))->toBe(UserRole::SuperAdmin);
    expect(UserRole::from('user'))->toBe(UserRole::User);
});
