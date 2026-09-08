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

it('exposes a default role used by the migration and the factory', function () {
    expect(UserRole::default())->toBe(UserRole::User);
});

it('labels every case', function () {
    // A missing arm in label()'s match would be a runtime error, not a warning.
    foreach (UserRole::cases() as $role) {
        expect($role->label())->toBeString()->not->toBeEmpty();
    }
});

it('answers every capability for every case', function () {
    // Guards the rule that capabilities, not case comparisons, are the contract:
    // a new case must make a decision about each one.
    foreach (UserRole::cases() as $role) {
        expect($role->canManageAllUsers())->toBeBool()
            ->and($role->canAccessAdmin())->toBeBool()
            ->and($role->canImpersonate())->toBeBool();
    }
});

it('only SuperAdmin can reach the admin surface', function () {
    expect(UserRole::SuperAdmin->canAccessAdmin())->toBeTrue();
    expect(UserRole::User->canAccessAdmin())->toBeFalse();
});
