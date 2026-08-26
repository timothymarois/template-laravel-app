<?php

declare(strict_types=1);

use App\Tenancy\NullExistingDataMigrator;

/*
|--------------------------------------------------------------------------
| NullExistingDataMigrator — error-message tests
|--------------------------------------------------------------------------
|
| The template's default binding for ExistingDataMigrator. Every method
| throws with an identical, actionable error pointing at the migration
| guide. These tests pin that message so an accidental refactor doesn't
| silently change what fork operators see.
|
*/

it('tablesToMoveToTenant() throws with a helpful message', function () {
    expect(fn () => (new NullExistingDataMigrator)->tablesToMoveToTenant())
        ->toThrow(RuntimeException::class, 'No ExistingDataMigrator implementation is bound');
});

it('userMapper() throws with a helpful message', function () {
    expect(fn () => (new NullExistingDataMigrator)->userMapper())
        ->toThrow(RuntimeException::class, 'No ExistingDataMigrator implementation is bound');
});

it('tenantProvisioner() throws with a helpful message', function () {
    expect(fn () => (new NullExistingDataMigrator)->tenantProvisioner())
        ->toThrow(RuntimeException::class, 'No ExistingDataMigrator implementation is bound');
});

it('every method points at the migrating doc', function () {
    foreach (['tablesToMoveToTenant', 'userMapper', 'tenantProvisioner'] as $method) {
        try {
            (new NullExistingDataMigrator)->{$method}();
            expect(false)->toBeTrue("Expected {$method} to throw");
        } catch (RuntimeException $e) {
            expect($e->getMessage())->toContain('docs/guides/tenancy-migrating.md');
        }
    }
});
