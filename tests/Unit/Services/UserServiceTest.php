<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_by_name_and_email(): void
    {
        $john = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $jane = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        User::factory()->create();

        $service = app(UserService::class);

        $byName = $service->buildQuery(['search' => 'John'])->get();
        $this->assertCount(1, $byName);
        $this->assertTrue($byName->contains($john));

        $byEmail = $service->buildQuery(['search' => 'jane@example.com'])->get();
        $this->assertCount(1, $byEmail);
        $this->assertTrue($byEmail->contains($jane));
    }
}
