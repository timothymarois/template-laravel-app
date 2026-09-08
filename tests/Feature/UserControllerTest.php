<?php

declare(strict_types=1);

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Admin user management
|--------------------------------------------------------------------------
|
| The acting user is an operator in every case below. The `admin` route prefix
| is not authorization — it carries `auth:sanctum` only — so the refusal cases
| at the foot of this file are what prove the UserPolicy is actually wired.
|
*/

it('allows user to be created', function () {
    $authUser = User::factory()->admin()->create();

    $response = $this->actingAs($authUser)->post('/admin/users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);

    $createdUser = User::where('email', 'new@example.com')->first();

    $response->assertRedirect(route('admin.users.show', $createdUser));
    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);
});

it('allows user to be shown', function () {
    $authUser = User::factory()->admin()->create();
    $targetUser = User::factory()->create();

    $this->withoutVite();

    $response = $this->actingAs($authUser)->get("/admin/users/{$targetUser->id}");

    $response->assertOk();
});

it('allows user to be updated', function () {
    $authUser = User::factory()->admin()->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($authUser)
        ->from("/admin/users/{$targetUser->id}")
        ->put("/admin/users/{$targetUser->id}", [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
        ]);

    $response->assertRedirect("/admin/users/{$targetUser->id}");
    $this->assertDatabaseHas('users', [
        'id' => $targetUser->id,
        'name' => 'Updated User',
        'email' => 'updated@example.com',
    ]);
});

it('allows user to be deleted', function () {
    $authUser = User::factory()->admin()->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($authUser)
        ->from('/admin/users')
        ->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect('/admin/users');
    $this->assertDatabaseMissing('users', [
        'id' => $targetUser->id,
    ]);
});

it('does not crash when filters query param is a scalar', function () {
    $authUser = User::factory()->admin()->create();

    $this->withoutVite();

    $response = $this->actingAs($authUser)->get('/admin/users/table?filters=hacked');

    $response->assertOk();
});

it('does not crash on the index route when filters query param is a scalar', function () {
    $authUser = User::factory()->admin()->create();

    $this->withoutVite();

    $response = $this->actingAs($authUser)->get('/admin/users?filters=hacked');

    $response->assertOk();
});

it('does not crash when a scalar filters value is persisted via session and reloaded', function () {
    $authUser = User::factory()->admin()->create();

    $this->withoutVite();

    $this->actingAs($authUser)->post('/admin/users/filters', ['filters' => 'hacked']);

    $response = $this->actingAs($authUser)->get('/admin/users');

    $response->assertOk();
});

it('does not crash when viewFields query param is a scalar', function () {
    $authUser = User::factory()->admin()->create();

    $this->withoutVite();

    $response = $this->actingAs($authUser)->get('/admin/users/table?viewFields=hacked');

    $response->assertOk();
});

it('refuses a non-admin every admin user route', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->withoutVite();

    $this->actingAs($user)->get('/admin/users')->assertForbidden();
    $this->actingAs($user)->get('/admin/users/table')->assertForbidden();
    $this->actingAs($user)->post('/admin/users/filters')->assertForbidden();
    $this->actingAs($user)->get("/admin/users/{$target->id}")->assertForbidden();
    $this->actingAs($user)->post('/admin/users', [
        'name' => 'Nope',
        'email' => 'nope@example.com',
    ])->assertForbidden();
    $this->actingAs($user)->put("/admin/users/{$target->id}", [
        'name' => 'Nope',
        'email' => 'nope@example.com',
    ])->assertForbidden();
    $this->actingAs($user)->delete("/admin/users/{$target->id}")->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'nope@example.com']);
    $this->assertDatabaseHas('users', ['id' => $target->id]);
});

it('refuses a guest every admin user route', function () {
    $target = User::factory()->create();

    $this->get('/admin/users')->assertRedirect(route('login'));
    $this->delete("/admin/users/{$target->id}")->assertRedirect(route('login'));
});

it('does not let an operator delete their own account', function () {
    // The one delete no other operator can undo, and almost always a misclick.
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->delete("/admin/users/{$admin->id}")->assertForbidden();

    $this->assertDatabaseHas('users', ['id' => $admin->id]);
});
