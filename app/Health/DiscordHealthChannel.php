<?php

declare(strict_types=1);

namespace App\Health;

use Illuminate\Notifications\Notification;
use Spatie\Health\Checks\Result;

/**
 * Custom Laravel notification channel that posts spatie/laravel-health's
 * CheckFailedNotification to a Discord webhook. Spatie ships only mail + slack;
 * Discord has no first-class channel, so we POST a Discord embed payload (no
 * third-party package) via the shared {@see DiscordWebhook} sender. Registered as
 * the `discord` channel in AppServiceProvider and selected via
 * config('health.notifications.notifications').
 *
 * Reuses spatie's own enable/throttle gating — this channel only runs when the
 * notification is actually dispatched. No-ops when the webhook URL is unset.
 */
class DiscordHealthChannel
{
    /** Status → embed color (decimal). */
    private const COLORS = [
        'warning' => DiscordWebhook::COLOR_WARNING,
        'failed' => DiscordWebhook::COLOR_FAILED,
        'crashed' => DiscordWebhook::COLOR_FAILED,
    ];

    public function __construct(private DiscordWebhook $webhook = new DiscordWebhook) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        // CheckFailedNotification carries a public array $results; guard for safety.
        $results = property_exists($notification, 'results') ? $notification->results : null;

        if (empty($results)) {
            return;
        }

        // Discord allows max 10 embeds per message.
        $embeds = [];
        foreach (array_slice($results, 0, 10) as $result) {
            /** @var Result $result */
            $status = (string) $result->status->value;

            $embeds[] = [
                'title' => $result->check->getLabel(),
                'description' => $result->getNotificationMessage() ?: ucfirst($status),
                'color' => self::COLORS[$status] ?? 0x808080,
            ];
        }

        $appName = config('app.name') ?: config('app.url') ?: 'Laravel';
        $count = count($results);
        $content = sprintf(
            '🔴 Health check failed on **%s** (%s) — %d issue%s',
            $appName,
            app()->environment(),
            $count,
            $count === 1 ? '' : 's',
        );

        $this->webhook->send($content, $embeds);
    }
}
