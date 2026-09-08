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

    User::factory()->create([
        'name' => 'Alex Smith',
        'email' => 'alex@example.com',
    ]);

    $service = app(UserService::class);

    $byName = $service->buildQuery(['search' => 'John'])->get();
    expect($byName)->toHaveCount(1)
        ->and($byName->contains($john))->toBeTrue();

    $byEmail = $service->buildQuery(['search' => 'jane@example.com'])->get();
    expect($byEmail)->toHaveCount(1)
        ->and($byEmail->contains($jane))->toBeTrue();
});

it('reports admin status from the role, not a string comparison', function () {
    expect(User::factory()->admin()->make()->isAdmin())->toBeTrue();
    expect(User::factory()->make()->isAdmin())->toBeFalse();
});
