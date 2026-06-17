<?php

declare(strict_types=1);

namespace App\Health;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin sender for the configured Discord webhook
 * (`health.notifications.discord.webhook_url`). Shared by DiscordHealthChannel
 * (failure alerts) and NotifyOnHealthRecovery (recovery alerts). No-ops when the
 * webhook is unset, and never lets a Discord outage bubble into the caller —
 * health runs on a schedule and must not be broken by a flaky webhook.
 */
class DiscordWebhook
{
    /** Discord embed colors (decimal). */
    public const COLOR_OK = 0x2EB67D;

    public const COLOR_WARNING = 0xECB22E;

    public const COLOR_FAILED = 0xE01E5A;

    /** @param  array<int, array<string, mixed>>  $embeds */
    public function send(string $content, array $embeds): void
    {
        $webhookUrl = config('health.notifications.discord.webhook_url');

        if (blank($webhookUrl)) {
            return;
        }

        try {
            $response = Http::asJson()->timeout(10)->post((string) $webhookUrl, [
                'content' => $content,
                'embeds' => $embeds,
            ]);

            if ($response->failed()) {
                Log::warning('Discord health notification rejected', ['status' => $response->status()]);
            }
        } catch (\Throwable $e) {
            Log::warning('Discord health notification failed to send', ['error' => $e->getMessage()]);
        }
    }
}
