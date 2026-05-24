<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use App\Tenancy\Listeners\ConditionalDeleteTenantDatabase;
use App\Tenancy\Listeners\MarkTenantReady;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Events\TenantDeleted;
use Stancl\Tenancy\Exceptions\DomainOccupiedByOtherTenantException;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| Tenant lifecycle — ready flag, failed flag, conditional DB delete
|--------------------------------------------------------------------------
|
| Covers the production safety controls a fork needs:
|   - ready flag (avoid users hitting half-provisioned tenants)
|   - failed flag (admins can find stuck tenants and clean up)
|   - soft-delete preserves the per-tenant DB (restorable)
|   - hard-delete drops the per-tenant DB (final cleanup)
|   - tenancy:purge-deleted command honors the grace window
|
| TenantCreated is faked in most tests so the package's CreateDatabase /
| MigrateDatabase pipeline doesn't fire — that pipeline writes real SQLite
| files to disk which we don't want in test runs. The listener under test
| (MarkTenantReady) is invoked directly.
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class, TenantDeleted::class]);
});

// ============================================================================
// ready / failed flags
// ============================================================================

it('a freshly-provisioned tenant is not ready by default', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);

    expect($tenant->isReady())->toBeFalse();
    expect($tenant->hasFailed())->toBeFalse();
});

it('markReady() flips ready=true (persisted)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);

    $tenant->markReady();

    expect($tenant->fresh()->isReady())->toBeTrue();
});

it('markFailed() flips failed=true and stores the reason', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);

    $tenant->markFailed('CreateDatabase: disk full');

    $reloaded = $tenant->fresh();
    expect($reloaded->hasFailed())->toBeTrue();
    expect($reloaded->getAttribute('failed_reason'))->toBe('CreateDatabase: disk full');
});

it('markFailed() without a reason still flips the flag', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);

    $tenant->markFailed();

    expect($tenant->fresh()->hasFailed())->toBeTrue();
});

// ============================================================================
// MarkTenantReady listener
// ============================================================================

it('MarkTenantReady listener flips ready=true when invoked', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $event = new TenantCreated($tenant);

    (new MarkTenantReady)->handle($event);

    expect($tenant->fresh()->isReady())->toBeTrue();
});

// ============================================================================
// ConditionalDeleteTenantDatabase — soft vs hard delete
// ============================================================================

it('soft delete does NOT drop the per-tenant DB (DB stays intact for restore)', function () {
    // We can't easily verify "DB still exists" without the actual SQLite file
    // (TenantCreated is faked so no real file was made). Instead, verify the
    // listener's logic directly: when the tenant is soft-deleted (trashed)
    // and NOT force-deleting, the listener early-returns without invoking
    // DeleteDatabase.

    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $tenant->delete(); // soft delete — sets deleted_at
    expect($tenant->trashed())->toBeTrue();

    $event = new TenantDeleted($tenant);

    // The listener early-returns when trashed && !force-deleting.
    // We assert no exception is thrown (the DeleteDatabase job would error
    // because the per-tenant SQLite file doesn't exist).
    expect(fn () => (new ConditionalDeleteTenantDatabase)->handle($event))->not->toThrow(Throwable::class);
});

it('hard delete listener invokes DeleteDatabase when tenant is not soft-trashed', function () {
    // Listener directly: tenant is NOT trashed (not soft-deleted) → the
    // soft-delete guard early-return doesn't fire → DeleteDatabase runs.
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $dbPath = database_path('tenant'.$tenant->id);
    touch($dbPath);

    (new ConditionalDeleteTenantDatabase)->handle(new TenantDeleted($tenant));

    expect(file_exists($dbPath))->toBeFalse();

    @unlink($dbPath);
});

// ============================================================================
// PurgeDeletedCommand
// ============================================================================

it('tenancy:purge-deleted reports nothing when no soft-deleted tenants exist', function () {
    $this->artisan('tenancy:purge-deleted', ['--hours' => 1])
        ->expectsOutputToContain('Nothing to purge')
        ->assertSuccessful();
});

it('tenancy:purge-deleted skips tenants younger than the grace window', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $tenant->delete(); // just soft-deleted

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72])
        ->expectsOutputToContain('Nothing to purge')
        ->assertSuccessful();

    expect(Tenant::withTrashed()->find($tenant->id))->not->toBeNull();
});

it('tenancy:purge-deleted force-deletes tenants past the grace window', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $tenant->delete();
    // Backdate deleted_at via raw DB update (VirtualColumn intercepts saveQuietly).
    DB::table('tenants')->where('id', $tenant->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72])
        ->expectsOutputToContain('Purging')
        ->assertSuccessful();

    // Verifies the tenant row is hard-deleted (no soft-delete fallback).
    // The per-tenant DB cleanup is tested separately via the
    // ConditionalDeleteTenantDatabase listener test above — combining both
    // here adds environment fragility (SQLite path resolution differs by setup).
    expect(Tenant::withTrashed()->find($tenant->id))->toBeNull();
});

it('tenancy:purge-deleted --dry-run reports without deleting', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $tenant->delete();
    DB::table('tenants')->where('id', $tenant->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72, '--dry-run' => true])
        ->expectsOutputToContain('[DRY RUN] Would purge')
        ->assertSuccessful();

    // Tenant still soft-deleted (NOT force-deleted)
    expect(Tenant::withTrashed()->find($tenant->id))->not->toBeNull();
});

// ============================================================================
// PurgeDeletedCommand — orphan-user sweep
// ============================================================================

it('removes a user left with zero tenants after their only tenant is purged', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Solo']);
    $user = User::factory()->create();
    $tenant->users()->attach($user->id, ['role' => 'owner']);

    $tenant->delete();
    DB::table('tenants')->where('id', $tenant->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72])->assertSuccessful();

    expect(User::find($user->id))->toBeNull();
});

it('keeps a user who still belongs to another tenant after purge', function () {
    $purged = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Purged']);
    $kept = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Kept']);
    $user = User::factory()->create();
    $purged->users()->attach($user->id, ['role' => 'admin']);
    $kept->users()->attach($user->id, ['role' => 'owner']);

    $purged->delete();
    DB::table('tenants')->where('id', $purged->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72])->assertSuccessful();

    expect(User::find($user->id))->not->toBeNull();
});

it('exempts SuperAdmins from the orphan-user sweep', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'AdminOwned']);
    $admin = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $tenant->users()->attach($admin->id, ['role' => 'owner']);

    $tenant->delete();
    DB::table('tenants')->where('id', $tenant->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72])->assertSuccessful();

    expect(User::find($admin->id))->not->toBeNull();
});

it('does NOT touch users who were never members of the purged tenants', function () {
    $purged = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Purged']);
    $owner = User::factory()->create();
    $purged->users()->attach($owner->id, ['role' => 'owner']);
    // Bystander: brand-new user with no tenant memberships at all.
    $bystander = User::factory()->create();

    $purged->delete();
    DB::table('tenants')->where('id', $purged->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72])->assertSuccessful();

    // Owner is removed (was a member, now has zero tenants).
    expect(User::find($owner->id))->toBeNull();
    // Bystander is preserved (never a member of the purged tenant).
    expect(User::find($bystander->id))->not->toBeNull();
});

it('--keep-orphan-users skips the orphan sweep', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Solo']);
    $user = User::factory()->create();
    $tenant->users()->attach($user->id, ['role' => 'owner']);

    $tenant->delete();
    DB::table('tenants')->where('id', $tenant->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72, '--keep-orphan-users' => true])->assertSuccessful();

    expect(User::find($user->id))->not->toBeNull();
});

it('--dry-run does not remove orphan users', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Solo']);
    $user = User::factory()->create();
    $tenant->users()->attach($user->id, ['role' => 'owner']);

    $tenant->delete();
    DB::table('tenants')->where('id', $tenant->id)
        ->update(['deleted_at' => now()->subHours(100)]);

    $this->artisan('tenancy:purge-deleted', ['--hours' => 72, '--dry-run' => true])->assertSuccessful();

    // Tenant still present (dry-run skipped destruction)
    expect(Tenant::withTrashed()->find($tenant->id))->not->toBeNull();
    // User still present
    expect(User::find($user->id))->not->toBeNull();
});

// ============================================================================
// Duplicate prevention
// ============================================================================

it('cannot insert two pivot rows for the same (tenant_id, user_id)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'Test']);
    $user = User::factory()->create();
    $tenant->users()->attach($user->id, ['role' => 'member']);

    // The unique index on (tenant_id, user_id) prevents duplicates at the DB layer.
    expect(fn () => DB::table('tenant_user')->insert([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'role' => 'admin',
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(QueryException::class);
});

it('cannot create two domains with the same slug', function () {
    $tenantA = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'A']);
    $tenantA->domains()->create(['domain' => 'unique-slug']);

    $tenantB = Tenant::create(['id' => (string) Str::uuid(), 'name' => 'B']);

    // The package wraps the DB-level UNIQUE violation into a friendlier
    // DomainOccupiedByOtherTenantException — both signal the same thing.
    expect(fn () => $tenantB->domains()->create(['domain' => 'unique-slug']))
        ->toThrow(DomainOccupiedByOtherTenantException::class);
});
