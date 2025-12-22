<?php

declare(strict_types=1);

use App\Models\User;

it('allows user to be created', function () {
    $authUser = User::factory()->create();

    $response = $this->actingAs($authUser)->post('/users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);

    $createdUser = User::where('email', 'new@example.com')->first();

    $response->assertRedirect(route('users.show', $createdUser));
    $this->assertDatabaseHas('users', [
        'name' => 'New User',
        'email' => 'new@example.com',
    ]);
});

it('allows user to be shown', function () {
    $authUser = User::factory()->create();
    $targetUser = User::factory()->create();

    $this->withoutVite();

    $response = $this->actingAs($authUser)->get("/users/{$targetUser->id}");

    $response->assertOk();
});

it('allows user to be updated', function () {
    $authUser = User::factory()->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($authUser)
        ->from("/users/{$targetUser->id}")
        ->put("/users/{$targetUser->id}", [
            'name' => 'Updated User',
            'email' => 'updated@example.com',
        ]);

    $response->assertRedirect("/users/{$targetUser->id}");
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
        ->from('/users')
        ->delete("/users/{$targetUser->id}");

    $response->assertRedirect('/users');
    $this->assertDatabaseMissing('users', [
        'id' => $targetUser->id,
    ]);
});
