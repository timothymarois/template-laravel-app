<?php

declare(strict_types=1);

namespace App\Health\Listeners;

use App\Health\DiscordWebhook;
use Illuminate\Support\Facades\Cache;
use Spatie\Health\Events\CheckEndedEvent;

/**
 * Sends a Discord "recovered" ping when a health check flips from a down state
 * (failed / warning / crashed) back to ok. spatie/laravel-health only notifies on
 * failure — without this you'd get the down alert but silence on recovery.
 *
 * Listens to CheckEndedEvent (fired per check on every scheduled health:check),
 * remembering each check's last status in the cache and acting only on the
 * transition. Gated on the same Discord webhook + notifications-enabled flags as
 * the failure path; runs in central context (the scheduler), so the cache is
 * shared/central, never tenant-scoped.
 */
class NotifyOnHealthRecovery
{
    /** Statuses treated as "down" — a flip from any of these to ok is a recovery. */
    private const DOWN = ['failed', 'warning', 'crashed'];

    private const CACHE_PREFIX = 'health:lastStatus:';

    public function __construct(private DiscordWebhook $webhook = new DiscordWebhook) {}

    public function handle(CheckEndedEvent $event): void
    {
        if (! config('health.notifications.enabled') || blank(config('health.notifications.discord.webhook_url'))) {
            return;
        }

        $current = (string) $event->result->status->value;

        // Skipped checks carry no health signal — ignore, and don't disturb state.
        if ($current === 'skipped') {
            return;
        }

        $cacheKey = self::CACHE_PREFIX.$event->check->getName();
        $previous = Cache::get($cacheKey);
        Cache::forever($cacheKey, $current);

        if (! in_array($previous, self::DOWN, true) || $current !== 'ok') {
            return;
        }

        $label = $event->check->getLabel();
        $appName = config('app.name') ?: config('app.url') ?: 'Laravel';

        $this->webhook->send(
            sprintf('✅ Recovered on **%s** (%s) — %s is healthy again.', $appName, app()->environment(), $label),
            [[
                'title' => $label,
                'description' => $event->result->getShortSummary() ?: 'Back to OK.',
                'color' => DiscordWebhook::COLOR_OK,
            ]],
        );
    }
}
