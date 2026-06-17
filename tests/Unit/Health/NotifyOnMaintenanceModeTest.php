<?php

declare(strict_types=1);

use App\Health\DiscordWebhook;
use App\Health\Listeners\NotifyOnMaintenanceMode;
use Illuminate\Foundation\Events\MaintenanceModeDisabled;
use Illuminate\Foundation\Events\MaintenanceModeEnabled;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| NotifyOnMaintenanceMode — Discord pings on artisan down / up
|--------------------------------------------------------------------------
|
| Covers the enter (amber) and leave (green) pings, the webhook + enabled gates,
| and that the listener is actually wired to the framework's MaintenanceMode
| events so `artisan down` / `artisan up` trigger it.
|
*/

const MAINTENANCE_WEBHOOK = 'https://discord.com/api/webhooks/1/abc';

beforeEach(function () {
    config()->set('health.notifications.enabled', true);
    config()->set('health.notifications.discord.webhook_url', MAINTENANCE_WEBHOOK);
    Http::fake();
});

it('pings amber when entering maintenance mode', function () {
    (new NotifyOnMaintenanceMode)->enabled();

    Http::assertSent(function ($request) {
        $body = $request->data();

        return str_contains($body['content'], 'entered maintenance mode')
            && $body['embeds'][0]['color'] === DiscordWebhook::COLOR_WARNING;
    });
});

it('pings green when leaving maintenance mode', function () {
    (new NotifyOnMaintenanceMode)->disabled();

    Http::assertSent(function ($request) {
        $body = $request->data();

        return str_contains($body['content'], 'back online')
            && $body['embeds'][0]['color'] === DiscordWebhook::COLOR_OK;
    });
});

it('does not ping without a webhook', function () {
    config()->set('health.notifications.discord.webhook_url', null);

    (new NotifyOnMaintenanceMode)->enabled();

    Http::assertNothingSent();
});

it('does not ping when notifications are disabled', function () {
    config()->set('health.notifications.enabled', false);

    (new NotifyOnMaintenanceMode)->enabled();

    Http::assertNothingSent();
});

it('is wired to the maintenance mode events', function () {
    // Dispatching the framework events should reach the listener (registered in
    // AppServiceProvider) and post to Discord.
    event(new MaintenanceModeEnabled);
    Http::assertSent(fn ($request) => str_contains($request->data()['content'], 'entered maintenance mode'));

    event(new MaintenanceModeDisabled);
    Http::assertSent(fn ($request) => str_contains($request->data()['content'], 'back online'));
});
