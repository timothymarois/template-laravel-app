<?php

declare(strict_types=1);

use App\Models\User;

it('returns a successful response for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});
