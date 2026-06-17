<?php

declare(strict_types=1);

use App\Health\DiscordWebhook;
use App\Health\Listeners\NotifyOnHealthRecovery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Result;
use Spatie\Health\Events\CheckEndedEvent;

/*
|--------------------------------------------------------------------------
| NotifyOnHealthRecovery — debounced Discord "recovered" ping
|--------------------------------------------------------------------------
|
| spatie notifies on failure only; this listener fires a green ping when a check
| returns to ok after a CONFIRMED outage (>= 2 consecutive down readings). A
| single transient failure must NOT ping — that flapping was producing spurious
| "recovered" messages. Covers the debounce, the gates, streak tracking, ping-once,
| and that it's wired to the event.
|
*/

const RECOVERY_WEBHOOK = 'https://discord.com/api/webhooks/1/abc';
const STREAK_KEY = 'health:downStreak:Reverb';

function recoveryEvent(string $statusValue, string $label = 'Reverb'): CheckEndedEvent
{
    $check = UsedDiskSpaceCheck::new()->name($label);

    $result = match ($statusValue) {
        'ok' => Result::make()->ok()->shortSummary('Accepting connections'),
        'warning' => Result::make()->warning('degraded'),
        default => Result::make()->failed('down'),
    };

    $result->check($check);

    return new CheckEndedEvent($check, $result);
}

beforeEach(function () {
    Cache::flush();
    config()->set('health.notifications.enabled', true);
    config()->set('health.notifications.discord.webhook_url', RECOVERY_WEBHOOK);
    Http::fake();
});

it('pings after a confirmed outage recovers', function () {
    $listener = new NotifyOnHealthRecovery;
    $listener->handle(recoveryEvent('failed'));   // streak 1
    $listener->handle(recoveryEvent('failed'));   // streak 2 — confirmed
    $listener->handle(recoveryEvent('ok'));        // recovery

    Http::assertSent(function ($request) {
        $body = $request->data();

        return str_contains($body['content'], 'Recovered')
            && str_contains($body['content'], 'Reverb')
            && $body['embeds'][0]['color'] === DiscordWebhook::COLOR_OK;
    });
});

it('does NOT ping on a single transient failure (the flapping bug)', function () {
    $listener = new NotifyOnHealthRecovery;
    $listener->handle(recoveryEvent('failed'));   // streak 1 — not yet confirmed
    $listener->handle(recoveryEvent('ok'));        // clears without a ping

    Http::assertNothingSent();
});

it('does not ping when always ok', function () {
    (new NotifyOnHealthRecovery)->handle(recoveryEvent('ok'));

    Http::assertNothingSent();
});

it('does not ping while still down', function () {
    $listener = new NotifyOnHealthRecovery;
    $listener->handle(recoveryEvent('failed'));
    $listener->handle(recoveryEvent('failed'));
    $listener->handle(recoveryEvent('failed'));

    Http::assertNothingSent();
});

it('pings only once per outage', function () {
    $listener = new NotifyOnHealthRecovery;
    $listener->handle(recoveryEvent('failed'));
    $listener->handle(recoveryEvent('failed'));
    $listener->handle(recoveryEvent('ok'));        // ping + reset
    $listener->handle(recoveryEvent('ok'));        // streak 0 — no ping

    Http::assertSentCount(1);
});

it('does not ping when notifications are disabled', function () {
    config()->set('health.notifications.enabled', false);
    Cache::forever(STREAK_KEY, 5); // even a confirmed outage stays silent

    (new NotifyOnHealthRecovery)->handle(recoveryEvent('ok'));

    Http::assertNothingSent();
});

it('tracks the consecutive down streak', function () {
    $listener = new NotifyOnHealthRecovery;
    $listener->handle(recoveryEvent('failed'));
    $listener->handle(recoveryEvent('failed'));

    expect(Cache::get(STREAK_KEY))->toBe(2);
});

it('is registered as a CheckEndedEvent listener', function () {
    Cache::forever(STREAK_KEY, 2); // simulate a confirmed outage

    // Dispatch the real event — only pings if AppServiceProvider wired the listener.
    event(recoveryEvent('ok'));

    Http::assertSent(fn ($request) => str_contains($request->data()['content'], 'Recovered'));
});
