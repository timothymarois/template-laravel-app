<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use InteractsWithViews, RefreshDatabase;

    public function test_user_can_be_created(): void
    {
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
    }

    public function test_user_can_be_shown(): void
    {
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->withoutVite();

        $response = $this->actingAs($authUser)->get("/users/{$targetUser->id}");

        $response->assertOk();
    }

    public function test_user_can_be_updated(): void
    {
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
    }

    public function test_user_can_be_deleted(): void
    {
        $authUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($authUser)
            ->from('/users')
            ->delete("/users/{$targetUser->id}");

        $response->assertRedirect('/users');
        $this->assertDatabaseMissing('users', [
            'id' => $targetUser->id,
        ]);
    }
}
