# Migrating a fork to template v5.3.0

v5.3.0 adds **application health checks** via [`spatie/laravel-health`](https://spatie.be/docs/laravel-health):
a deep `/health` endpoint covering the database, Redis, Horizon, queue, scheduler,
and the Reverb websocket server, run on a schedule. It complements — does not
replace — Laravel's `/up`. No schema or migration (results store in the cache).

> **Why:** `/up` only proves the framework booted; it knows nothing about whether
> Horizon is draining jobs or Reverb is accepting sockets. `/health` makes each
> dependency observable for uptime monitoring, without coupling a flaky dependency
> to container restarts (that stays `/up`'s job).

## Prerequisites

- Fork is on template **v5.2.1**. Check: `jq -r .version template-manifest.json` → `5.2.1`.

---

## Part A — Install the package

```sh
composer require spatie/laravel-health
```

## Part B — Config (cache result store, no migration)

Publish and edit `config/health.php`:

```sh
php artisan vendor:publish --tag=health-config
```

- **Result store → cache** (not Eloquent — avoids a migration and works for
  multi-tenant / DB-less forks):
  ```php
  use Spatie\Health\ResultStores\CacheHealthResultStore;

  'result_stores' => [
      CacheHealthResultStore::class => [
          'store' => env('HEALTH_CACHE_STORE', env('CACHE_STORE', 'database')),
      ],
  ],
  ```
  (Remove the `EloquentHealthResultStore` block and its unused imports.)
- **Notifications off by default:** `'enabled' => env('HEALTH_NOTIFICATIONS_ENABLED', false)` (Sentry already covers errors).
- **Fail loud for monitors:** `'json_results_failure_status' => 503`.
- **Skips aren't failures:** set `'treat_skipped_as_failure' => false` (required for the `->if()` gating below).

## Part C — Custom Reverb check + Discord channel

Copy these from the template (`app/Health/`):
- `Checks/ReverbCheck.php` — spatie ships no Reverb check; TCP-probes the address Reverb binds to (`reverb.servers.reverb`) over loopback.
- `DiscordWebhook.php` — shared Discord sender (no-ops without a webhook; swallows outages).
- `DiscordHealthChannel.php` — the `discord` notification channel (failed-check embeds).
- `Listeners/NotifyOnHealthRecovery.php` — green "recovered" ping on a down→ok flip.
- `Listeners/NotifyOnMaintenanceMode.php` — pings on `artisan down`/`up`.

And the tests (`tests/Unit/Health/`): `ReverbCheckTest`, `DiscordHealthChannelTest`,
`NotifyOnHealthRecoveryTest`, `NotifyOnMaintenanceModeTest`. Run
`./vendor/bin/pest tests/Unit/Health` to confirm they pass in your fork.

## Part D — Register the checks, channel + listeners

In `app/Providers/AppServiceProvider.php`, add `$this->configureHealthChecks();`
to `boot()` and copy the `configureHealthChecks()` method from the template. It:
- registers the `discord` notification channel (`Notification::extend('discord', …)`);
- registers the recovery listener (`Event::listen(CheckEndedEvent::class, NotifyOnHealthRecovery::class)`);
- registers the maintenance-mode listeners (`MaintenanceModeEnabled`/`Disabled` → `NotifyOnMaintenanceMode`);
- registers the checks, each gated with `->if(...)` on whether the fork uses that dependency.

> **Enable only what the project runs.** This is the one place a fork is *expected*
> to diverge: trim the check list to the services this project actually has. A
> DB-less marketing site keeps `UsedDiskSpaceCheck` + `ScheduleCheck` and drops
> Database/Redis/Horizon/Queue/Reverb; an app with no websockets drops `ReverbCheck`.
> The default `->if()` gating already self-disables inapplicable checks, but remove
> a check outright when it will never apply. See `docs/guidelines/health-checks.md`.

## Part E — Route

In `routes/web.php` (a **central**, non-tenant route), add:

```php
use Spatie\Health\Http\Controllers\HealthCheckJsonResultsController;

Route::get('health', HealthCheckJsonResultsController::class)->name('health');
```

## Part F — Schedule

In `bootstrap/app.php` → `withSchedule()` (add the closure if the fork has none):

```php
$schedule->command('health:check')->everyMinute();
$schedule->command('health:queue-check-heartbeat')->everyMinute();   // QueueCheck
$schedule->command('health:schedule-check-heartbeat')->everyMinute(); // ScheduleCheck
```

(If you dropped `QueueCheck`/`ScheduleCheck` in Part D, drop the matching heartbeat.)

## Part G — Env

Add to `.env.example`:

```dotenv
HEALTH_NOTIFICATIONS_ENABLED=false
# HEALTH_CACHE_STORE=
# A channel fires only when its target is set (and notifications enabled):
# HEALTH_TO_ADDRESS=
# HEALTH_DISCORD_WEBHOOK_URL=
# HEALTH_SLACK_WEBHOOK_URL=
# HEALTH_SECRET_TOKEN=
```

> **Discord:** set `HEALTH_DISCORD_WEBHOOK_URL` to a Discord channel webhook URL and
> `HEALTH_NOTIFICATIONS_ENABLED=true` — failed checks post there automatically (no
> extra package; the `discord` channel is registered in `AppServiceProvider`).

---

## Verify

```sh
php artisan config:clear
php artisan health:check        # all registered checks run (services not running locally will show failed — expected)
php artisan health:list         # last stored snapshot
curl -s localhost/health | jq   # JSON; 200 all-clear, 503 on failure
```

**Keep `/up` as the Coolify container health check — do NOT point it at `/health`.**

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.3.0"`. Don't copy this
changelog into the fork — the manifest `version` is the record (see the template's
[`.template/README.md`](../README.md)).
