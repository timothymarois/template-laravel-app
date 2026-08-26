<?php

declare(strict_types=1);

namespace App\Health\Listeners;

use App\Health\DiscordWebhook;

/**
 * Pings Discord when the app ENTERS (`artisan down`) or LEAVES (`artisan up`)
 * maintenance mode — Laravel fires MaintenanceModeEnabled / MaintenanceModeDisabled
 * from those commands. Useful to bracket deploy/maintenance windows in the channel.
 *
 * NOTE: this covers DELIBERATE maintenance mode, not a crash. A fully-down app
 * can't notify anyone — for "the whole app is unreachable" detection, point an
 * external uptime monitor at /health (see docs/concepts/health-checks.md).
 *
 * Gated on the same Discord webhook + notifications-enabled flags as the health
 * notifications, via the shared {@see DiscordWebhook} sender.
 */
class NotifyOnMaintenanceMode
{
    public function __construct(private DiscordWebhook $webhook = new DiscordWebhook) {}

    public function enabled(): void
    {
        if (! $this->shouldNotify()) {
            return;
        }

        $this->webhook->send(
            sprintf('🚧 **%s** (%s) entered maintenance mode.', $this->appName(), app()->environment()),
            [[
                'title' => 'Maintenance mode enabled',
                'description' => 'The app is temporarily unavailable to visitors (returning 503).',
                'color' => DiscordWebhook::COLOR_WARNING,
            ]],
        );
    }

    public function disabled(): void
    {
        if (! $this->shouldNotify()) {
            return;
        }

        $this->webhook->send(
            sprintf('✅ **%s** (%s) is back online.', $this->appName(), app()->environment()),
            [[
                'title' => 'Maintenance mode disabled',
                'description' => 'The app is serving traffic again.',
                'color' => DiscordWebhook::COLOR_OK,
            ]],
        );
    }

    private function shouldNotify(): bool
    {
        return (bool) config('health.notifications.enabled')
            && filled(config('health.notifications.discord.webhook_url'));
    }

    private function appName(): string
    {
        return config('app.name') ?: config('app.url') ?: 'Laravel';
    }
}
