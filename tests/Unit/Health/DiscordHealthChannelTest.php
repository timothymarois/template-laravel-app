<?php

declare(strict_types=1);

use App\Health\DiscordHealthChannel;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Checks\Result;
use Spatie\Health\Notifications\CheckFailedNotification;

/*
|--------------------------------------------------------------------------
| DiscordHealthChannel — posts failed health checks to a Discord webhook
|--------------------------------------------------------------------------
|
| Spatie ships only mail + slack, so this custom channel POSTs a Discord embed
| payload. Tests cover: no-op without a webhook, one embed per failed check with
| the right colors, the 10-embed cap, graceful handling of a rejected request,
| and that the channel is registered as `discord`.
|
*/

const WEBHOOK = 'https://discord.com/api/webhooks/1/abc';

function failedResult(string $label, string $message, string $status = 'failed'): Result
{
    $result = Result::make()->check(UsedDiskSpaceCheck::new()->name($label));

    return $status === 'warning' ? $result->warning($message) : $result->failed($message);
}

function sendDiscord(array $results): void
{
    (new DiscordHealthChannel)->send(new AnonymousNotifiable, new CheckFailedNotification($results));
}

it('does not post when no webhook is configured', function () {
    Http::fake();
    config()->set('health.notifications.discord.webhook_url', null);

    sendDiscord([failedResult('Reverb', 'down')]);

    Http::assertNothingSent();
});

it('posts an embed per failed check with status colors', function () {
    Http::fake();
    config()->set('health.notifications.discord.webhook_url', WEBHOOK);

    sendDiscord([
        failedResult('Reverb', 'Reverb is not accepting connections'),
        failedResult('Horizon', 'Horizon is not running', 'warning'),
    ]);

    Http::assertSent(function ($request) {
        $body = $request->data();

        return $request->url() === WEBHOOK
            && str_contains($body['content'], '2 issues')
            && count($body['embeds']) === 2
            && $body['embeds'][0]['title'] === 'Reverb'
            && $body['embeds'][0]['description'] === 'Reverb is not accepting connections'
            && $body['embeds'][0]['color'] === 0xE01E5A   // failed → red
            && $body['embeds'][1]['color'] === 0xECB22E;  // warning → amber
    });
});

it('caps the payload at 10 embeds', function () {
    Http::fake();
    config()->set('health.notifications.discord.webhook_url', WEBHOOK);

    sendDiscord(collect(range(1, 15))->map(fn ($i) => failedResult("Check {$i}", "msg {$i}"))->all());

    Http::assertSent(fn ($request) => count($request->data()['embeds']) === 10);
});

it('swallows a rejected request instead of throwing', function () {
    Http::fake(['*' => Http::response('rate limited', 429)]);
    config()->set('health.notifications.discord.webhook_url', WEBHOOK);

    // If the channel let the failure bubble, this call would throw and fail the test.
    sendDiscord([failedResult('Reverb', 'down')]);

    Http::assertSent(fn () => true);
});

it('registers the discord notification channel', function () {
    expect(app(ChannelManager::class)->driver('discord'))
        ->toBeInstanceOf(DiscordHealthChannel::class);
});
