<?php

declare(strict_types=1);

namespace App\Health\Listeners;

use App\Health\DiscordWebhook;
use Illuminate\Support\Facades\Cache;
use Spatie\Health\Events\CheckEndedEvent;

/**
 * Sends a Discord "recovered" ping when a health check returns to ok after a
 * CONFIRMED outage. spatie/laravel-health only notifies on failure — without this
 * you'd get the down alert but silence on recovery.
 *
 * Debounced: a check must be down for {@see self::CONFIRM_AFTER} consecutive runs
 * (~2 min) before it counts as a real outage, so a single transient/flapping
 * failure never produces a spurious "recovered" ping. Tracks a per-check down
 * streak in the cache; pings once when a confirmed outage clears, then resets.
 *
 * Listens to CheckEndedEvent (fired per check on every scheduled health:check) and
 * runs in central context (the scheduler), so the cache is shared/central — never
 * tenant-scoped. Gated on the same Discord webhook + notifications-enabled flags.
 */
class NotifyOnHealthRecovery
{
    /** Statuses treated as "down". */
    private const DOWN = ['failed', 'warning', 'crashed'];

    /** Consecutive down readings before an outage is considered real. */
    private const CONFIRM_AFTER = 2;

    private const CACHE_PREFIX = 'health:downStreak:';

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
        $downStreak = (int) Cache::get($cacheKey, 0);

        // Still down: extend the streak and wait it out.
        if (in_array($current, self::DOWN, true)) {
            Cache::forever($cacheKey, $downStreak + 1);

            return;
        }

        // Back to ok: clear the streak. Ping only if a CONFIRMED outage just ended.
        Cache::forever($cacheKey, 0);

        if ($downStreak < self::CONFIRM_AFTER) {
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
