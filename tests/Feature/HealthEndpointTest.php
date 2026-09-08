<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Facades\Health;

/*
|--------------------------------------------------------------------------
| /health endpoint contract
|--------------------------------------------------------------------------
|
| Nothing covered this endpoint before, which is how two defects survived: the
| documented HEALTH_SECRET_TOKEN protection was never attached to the route, and
| the package default `always_send_fresh_results` was left true, so every request
| ran every check inline instead of serving the scheduled snapshot.
|
*/

it('is open when no secret token is configured', function () {
    config()->set('health.secret_token', null);

    $this->getJson('/health')->assertOk();
});

it('refuses a request with no token once a secret is configured', function () {
    config()->set('health.secret_token', 's3cret');

    $this->getJson('/health')->assertForbidden();
});

it('refuses a request with the wrong token', function () {
    config()->set('health.secret_token', 's3cret');

    $this->withHeader('X-Secret-Token', 'wrong')
        ->getJson('/health')
        ->assertForbidden();
});

it('accepts a request carrying the configured token', function () {
    config()->set('health.secret_token', 's3cret');

    $this->withHeader('X-Secret-Token', 's3cret')
        ->getJson('/health')
        ->assertOk();
});

it('serves the stored snapshot instead of running every check per request', function () {
    // always_send_fresh_results must stay false. The package default is true, and
    // the /health controller reads it regardless of whether the Oh Dear endpoint is
    // enabled — so true means an unauthenticated, unthrottled request runs a DB
    // round-trip, a Redis lookup, a `df` subprocess and a TCP probe, fires the
    // CheckEnded events that drive notification debouncing, and rewrites the shared
    // result cache. That is a denial-of-service lever, not a configuration taste.
    expect(config('health.oh_dear_endpoint.always_send_fresh_results'))->toBeFalse();

    $ran = 0;
    Artisan::command('health:check', function () use (&$ran) {
        $ran++;
    });

    config()->set('health.secret_token', null);
    $this->getJson('/health')->assertOk();

    expect($ran)->toBe(0);
});

it('gates the Horizon check on the queue driver, not on the package being installed', function () {
    // laravel/horizon is a hard composer requirement, so class_exists() is always
    // true — a fork that removed the horizon process from supervisord would have had
    // the check fail forever.
    $registered = collect(Health::registeredChecks())
        ->first(fn ($check) => $check instanceof HorizonCheck);

    expect($registered)->not->toBeNull();

    config()->set('queue.default', 'sync');
    expect($registered->shouldRun())->toBeFalse();

    config()->set('queue.default', 'redis');
    expect($registered->shouldRun())->toBeTrue();
});
