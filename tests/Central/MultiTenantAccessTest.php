<?php

declare(strict_types=1);

use App\Enums\TenantRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Events\TenantDeleted;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| Multi-tenant access — full service tests
|--------------------------------------------------------------------------
|
| Exercises the `tenant_user` pivot table end-to-end: attach/detach users,
| query tenants for a user, query users for a tenant, pivot data access,
| cascade delete, unique constraint, role enum behavior.
|
| TenantCreated / TenantDeleted are faked because they fire the package's
| CreateDatabase / DeleteDatabase pipeline which would try to provision a
| per-tenant SQLite file — irrelevant for pivot tests and slow under tests.
| The pivot logic and central-side queries don't need the pipeline.
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class, TenantDeleted::class]);
    expect(Schema::hasTable('tenant_user'))->toBeTrue(
        'tenant_user pivot must exist when tenancy is enabled.'
    );
});

it('attaches a user to a tenant via the pivot', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $tenant->users()->attach($user->id, [
        'role' => TenantRole::Owner->value,
        'joined_at' => now(),
    ]);

    $this->assertDatabaseHas('tenant_user', [
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
        'role' => 'owner',
    ]);
});

it('lists tenants a user belongs to', function () {
    $user = User::factory()->create();
    $tenantA = Tenant::create(['id' => (string) Str::uuid(), 'data' => ['name' => 'Acme']]);
    $tenantB = Tenant::create(['id' => (string) Str::uuid(), 'data' => ['name' => 'Globex']]);
    $tenantC = Tenant::create(['id' => (string) Str::uuid(), 'data' => ['name' => 'Nobody']]);

    $tenantA->users()->attach($user->id, ['role' => TenantRole::Owner->value]);
    $tenantB->users()->attach($user->id, ['role' => TenantRole::Member->value]);
    // tenantC has no pivot row.

    $tenantIds = $user->tenants->pluck('id')->all();

    expect($tenantIds)->toHaveCount(2);
    expect($tenantIds)->toContain($tenantA->id);
    expect($tenantIds)->toContain($tenantB->id);
    expect($tenantIds)->not->toContain($tenantC->id);
});

it('lists users a tenant has', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $owner = User::factory()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $outsider = User::factory()->create();

    $tenant->users()->attach($owner->id, ['role' => TenantRole::Owner->value]);
    $tenant->users()->attach($admin->id, ['role' => TenantRole::Admin->value]);
    $tenant->users()->attach($member->id, ['role' => TenantRole::Member->value]);

    $userIds = $tenant->users->pluck('id')->all();

    expect($userIds)->toHaveCount(3);
    expect($userIds)->toContain($owner->id);
    expect($userIds)->toContain($admin->id);
    expect($userIds)->toContain($member->id);
    expect($userIds)->not->toContain($outsider->id);
});

it('exposes pivot columns (role, joined_at) on the relationship', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $joinedAt = now()->subDay();

    $tenant->users()->attach($user->id, [
        'role' => TenantRole::Admin->value,
        'joined_at' => $joinedAt,
    ]);

    $reloaded = $tenant->users->first();
    expect($reloaded->pivot->role)->toBe('admin');
    expect($reloaded->pivot->joined_at)->not->toBeNull();
});

it('detaches a user from a tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->users()->attach($user->id, ['role' => TenantRole::Owner->value]);

    $tenant->users()->detach($user->id);

    $this->assertDatabaseMissing('tenant_user', [
        'user_id' => $user->id,
        'tenant_id' => $tenant->id,
    ]);
});

it('cascades pivot rows when a tenant is hard-deleted', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->users()->attach($user->id, ['role' => TenantRole::Owner->value]);

    expect(\DB::table('tenant_user')->where('tenant_id', $tenant->id)->count())->toBe(1);

    $tenant->forceDelete();

    expect(\DB::table('tenant_user')->where('tenant_id', $tenant->id)->count())->toBe(0);
});

it('cascades pivot rows when a user is deleted', function () {
    $user = User::factory()->create();
    $tenantA = Tenant::create(['id' => (string) Str::uuid()]);
    $tenantB = Tenant::create(['id' => (string) Str::uuid()]);
    $tenantA->users()->attach($user->id, ['role' => TenantRole::Owner->value]);
    $tenantB->users()->attach($user->id, ['role' => TenantRole::Member->value]);

    expect(\DB::table('tenant_user')->where('user_id', $user->id)->count())->toBe(2);

    $user->delete();

    expect(\DB::table('tenant_user')->where('user_id', $user->id)->count())->toBe(0);
});

it('enforces the unique (tenant_id, user_id) constraint', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $tenant->users()->attach($user->id, ['role' => TenantRole::Owner->value]);

    expect(fn () => \DB::table('tenant_user')->insert([
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'role' => TenantRole::Admin->value,
        'created_at' => now(),
        'updated_at' => now(),
    ]))->toThrow(QueryException::class);
});

it('supports one user belonging to many tenants', function () {
    $user = User::factory()->create();

    $tenants = collect(range(1, 5))->map(fn (int $i) => Tenant::create([
        'id' => (string) Str::uuid(),
        'data' => ['name' => "Tenant {$i}"],
    ]));

    foreach ($tenants as $tenant) {
        $tenant->users()->attach($user->id, ['role' => TenantRole::Member->value]);
    }

    expect($user->tenants)->toHaveCount(5);
});

it('supports one tenant having many users with different roles', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $users = User::factory()->count(3)->create();
    $tenant->users()->attach($users[0]->id, ['role' => TenantRole::Owner->value]);
    $tenant->users()->attach($users[1]->id, ['role' => TenantRole::Admin->value]);
    $tenant->users()->attach($users[2]->id, ['role' => TenantRole::Member->value]);

    $byRole = $tenant->users->keyBy(fn ($u) => $u->pivot->role);
    expect($byRole)->toHaveKey('owner');
    expect($byRole)->toHaveKey('admin');
    expect($byRole)->toHaveKey('member');
});

it('updates pivot data via updateExistingPivot', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->users()->attach($user->id, ['role' => TenantRole::Member->value]);

    $tenant->users()->updateExistingPivot($user->id, ['role' => TenantRole::Admin->value]);

    $this->assertDatabaseHas('tenant_user', [
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'role' => 'admin',
    ]);
});

it('uses Member as the default role when role is omitted on attach', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    // Attach without specifying role — the DB column default kicks in.
    $tenant->users()->attach($user->id);

    $this->assertDatabaseHas('tenant_user', [
        'tenant_id' => $tenant->id,
        'user_id' => $user->id,
        'role' => TenantRole::default()->value,
    ]);
});
