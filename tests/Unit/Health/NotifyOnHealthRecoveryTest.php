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
| NotifyOnHealthRecovery — Discord "recovered" ping on down → ok
|--------------------------------------------------------------------------
|
| spatie notifies on failure only; this listener fires a green ping when a check
| transitions from failed/warning/crashed back to ok. Covers the transition, the
| non-transitions (still-ok, still-down, first run), the enabled gate, that it
| records status for next time, and that it's actually registered on the event.
|
*/

const RECOVERY_WEBHOOK = 'https://discord.com/api/webhooks/1/abc';

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

it('pings when a check recovers from failed to ok', function () {
    Cache::forever('health:lastStatus:Reverb', 'failed');

    (new NotifyOnHealthRecovery)->handle(recoveryEvent('ok'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return $request->url() === RECOVERY_WEBHOOK
            && str_contains($body['content'], 'Recovered')
            && str_contains($body['content'], 'Reverb')
            && $body['embeds'][0]['color'] === DiscordWebhook::COLOR_OK
            && $body['embeds'][0]['description'] === 'Accepting connections';
    });
});

it('does not ping when the status was already ok', function () {
    Cache::forever('health:lastStatus:Reverb', 'ok');

    (new NotifyOnHealthRecovery)->handle(recoveryEvent('ok'));

    Http::assertNothingSent();
});

it('does not ping on the first ever run (no prior status)', function () {
    (new NotifyOnHealthRecovery)->handle(recoveryEvent('ok'));

    Http::assertNothingSent();
});

it('does not ping while still down', function () {
    Cache::forever('health:lastStatus:Reverb', 'failed');

    (new NotifyOnHealthRecovery)->handle(recoveryEvent('failed'));

    Http::assertNothingSent();
});

it('does not ping when notifications are disabled', function () {
    config()->set('health.notifications.enabled', false);
    Cache::forever('health:lastStatus:Reverb', 'failed');

    (new NotifyOnHealthRecovery)->handle(recoveryEvent('ok'));

    Http::assertNothingSent();
});

it('records the current status for next time', function () {
    (new NotifyOnHealthRecovery)->handle(recoveryEvent('failed'));

    expect(Cache::get('health:lastStatus:Reverb'))->toBe('failed');
});

it('is registered as a CheckEndedEvent listener', function () {
    Cache::forever('health:lastStatus:Reverb', 'failed');

    // Dispatch the real event — only fires Discord if AppServiceProvider wired it.
    event(recoveryEvent('ok'));

    Http::assertSent(fn ($request) => str_contains($request->data()['content'], 'Recovered'));
});
