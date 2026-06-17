# Migrating a fork to template v5.2.0

v5.2.0 removes the bundled **Log Viewer** (`opcodesio/log-viewer`) and adds a vendor-neutral **centralized logging** guide. Minor — no schema, API, or Docker-core changes. ~5 minutes, mechanical.

**Why:** Log Viewer read log *files* from `storage/logs/`, but the Docker/Coolify deploy sets `LOG_CHANNEL=stderr` and runs on an ephemeral container with no persistent disk — so there were no files to browse and the `/log-viewer` page was a non-functional, authenticated route in production. Production logs now go to a central sink you choose (Loki/Grafana, Axiom, …).

> **Local development is unchanged.** `LOG_CHANNEL` still defaults to `daily`; logs are still written to `storage/logs/laravel-*.log` locally. Read them with `tail -f storage/logs/laravel-*.log`, your IDE, or `php artisan pail`. Only the in-browser `/log-viewer` page is removed.

## Prerequisites

- Fork is on template **v5.1.3** (apply earlier migrations first). Check: `jq -r .version template-manifest.json` → expect `5.1.3`.
- Baseline `pnpm check` is green.

> If your fork actively relies on the `/log-viewer` UI in production, note it is already non-functional there (no log files exist on the ephemeral container). The replacement is a central sink — see Part C.

---

## Part A — Remove Log Viewer

Do the **code edits first**, then uninstall the package — otherwise the leftover `AppServiceProvider` reference fatals the post-uninstall `package:discover`.

**1. `app/Providers/AppServiceProvider.php`** — remove the import, the `boot()` call, and the method:

```diff
 use Illuminate\Support\ServiceProvider;
 use Illuminate\Validation\Rules\Password;
-use Opcodes\LogViewer\Facades\LogViewer;
```

```diff
         $this->configurePasswordRules();
         $this->configureRateLimiting();
-        $this->configureLogViewer();
     }
-
-    /**
-     * Configure Log Viewer authorization.
-     */
-    protected function configureLogViewer(): void
-    {
-        // Allow access in local/dev mode, require authentication in production
-        LogViewer::auth(function (Request $request) {
-            return app()->environment('local', 'development')
-                || $request->user() !== null;
-        });
-    }
```

> Keep the `use Illuminate\Http\Request;` import — `configureRateLimiting()` still uses it.

**2. `config/ziggy.php`** — drop the `log-viewer.*` exclusion:

```diff
-    'except' => ['_debugbar.*', 'horizon.*', 'log-viewer.*', 'sanctum'],
+    'except' => ['_debugbar.*', 'horizon.*', 'sanctum'],
```

**3. `app/Console/Commands/GenerateSitemap.php`** — drop the excluded prefix:

```diff
         'horizon',
-        'log-viewer',
         'sanctum',
```

**4. `resources/js/components/app/layout/AppLayout.vue`** — remove the menu item and the now-unused icon:

```diff
-import { House, User, Palette, Gauge, ScrollText, LogOut } from 'lucide-vue-next';
+import { House, User, Palette, Gauge, LogOut } from 'lucide-vue-next';
```

```diff
     { label: 'Horizon', icon: Gauge, href: '/horizon', external: true },
-    { label: 'Log Viewer', icon: ScrollText, href: '/log-viewer', external: true },
     { separator: true },
```

> If your fork moved this link elsewhere (sidebar, a different menu), remove it there too.

**5. Delete the config + published assets:**

```sh
rm -f config/log-viewer.php
rm -rf public/vendor/log-viewer
```

**6. Uninstall the package** (updates `composer.json` + `composer.lock`, reruns discovery):

```sh
composer remove opcodesio/log-viewer
```

**7. README** — remove any "Log Viewer" bullet / monitoring-table row, pointing readers to the logging guide instead.

Sanity check — nothing but history should remain:

```sh
grep -rni "log-viewer\|logviewer\|opcodes\|ScrollText" app config resources routes README.md docs
```

---

## Part B — Structured stderr by default (every fork)

`config/logging.php` — default the `stderr` channel to one-line JSON so a log collector ships structured fields to Loki/Grafana with no per-app setup:

```diff
+use Monolog\Formatter\JsonFormatter;
 use Monolog\Handler\NullHandler;
 use Monolog\Handler\StreamHandler;
```

```diff
         'handler' => StreamHandler::class,
-        'formatter' => env('LOG_STDERR_FORMATTER'),
+        'formatter' => env('LOG_STDERR_FORMATTER', JsonFormatter::class),
```

> Prod containers now emit JSON on stderr (the Coolify Logs tab shows JSON lines). To keep human-readable lines, set `LOG_STDERR_FORMATTER=` (empty) in that environment. Local dev is unaffected — it uses the `daily` file channel.

## Part C — Adopt the logging guide (optional copy)

The template adds `docs/guidelines/logging.md` and a sidebar entry in `docs/.vitepress/config.mts`. If your fork keeps the docs site in sync, copy both; otherwise skip — they're template docs, not app code.

---

## Part D — Choose a central log sink (recommended)

Production logs go to `stderr` (now JSON). To actually read them, forward that stream to **one** sink. This is an infrastructure step, done once per environment — no app code:

- **Self-hosted Loki + Grafana** — one instance serves all your apps; retention on S3 / DO Spaces; labels separate apps.
- **Axiom** — native Coolify Log Drain target; zero infra; generous free tier.
- **Better Stack / Grafana Cloud** — hosted; ship via Fluent Bit/HTTP.

Forward with a **host collector** (Grafana Alloy / Fluent Bit on the Coolify host) — robust and captures nginx/php-fpm/worker output. Coolify's native Log Drain is a fallback (it's ignored for Dockerfile build-pack resources — [coollabsio/coolify#2915](https://github.com/coollabsio/coolify/issues/2915)). Logs already arrive as JSON (Part B), so the collector gets parsed fields with no extra config.

Full detail incl. the recommended Coolify → Loki/Grafana + Spaces setup: [`docs/guidelines/logging.md`](../../docs/guidelines/logging.md).

---

## Verify

```sh
php artisan optimize:clear
php artisan route:list | grep log-viewer   # expect no matches
php artisan config:clear

pnpm check                                  # PHP + JS + docs + build, all green
```

Boot the app locally and confirm the profile menu no longer shows **Log Viewer** (Horizon stays). Trigger a log line and confirm it still writes to `storage/logs/laravel-*.log` locally.

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.2.0"`. Don't copy this changelog into the fork — the manifest `version` is the record (see the template's [`.template/README.md`](../README.md)).
