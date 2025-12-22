<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\Models\UserService;

it('searches users by name and email', function () {
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
    expect($byName)->toHaveCount(1)
        ->and($byName->contains($john))->toBeTrue();

    $byEmail = $service->buildQuery(['search' => 'jane@example.com'])->get();
    expect($byEmail)->toHaveCount(1)
        ->and($byEmail->contains($jane))->toBeTrue();
});
