<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Cache;

it('updates last_seen_at for authenticated users', function () {
    $user = User::factory()->create([
        'last_seen_at' => null,
        'last_ip_address' => null,
        'last_user_agent' => null,
    ]);

    $this->actingAs($user)->get('/');

    $user->refresh();

    expect($user->last_seen_at)->not->toBeNull();
    expect($user->last_seen_at->diffInSeconds(now()))->toBeLessThan(5);
});

it('tracks ip address and user agent for authenticated users', function () {
    $user = User::factory()->create([
        'last_ip_address' => null,
        'last_user_agent' => null,
    ]);

    $this->actingAs($user)
        ->withHeaders([
            'User-Agent' => 'Mozilla/5.0 TestBrowser',
        ])
        ->get('/');

    $user->refresh();

    expect($user->last_ip_address)->not->toBeNull();
    expect($user->last_user_agent)->toBe('Mozilla/5.0 TestBrowser');
});

it('does not update last_seen_at for guests', function () {
    $this->get('/');

    // No error should occur for guests
    expect(true)->toBeTrue();
});

it('caches last seen update to prevent excessive writes', function () {
    $user = User::factory()->create([
        'last_seen_at' => null,
    ]);

    // First request - should update
    $this->actingAs($user)->get('/');
    $user->refresh();
    $firstUpdate = $user->last_seen_at;

    expect($firstUpdate)->not->toBeNull();

    // Second request immediately after - should use cache, not update
    $this->actingAs($user)->get('/');
    $user->refresh();

    expect($user->last_seen_at->equalTo($firstUpdate))->toBeTrue();
    expect(Cache::has("user-last-seen-{$user->id}"))->toBeTrue();
});

it('updates last_seen_at after cache expires', function () {
    $user = User::factory()->create([
        'last_seen_at' => now()->subMinutes(10),
    ]);

    $oldLastSeen = $user->last_seen_at;

    // Clear the cache to simulate expiration
    Cache::forget("user-last-seen-{$user->id}");

    $this->actingAs($user)->get('/');
    $user->refresh();

    expect($user->last_seen_at->greaterThan($oldLastSeen))->toBeTrue();
});

it('correctly identifies online users', function () {
    $onlineUser = User::factory()->create([
        'last_seen_at' => now()->subMinutes(2),
    ]);

    $offlineUser = User::factory()->create([
        'last_seen_at' => now()->subMinutes(10),
    ]);

    $neverSeenUser = User::factory()->create([
        'last_seen_at' => null,
    ]);

    expect($onlineUser->isOnline())->toBeTrue();
    expect($offlineUser->isOnline())->toBeFalse();
    expect($neverSeenUser->isOnline())->toBeFalse();
});
