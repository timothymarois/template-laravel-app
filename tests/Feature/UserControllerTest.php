<?php

declare(strict_types=1);

use App\Models\User;

it('allows user to be created', function () {
    $authUser = User::factory()->create();

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
    $authUser = User::factory()->create();
    $targetUser = User::factory()->create();

    $this->withoutVite();

    $response = $this->actingAs($authUser)->get("/admin/users/{$targetUser->id}");

    $response->assertOk();
});

it('allows user to be updated', function () {
    $authUser = User::factory()->create();
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
    $authUser = User::factory()->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($authUser)
        ->from('/admin/users')
        ->delete("/admin/users/{$targetUser->id}");

    $response->assertRedirect('/admin/users');
    $this->assertDatabaseMissing('users', [
        'id' => $targetUser->id,
    ]);
});
