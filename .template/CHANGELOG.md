# Changelog

This is the change history for `template-laravel-app` — the migration log forks read to stay compatible. Each release links its upgrade steps in `migrations/`.

**Format:** every entry follows the [changelog rules](README.md#changelog-rules) — standardized **Added / Changed / Fixed / Migration** sections. Releases before v4.0.0 are archived in [`CHANGELOG-LEGACY.md`](CHANGELOG-LEGACY.md).

**Forks do not copy this file.** A fork records the template version it's aligned with in its `template-manifest.json` (`version`); its own `CHANGELOG.md` is a short stub pointing here (see [`README.md`](README.md)) — unless the fork tracks its own product versions.

# Released

## v5.7.0 - 09/08/2026

`Docker:` Vendors this stack's agent skills into `.claude/skills/`, adds `docker/project/` so a fork can configure nginx and PHP without editing managed core, and puts the production image under a `check:deploy` gate plus a built-image CI workflow. Minor: no schema or dependency change.

> ⚠️ Managed core moves — `Dockerfile` and `docker/config/nginx.conf`. Every fork acts.

### Added

- **14 vendored agent skills** in `.claude/skills/`, matched to this stack and its delivery workflow, plus `.agents/skills` for the neutral path.
- **`docker/project/`** — `nginx/http/`, `nginx/server/` and `php/`, baked into the image and never template-managed. Ships empty; a wildcard include matching nothing is a no-op, so a fork that adds nothing gets byte-identical behavior. Closes #18.
- **`docker/config/nginx-snippets/`** — `laravel-fastcgi.conf` and `laravel-front-controller.conf`, so a project location reaches PHP-FPM without copying managed-core wiring or losing its ceiling to `try_files`.
- **`check:deploy` and `.github/workflows/docker-config.yml`** — file-agreement contracts inside `pnpm check`, and a built-image proof in CI: `nginx -t`, effective context, and a real oversized POST refused on a default route and accepted on the elevated one.
- **`docs/guides/git-conventions.md`** — branch and commit naming, the canonical type set, and the no-machine-authorship-branding rule.
- **`.lerd.yaml`** — the [Lerd](https://lerd.sh/) counterpart to `herd.yml`; both coexist, each tool ignores the other's.
- **Issue and pull-request templates** in `.github/`, so a filed issue or PR carries the same headings whoever writes it.

### Changed

- **`client_max_body_size` stays 25M** — route-scoped capacity goes in `docker/project/`, whose include is last in the server block so a project regex can shadow neither the PHP handler nor the dotfile deny.
- **`CLAUDE.md` is a byte-identical copy of `AGENTS.md`**, enforced by a Pest test in the existing suite.
- **`AGENTS.md` restructured** — a skills step and a scope rule in "Before you work", "Hard gates" and "Never" folded into one "Hard rules" list, "Tech stack" and "Architecture" merged, tree trimmed.
- **`.gitignore` ignores `/.claude/settings.local.json`** — the skills are committed, per-machine permissions are not.

### Fixed

- **`herd.yml` pinned PHP 8.3 while `composer.json` requires `^8.4`** — `composer install` refused on a Herd site built from the shipped file.

### Migration

See [`migrations/template-v5.7.0.md`](migrations/template-v5.7.0.md). Parts A-D and G-J: every fork. Parts E-F: Docker forks.

## v5.6.0 - 08/26/2026

Replaces the `.knowledge/` documentation payload with a plain **`docs/`** tree in the standard layout, and adds a **production release process** with a `/release` endpoint so a deploy can be polled instead of guessed at. Minor: no schema, dependency, or Docker-core change. `pnpm check` swaps its `check:docs` step for `check:release`.

> ⚠️ Every fork acts: convert `.knowledge/` to `docs/`, and fill in the new `deploy` block in `template-manifest.json` before its first release.

### Added

- **`docs/` documentation home** — `BRIEF.md` + `CODEMAP.md` at the root, `concepts/` (how a subsystem works and how it fails) and `guides/` (one task each), every home carrying a `README.md` index. No payload, no linter, no version stamp: the writing standards live in the `structuring-project-docs` skill, so there is nothing to version again.
- **Production release process** — `scripts/{prepare,publish}-production-release`, `production-release-version`, `assert-neutral-main-version`. `production` is the deployed branch, `main` stays at version `0.0.0`, and a tag is published only after the deploy is verified live. Full procedure in `docs/guides/releasing.md`.
- **`GET /release`** — reports the deployed version as JSON with `Cache-Control: no-store`, the endpoint the publish script and any external deployment monitor poll. Joins `/up` and `/health`; the three are documented together in `docs/concepts/deployment-endpoints.md`.
- **`deploy` block in `template-manifest.json`** — `repository`, `productionUrl`, `productionBranch`, read by the release scripts. An unedited placeholder is refused rather than polled.
- **`check:release`** — the four release suites, with stubbed `gh` and `curl` so nothing reaches the network; wired into `pnpm check` and CI alongside a neutral-version guard for main-bound changes.

### Changed

- **`AGENTS.md`** rewired onto `docs/`, and the `✅`/`❌` code galleries folded back in as a closing appendix — reversing that part of v5.5.0, which had extracted them. 208 → 698 lines, all always-loaded.
- **Guides renamed to the paths the code already cites** — `tenancy-usage.md` → `guides/tenancy-using.md`, `tenancy-migrations.md` → `guides/tenancy-migrating.md`, `write-tests.md` → `guides/writing-tests.md`; `health-checks.md`, `logging.md` and `ziggy-routes.md` → `concepts/`.
- **Workflows trigger on `production` as well as `main`** — without it a release pull request arrives with no checks.
- **Removed: `.knowledge/`** — the seven `docs-*.md` standards, `doc-lint` and its teeth-test, `.version`, `.payload-manifest`, and the four homes that only ever held a "none yet" row. `MEMORY.md` is not carried forward; friction now goes in the page that owns the subsystem, under how it fails.
- **Removed: `check:docs` and `.github/workflows/doc-lint.yml`** — nothing replaces them. `docs/` correctness is a review concern, and `AGENTS.md` says so.

### Fixed

- **18 dangling `docs/guidelines/*` citations**, in shipped runtime output, `README.md`, `docker/README.md`, `.env.example`, and two test assertions. That directory has not existed since v5.4.0 removed the VitePress site; the tests passed throughout because they assert on the string, not on a file existing. Naming the new guides after the paths the code already used fixes 16 of the 18 by construction.

### Migration

See [`migrations/template-v5.6.0.md`](migrations/template-v5.6.0.md). Every fork: convert its docs home, fill in the `deploy` block, and drop the retired gate. Docs, tooling and one additive route — no schema or deploy-time work.

## v5.5.0 - 07/20/2026

Replaces the `.ai/` knowledge system with **`.knowledge/`** — the versioned, linted payload from [knowledge-template](https://github.com/timothymarois/knowledge-template) (adopts its v1.0.0) — restructures `AGENTS.md` onto it, and folds in several small bug fixes and guide improvements sourced from fork friction. Minor: no schema, dependency, or Docker-core change. `pnpm check` regains a `check:docs` step.

### Added

- **`.knowledge/` knowledge system** — homes (`prd/`, `prd-drafts/`, `research/`, `references/`, `tmp/`), writing standards (`guides/docs-*.md`), a stdlib `doc-lint` + teeth-test, the orientation trio + `OVERVIEW.md`, a `.version` stamp, and a `.payload-manifest` that checksums the shipped files so a repo can prove it runs the version it claims. Versioned separately from the template, by [knowledge-template](https://github.com/timothymarois/knowledge-template).
- **`check:docs`** back in the gate — `pnpm check` runs the knowledge linter (teeth-test + `doc-lint`) alongside PHP and JS; a new `.github/workflows/doc-lint.yml` runs it in CI.
- **`guides/stack-examples.md`** — the full PHP + Vue `✅`/`❌` code galleries, pulled out of `AGENTS.md`.

### Changed

- **`AGENTS.md`** restructured onto `.knowledge/` and cut from ~900 to ~205 lines — every rule kept, the two code-example galleries moved to `guides/stack-examples.md`, all `.ai/` paths now `.knowledge/`.
- **`README.md`** points at `.knowledge/` and credits knowledge-template.
- **Removed: `.ai/`** — the orientation trio and project guides moved into `.knowledge/`, reshaped to the new standards; placeholder `TEMPLATE.md`/`README.md` files dropped.
- **Guide improvements from fork friction** — `write-tests.md` and `tenancy-usage.md` now warn that the default `phpunit.xml` disables tenancy (a green run hides tenant behavior; run `check:tenancy`), and that a new tenant table must not reuse a framework/central table name. New `guides/ziggy-routes.md` documents the Ziggy operational gotchas that repeatedly tripped agents (never hand-edit the generated `ziggy.js`; regenerate when `route()` can't find a new route; `route()` is SSR-safe); `AGENTS.md` carries the one-line rule.

### Fixed

- **500 crash on the admin data-table routes when `filters` is a scalar.** A non-array `filters` query param reached the array-typed caster and threw; any authenticated user could 500 the admin users page (and the simple-table endpoint), and a scalar submitted via `POST /admin/users/filters` persisted into the session and crashed the next load. `app/Http/Concerns/InertiaDataTableOptions.php` now coerces a non-array `filters` back to the default before casting; regression tests added.
- **`viewFields` accepted a scalar and returned untyped.** The same concern now guards `viewFields` the way it guards `filters` (no crash, but it kept a scalar through); regression test added.
- **`PhoneNumber::normalize()` kept a stray non-leading `+`.** `415-555-019+` used to format as `(415) 555-019+`; it now strips every non-digit before validating the 10-digit number. Test added. *(Both surfaced from fork friction logs.)*

### Migration

See [`migrations/template-v5.5.0.md`](migrations/template-v5.5.0.md). **Forks on v5.3.0 or below skip v5.4.0 entirely** — do not adopt `.ai/` only to delete it; go straight to v5.5.0 (Path 2, fresh adoption). Mostly docs; the code changes are three small bug fixes in Part F (data-table scalar guards, `PhoneNumber` normalize).

## v5.4.0 - 07/13/2026

Replaces the VitePress **`docs/` site** with a Markdown-only **`.ai/` knowledge system** and restructures `AGENTS.md` onto it. Minor: docs / agent-tooling only — no schema, API, runtime, or Docker-core change. `pnpm check` drops its `docs` step.

### Added

- **`.ai/docs/` knowledge system** — `research/` (with a `references/` subfolder for visual targets), `PRD-drafts/` (upcoming PRDs), `PRD/` (tested contracts), and `guides/`, each with a `README` + `TEMPLATE`, under a `docs/README.md` map; `.ai/tmp/` git-ignored scratch. The template's guideline pages ship as `guides/`.
- **Code-documentation convention** (`AGENTS.md`) — complex methods get a doc-block; a method implementing a `PRD/` requirement cites its `R-<AREA>-<n>`.

### Changed

- **`AGENTS.md`** restructured onto the `.ai/docs` model — light-by-default load order (always read `.ai/BRIEF` + `.ai/CODEMAP` + `.ai/MEMORY`), a read-the-home-`README` doc gate, and `.ai/docs` references throughout; all conventions preserved.
- **`.ai/MEMORY.md`** is now a living friction log — a trap stays until it's solved, then gets deleted — and one of the always-loaded orientation trio (`BRIEF` · `CODEMAP` · `MEMORY`).
- **Removed: VitePress `docs/` site** — the site, its `docs:*` / `check:docs` scripts, the `docs:build` step in `check`, and its `.gitignore` block. Guideline content moved to `.ai/docs/guides/`.

### Migration

See [`migrations/template-v5.4.0.md`](migrations/template-v5.4.0.md). Every fork: add `.ai/docs`, keep `MEMORY.md` (refresh its framing), restructure `AGENTS.md`, remove the VitePress site. Docs-only — no deploy work.

## v5.3.0 - 06/17/2026

Adds **application health checks** via `spatie/laravel-health`: a `/health` endpoint covering database, Redis, Horizon, queue, scheduler, and Reverb, complementing Laravel's `/up`. Each check self-gates to the services a fork runs. Cache result store — no migration.

### Added

- **`spatie/laravel-health`** — checks for disk, database, Redis, Horizon, queue, schedule, and a custom `ReverbCheck`; registered (and gated) in `AppServiceProvider`.
- **`GET /health`** — JSON snapshot for uptime monitors, 503 on failure; distinct from the `/up` container gate. Optional `HEALTH_SECRET_TOKEN`.
- **Discord notifications** (opt-in) — failed checks, "recovered" pings, and maintenance-mode (`artisan down`/`up`) pings post to `HEALTH_DISCORD_WEBHOOK_URL`. Channels self-gate to whichever target env is set.

### Changed

- **`config/health.php`** uses the cache result store (no migration; multi-tenant / DB-less safe). Notifications default off (Sentry covers errors).

### Migration

See [`migrations/template-v5.3.0.md`](migrations/template-v5.3.0.md). **Trim the check list to the services your fork runs.** No deploy-time DB work. Full detail in `docs/guidelines/health-checks.md`.

## v5.2.1 - 06/17/2026

`Docker:` Adds the missing **Reverb WebSocket proxy** to `docker/config/nginx.conf`. The Pusher-protocol `/app/{appKey}` connection now upgrades and proxies to the Reverb server on `127.0.0.1:8080`; without it, browsers could never open the websocket through the public domain (the handshake fell through to `index.php` and failed). Patch: nginx-only, no schema/API/app-runtime change. No-op for forks that don't run Reverb.

> Note: this fixes a real gap — Reverb websockets never worked through the public domain on any fork built from this template, because nginx had no `/app/` location. `broadcasting.md` documented the proxy as if it existed. Server-side publishing (`/apps/{id}/events`, direct to `127.0.0.1:8080`) was unaffected; only the browser→Reverb path was broken.

### Changed

- **`docker/config/nginx.conf` gains `location ^~ /app/`** — proxies the websocket to `127.0.0.1:8080` with `Upgrade`/`Connection` headers and a long read/send timeout. The `^~ /app/` matcher catches the WS path only and never `/apps/{id}/events` (the server-side events API, which must stay off the public domain).

### Migration

See [`migrations/template-v5.2.1.md`](migrations/template-v5.2.1.md) — add the one `location` block to `docker/config/nginx.conf` and redeploy. Only forks running Reverb (`requires.reverb: true`) need it functionally; others can take it as a harmless no-op to stay in sync.

## v5.2.0 - 06/16/2026

Replaces the disk-bound **Log Viewer** with centralized logging: production logs to `stderr` as structured JSON, shipped to a central sink. Minor: no schema or API changes. Local dev is unchanged (`LOG_CHANNEL` stays `daily`; `/log-viewer` page is the only thing gone).

### Added

- **Centralized logging guide** — `docs/guidelines/logging.md` (in the docs sidebar): the local-vs-prod model, sink options (self-hosted Loki/Grafana + Spaces, Axiom, Better Stack), and the recommended **Coolify → Loki/Grafana via Alloy** default setup.

### Changed

- **`stderr` channel defaults to one-line JSON** (`config/logging.php`) so a log collector ships structured fields to Loki/Grafana with no per-app setup; override with `LOG_STDERR_FORMATTER`.
- **Removed: Log Viewer** (`opcodesio/log-viewer`) — non-functional on the disk-less Coolify deploy and an exposed `/log-viewer` route; replaced by the central-logging path above.

### Migration

See [`migrations/template-v5.2.0.md`](migrations/template-v5.2.0.md) — remove Log Viewer (package, config, 5 code refs, published assets) and adopt the JSON-stderr default. ~5 minutes, mechanical.

## v5.1.3 - 06/16/2026

Aligns the runtime defaults with the mandatory Redis stack, stops the tenant-invite email from blocking the request, and reorganizes the template's lineage docs into a dedicated `.template/` directory. Patch: no schema or API changes.

> ⚠️ **Requires Redis to be reachable.** Redis is already a documented stack requirement (queue + cache via Horizon), but a fork that left `CACHE_STORE`/`QUEUE_CONNECTION` unset **and** has no reachable Redis will break on the next deploy. Provision Redis (`REDIS_HOST`/`REDIS_PORT`) or pin the env vars to `database` explicitly before upgrading.

### Added

- **Node version pinned** — `.nvmrc` (22) + `engines.node >=22.12` in `package.json`; the build requires Node ≥ 22.12.

### Changed

- **`config/cache.php` + `config/queue.php` defaults → `redis`** (were `database`); `.env.example` matched. Tests unaffected — `phpunit.xml` forces `array`/`sync`.
- **Tenant invite email now queued** — `TenantInvitationNotification` implements `ShouldQueue`, so it no longer blocks the request (tenancy-only).
- **Template docs reorganized into `.template/`** — changelog + migration guides moved out of the repo root / `docs/` into a hidden `.template/` dir (with a `README.md` defining the changelog + versioning rules); the root `CHANGELOG.md` is now a stub for the fork's own changelog. No fork code change.
- **`AGENTS.md` slimmed** — optional tenancy + Docker sections reduced to pointers (detail moved to their own docs); "Template version tracking" section removed. Forks: reconcile your `AGENTS.md`, keeping project-specific additions.

### Fixed

- **Docs corrected** — stale Laravel/Inertia versions (now 13 / 3), config-file names, Node requirement, `2`→`3`-layer wording, and the `app/Integrations` phantom dir across `README.md` / `docs/` / `AGENTS.md`; tenancy guides surfaced in the docs sidebar; the full `pnpm check` family documented.

### Migration

See [`migrations/template-v5.1.3.md`](migrations/template-v5.1.3.md) — flip the two config defaults, queue the notification, and ensure Redis is reachable. The `.template/` move needs no fork action.

## v5.1.2 - 06/16/2026

`Docker:` Extracts the PHP-FPM **base stage** of the deploy `Dockerfile` into a pre-built, published image — **`ghcr.io/timothymarois/docker-laravel-base`** ([repo](https://github.com/timothymarois/docker-laravel-base)) — and `FROM`s it instead of compiling PHP extensions on every build. Patch: no schema, API, or app-runtime changes; the produced container is byte-equivalent. **Deploy-time only.**

> Note: the `base` stage (apt + compiling `gd`/`pdo_pgsql`/`pdo_mysql`/`redis`/`pcntl`/… + composer) is the slowest, least-changing part of every build, and Coolify's periodic Docker cleanup prunes the local layer cache — so it was recompiled cold (~minutes) on many deploys. A named, pre-built base runs that compile once in CI, pulls in seconds, and is never evicted by `docker image prune`.

### Changed

- **`Dockerfile` base stage → `FROM ghcr.io/timothymarois/docker-laravel-base:8.4-v1 AS base`.** The apt/extension/pecl/composer block and `ARG PHP_VERSION` are removed; the `build` and `runtime` stages (`FROM base`) are unchanged. The extension superset is identical — the base image is built from the same recipe.
- **PHP-version knob moves** from `ARG PHP_VERSION` to **which base tag you pin** (`:8.4-v1`). Only `8.4` is published today.
- **`template-manifest.json` `docker` block gains a `baseImage` field** recording the pinned tag.
- **Base image source + publish CI** live in the standalone [`docker-laravel-base`](https://github.com/timothymarois/docker-laravel-base) repo (public GHCR package, amd64, built by GitHub Actions). It is the single producer; the template and every fork are consumers.

### Migration

**See [`migrations/template-v5.1.2.md`](migrations/template-v5.1.2.md).** ~5 minutes, mechanical: swap the base stage to the `FROM`, drop `ARG PHP_VERSION`, add `baseImage` to the manifest, redeploy. The base image must be public (it is). Forks on PHP 8.3 stay on the inline base until an `:8.3` base tag is published or they move to 8.4.

## v5.1.1 - 06/16/2026

Replaces the unmaintained **`vuedraggable@4`** drag-and-drop library with **`vue-draggable-plus`**. Patch: no schema, API, or Docker changes. The only component affected is `CustomizeColumns.vue` (data-table column reorder); behavior is identical.

> Note: `vuedraggable@4` ships UMD-only and does `require('vue')`. When the SSR build externalizes it, Rollup emits a default import of Vue's ESM (no default export) → the Inertia SSR server crashes (`The requested module 'vue' does not provide an export named 'default'`) on any page that mounts a `<draggable>`. `vue-draggable-plus` is the maintained Vue 3 successor on the same Sortable.js engine, ships proper ESM/CJS, and is SSR-safe.

### Changed

- **`vue-draggable-plus` replaces `vuedraggable`** in `package.json`. Same Sortable.js engine; props (`animation`, `handle`, `group`, `disabled`, `ghost-class`, `chosen-class`, `drag-class`, `filter`) carry over.
- **`CustomizeColumns.vue` migrated** — vuedraggable's `#item` scoped slot → `vue-draggable-plus`'s default slot with a `v-for`; `item-key` → `:key`; the `:move` lock guard → `@move` reading the raw Sortable `MoveEvent` via a `data-column-key` attribute.
- **`vite.config.js` simplified** — removed `optimizeDeps.include: ['vuedraggable']` and the `ssr: { external: ['vuedraggable'] }` block. `resolve.dedupe: ['vue']` and `optimizeDeps.exclude: ['vue']` stay.

### Migration

**See [`migrations/template-v5.1.1.md`](migrations/template-v5.1.1.md).** Only forks that use `<draggable>` (i.e. ship `CustomizeColumns.vue` or any other draggable) need the component migration; every fork should swap the dependency and clean `vite.config.js`. ~10 minutes.

## v5.1.0 - 06/15/2026

Adds a canonical **Docker deploy setup** for Coolify — `Dockerfile`, `.dockerignore`, and an organized `docker/` tree — and renames `template-version.json` → **`template-manifest.json`**. Minor/additive: no framework, schema, or runtime API changes. Forks adopt the Docker files by copying them; the manifest rename applies to every fork.

### Added

- **Universal multi-stage `Dockerfile`** — PHP 8.4 (`ARG PHP_VERSION`), full extension superset (`pdo_mysql`, `pdo_pgsql`, `redis`, `sockets`, `gd`, `intl`, `zip`, `bcmath`, `pcntl`, `opcache`, `exif`, `gmp`), Node 22, layer-cached composer + pnpm `build-ssr`, OPcache + JIT. supervisord in the foreground (`-n`).
- **Organized `docker/`** — `config/` (nginx.conf · php.ini · supervisord.conf), `deploy/` (entrypoint.sh · pre-deployment.sh · post-deployment.sh), and `README.md`. **One universal setup — no "simple vs full" variant.** supervisord ships every process (php-fpm · nginx · inertia-ssr · horizon · scheduler; reverb optional); each project enables only what it needs and deletes the rest.
- **Deploy lifecycle scripts** — `entrypoint.sh` (Start phase: `app:ensure-storage` + `optimize` + `storage:link`), `pre-deployment.sh` (Coolify Pre-deployment Command — OLD container, for maintenance/backups), `post-deployment.sh` (Coolify Post-deployment Command — NEW container, for migrations). Pre/post ship even when empty so the Coolify commands are wired once and deploy logic lives in versioned scripts.
- **Trusts the reverse proxy.** `bootstrap/app.php` adds `trustProxies(at: '*')` (reading `X-Forwarded-For/Host/Port/Proto`) so Laravel sees the real HTTPS scheme behind Coolify/Traefik and generates `https://` URLs. Without it, a TLS-terminated app emits `http://` links (Ziggy, redirects, asset URLs) that the browser blocks as **mixed content** — the #1 "login doesn't work behind the proxy" gotcha.

### Changed

- **Removed: `template-version.json` → renamed to `template-manifest.json`.** Declares the template `version` AND the project's Docker requirements: `docker.{php, pkg, build}` plus a `requires` block (`database`, `redis`, `ssr`, `horizon`, `scheduler`, `reverb`). The single machine-readable "what this project needs"; `docker/README.md` → "This project's setup" is the human mirror. The old `docker.variant` field is dropped.

### Migration

**See [`migrations/template-v5.1.0.md`](migrations/template-v5.1.0.md)** — the agent-runnable guide with verification commands. Summary:

**Part A — rename the manifest (EVERY fork):**

1. `git mv template-version.json template-manifest.json`.
2. Drop `docker.variant`; add a `requires` block declaring the project's needs, e.g. `"requires": { "database": true, "redis": true, "ssr": true, "horizon": true, "scheduler": false, "reverb": false }`.
3. Point any tooling that read `template-version.json` at `template-manifest.json`.

**Part B — adopt the Docker setup (per fork moving to Coolify):**

1. Copy `Dockerfile`, `.dockerignore`, and the `docker/` tree into the fork.
2. Fill `docker/README.md` → "This project's setup" and `template-manifest.json`'s `docker.requires` to match the app.
3. In `docker/config/supervisord.conf`, delete the OPTIONAL process blocks the project doesn't use. Set Dockerfile knobs (`ARG PHP_VERSION`, build command, pnpm/npm). DB-less? Leave `post-deployment.sh` empty.
4. Put migrations in `docker/deploy/post-deployment.sh`.
5. In Coolify: Build Pack = Dockerfile, Port = 80, Health check = `/up`; add DB/Redis resources to match `requires`; wire **Pre-deployment Command** = `sh /var/www/html/docker/deploy/pre-deployment.sh` and **Post-deployment Command** = `sh /var/www/html/docker/deploy/post-deployment.sh`; set domains with `https://`. Bump `template-manifest.json` `version` to `5.1.0`.

## v5.0.1 - 06/11/2026

Security/audit patch on top of v5.0.0. Clears `pnpm audit` + `composer audit` findings and adds a `typecheck` step to `check:js`. No framework, schema, or runtime API changes — drop-in for every fork. One minor behavior change in `useSelectableOptions` (noted below).

### Added

- **New `typecheck` script** (`tsc --noEmit`), now part of `check:js`. Adds `vue-tsc` to devDeps. Type-safety fixes to make it pass: new `env.d.ts` + `.d.ts` files for `useEcho`/`useFormSubmit`, a generic re-type of `useSelectableOptions`, and `TagsInputVariant` moved to `tags-input/types.ts`.

### Changed

- **Security bumps.** JS: `pnpm.overrides` force `shell-quote >=1.8.4` and `yaml >=2.8.3`. Composer: in-range Symfony refresh (`http-foundation`, `routing`, `polyfill-*`). No `composer.json`/`package.json` constraint changes.
- **⚠️ `useSelectableOptions` behavior change.** `normalizedOptions` now always augments object options with normalized `label`/`value` keys (spreading the original) instead of passing them through untouched. Displayed labels and emitted values are unchanged. If your fork reads `normalizedOptions` for the original object shape (deep-equality, key-absence), audit those reads.

### Migration

Mechanical, ~5 minutes:

1. Copy the changed files into your fork (14 files — `package.json`, `pnpm-lock.yaml`, `composer.lock`, the new `*.d.ts` + `tags-input/types.ts`, and the `useSelectableOptions` / combobox / select-popover / tags-input edits).
2. `composer install && pnpm install --frozen-lockfile`.
3. Audit any direct reads of `useSelectableOptions`' `normalizedOptions` (see behavior change above).
4. Run `pnpm check:js`. Bump `template-version.json` to `5.0.1` once green.

> Note: the `typecheck` gate uses `tsc`, which covers `.ts`/`.d.ts` only — it does not deep-typecheck `.vue` `<script setup>` blocks.

## v5.0.0 - 05/24/2026

Three bundled upgrades, applied as independent chunks (see Migration): **Laravel 12 → 13**, **Inertia 2 → 3**, and **optional multi-tenancy** via `stancl/tenancy ^3.10`. Tenancy is off by default — forks that don't set `TENANCY_ENABLED=true` see zero tenancy behavior change vs v4.5.0. The framework/Inertia bumps apply to every fork; the major version signals those library majors plus the new tenancy model classes + schema additions.

### Added

Multi-tenancy (only active when `TENANCY_ENABLED=true`):

- **DB-per-tenant isolation** (path-mode by default at `/t/{slug}/...`, subdomain mode one env-flip away).
- **Multi-tenant access** — one user → many tenants via `tenant_user` pivot. `User::tenants()` / `Tenant::users()` relationships.
- **Tenant role model** — `Owner` / `Admin` / `Member` (`App\Enums\TenantRole`) with capability methods. Central super-admin role on the `users.role` column (`App\Enums\UserRole`).
- **Full invite lifecycle** — `App\Services\Tenancy\TenantInviteService` (send / accept / decline / revoke) + acceptance routes at `/invites/{token}` + email notification.
- **Membership operations** — `App\Services\Tenancy\TenantMembershipService` (switchTo / leave / remove / changeRole / transferOwnership / isMember / roleOf).
- **Tenant provisioning service** — `App\Services\Tenancy\TenantProvisioningService` (also wraps the `tenancy:provision` CLI).
- **Access control middleware** — `InitializeTenancyBySlug` + `EnsureUserBelongsToTenant` + `EnsureTenantReady` (503 + `Retry-After: 30` for tenants whose provisioning hasn't completed).
- **Tenant lifecycle** — `ready` and `failed` flags on `App\Models\Tenant`, `MarkTenantReady` + `ConditionalDeleteTenantDatabase` listeners (soft-delete preserves the per-tenant DB; hard-delete drops it), and `tenancy:purge-deleted` to force-delete soft-deleted tenants past `config('tenancy.purge_deleted_after_hours')` (default 72). Also sweeps users left with zero tenants after purge (SuperAdmins exempt; `--keep-orphan-users` opt-out).
- **Artisan commands** — `tenancy:enable`, `tenancy:provision`, `tenancy:migrate-existing`, `tenancy:purge-deleted`.
- **Schema:** `users` table gains a `role` string column. Fresh installs get it from the updated base migration; existing forks add it via a one-off migration (see Migration).

> Note (disabled-state): `config/auth.php` is untouched; `App\Models\User` keeps its v4.5.0 behavior (the new `CentralConnection` trait is a no-op when disabled); `tests/Feature/Tenancy/DisabledStateTest.php` locks in the inert contract.

### Changed

- **Laravel 12 → 13** (`laravel/framework ^13.0`, `laravel/tinker ^3.0`). No app-level breaking-change touchpoints in the template (verified: no `VerifyCsrfToken`, `Inertia::lazy()`, `->upsert()`, queue-event listeners, or published pagination views). PHP floor is 8.3 (template already on 8.4). Cosmetic at deploy: L13 hyphenates cache-prefix/session-cookie names — flush cache, expect one re-login.
- **Inertia 2 → 3** (`inertiajs/inertia-laravel ^3.0`, `@inertiajs/vue3 ^3.0`). `config/inertia.php` restructured (page settings nested under `pages`, `testing` simplified, `use_script_element_for_initial_page` removed). `resources/views/app.blade.php` head marker `<title inertia>` → `<title data-inertia>`. Axios stays a direct dependency. No `future` block, `router.cancel()`, or renamed-event usage to migrate in the template.
- **Pint preset bump** (1.27 → 1.29) now enforces `fully_qualified_strict_types` — reformats many files cosmetically. Two Larastan findings fixed (`InertiaDataTableOptions` dead null-coalesce; `InitializeTenancyBySlug` redundant nullsafe).
- **CI fix:** `tenancy-enabled.yml`'s Tenant step now passes `--do-not-fail-on-empty-test-suite`. Pest 4.7 exits `1` on an isolated empty suite, and `tests/Tenant/` is an intentionally-empty placeholder for fork-authored tenant tests.
- **Dependencies:**
  - *Bumped (major):* `laravel/framework ^12 → ^13`, `laravel/tinker ^2.9 → ^3.0`, `inertiajs/inertia-laravel ^2 → ^3`, `@inertiajs/vue3 ^2 → ^3`.
  - *Added:* `stancl/tenancy ^3.10` (+ transitive `stancl/jobpipeline`, `stancl/virtualcolumn`, `facade/ignition-contracts`).
  - *Temporary pin:* `soloterm/solo ^0.5 → dev-main` — tagged releases cap at Laravel 12; `main` supports L13 but isn't tagged yet. Dev-only; revert to a stable tag once one ships.
  - *Held back deliberately:* `phpunit` stays `^12` (Pest 4 requires PHPUnit 12 — do **not** bump to 13). `spatie/laravel-sitemap` stays `^7` (7.4 added L13 support). JS tooling majors (Vite 8, ESLint 10, TypeScript 6, Stylelint 17, lucide 1.0, unplugin-auto-import 21) intentionally **not** taken.
  - *In-range refresh:* all other composer + npm deps updated to latest patch/minor (Horizon, Reverb, Socialite, Cashier, Sentry, Pest, Pint, Vue, Tailwind, TipTap, reka-ui, Vitest, axios, etc.).

### Migration

**See [`migrations/template-v5.0.0.md`](migrations/template-v5.0.0.md)** — restructured into **three independent chunks**, each pointing at its official upgrade guide:

- **Chunk 1 — Dependencies + Laravel 12 → 13** (required). Read https://laravel.com/docs/13.x/upgrade.
- **Chunk 2 — Inertia 2 → 3** (required). Read https://inertiajs.com/upgrade-guide (v3).
- **Chunk 3 — Optional multi-tenancy** (opt-in), with four tracks:
  - **Track A** — fork doesn't want tenancy. ~10 min of mechanical file copying. Scaffolding installed but inert.
  - **Track B** — new project, tenancy on day one. Track A + `php artisan tenancy:enable`.
  - **Track C** — existing project with user data wants tenancy. Track A + `tenancy:enable` + [`docs/guidelines/tenancy-migrating.md`](docs/guidelines/tenancy-migrating.md).
  - **Track D** — fork already has its own tenancy. Track A only; optional alignment later.

Chunks are independent — land and verify each before the next. Bump `template-version.json` only after `pnpm check` is green for every chunk applied.

## v4.5.0 - 05/20/2026

Scaffold correctness pass — bug fixes to template-shipped scaffolding (data-table state, real-time / SSR plumbing, shadcn-vue components, base service, auth middleware, tooling) plus one infrastructure improvement (Ziggy generation moved to the build pipeline so `route()` works identically in browser AND SSR). Most items are drop-in. One behavior change: logout now goes through POST.

### Added

- **Three reusable UI components promoted from a production fork:**
  - **`ViewToggle`** (`components/ui/view-toggle`) — animated grid/list mode switcher with v-model. Use anywhere you have dual-view data surfaces (admin listings, dashboards, file browsers).
  - **`CodeBlock`** (`components/ui/code-block`) — read-only code/snippet display with **syntax highlighting** via `highlight.js` and a language-picker dropdown. Auto-detects on by default; pass `language="php"` (or any registered id) to force, or use `v-model:language` for two-way binding. Bundled languages: bash, css, diff, dockerfile, go, html/xml, ini, javascript, json, markdown, nginx, php, plaintext, python, ruby, shell, sql, typescript, yaml. Syntax theme uses shadcn token variants so dark mode adapts automatically. Hover-revealed copy-to-clipboard button.
  - **`PinInput`** + `PinInputGroup` / `PinInputSeparator` / `PinInputSlot` (`components/ui/pin-input`) — accessible OTP / passcode input built on `reka-ui` primitives. Auto-advance focus, `autocomplete="one-time-code"`, supports `text` and `number` modes.

  All three ship with showcase pages under `admin/components/display/` (CodeBlock, ViewToggle) and `admin/components/forms/` (PinInput). Adds `highlight.js@^11.11.1` as a runtime dep (CodeBlock); reka-ui and Lucide are already in the template.

- **Ziggy generation moved to the build pipeline.** `resources/js/ziggy.js` is now gitignored and produced by `php artisan ziggy:generate` automatically on `composer install` / `composer dump-autoload` (via `post-autoload-dump`) and `pnpm dev` / `pnpm build` / `pnpm build-ssr`. `setup.js` imports the generated config and assigns it to `globalThis.Ziggy`; the new `plugins/inertia/ziggy.js` wires `route()` through the bundled library — so server-rendered `route()` calls produce **real URLs** in the SSR HTML instead of empty placeholders. Better SEO for server-rendered links, fewer hydration-flicker scenarios. Adds `qs-esm@^8.0.1` as a runtime dep (Ziggy library requirement).

### Changed

- **⚠️ `Route::get('logout', ...)` → `Route::post('logout', ...)`** (action required). CSRF hardening: a destructive auth action no longer accepts GET. Template's `Index.vue` and `AppLayout.vue` are updated. `ProfileMenu.vue` now passes an optional `item.method`/`as="button"` through to the Inertia `<Link>`, so menu items can opt into POST. Any fork frontend using `<a href="/logout">` or `<Link href="/logout">` must move to a POST form or `useForm().post(route('auth.logout'))`.

  **Deploy note:** if frontend assets are served with long cache TTLs (CDN, service worker, aggressive browser caching), deploy the frontend bundle atomically with the route change, or flush the CDN before routing traffic. An old client bundle issuing `GET /logout` against the new route gets `405 Method Not Allowed` until it picks up the new JS.

### Fixed

- **`useDataTableOptions` selection state leaked across pages.** Module-scoped `reactive()` is now per-instance. Same composable also captures the `router.on('before')` unsubscribe (was leaking a listener per mount) and uses an SSR-safe `URL` base. Cross-confirmed in two forks.
- **`useEcho` channels never released.** `echo.leave(channelName)` (no `private-` / `presence-` prefix — Echo strips them internally). `useListen` cleanup now also calls `channel.stopListening`.
- **`useModal` leaked open/close listeners.** `onUnmounted` cleanup, guarded by `getCurrentInstance()` so it's safe outside setup.
- **`broadcast(...)->toOthers()` included the sender.** Added an axios interceptor in `bootstrap.js` that sends `X-Socket-Id` from `window.Echo.socketId()`.
- **Bare `route(...)` calls threw `ReferenceError` under SSR.** Ziggy plugin now wires `globalThis.route` to the bundled Ziggy library on both client and SSR — `route()` returns real URLs in SSR, not no-ops.
- **`DropdownMenuItem` dropped `@select` events.** Forward emits via `useForwardPropsEmits`.
- **`AccordionContent` flashed open on mount and snapped closed.** Replaced `setTimeout(200)` with `transitionend` + double-`rAF`; added `hasMounted` gate.
- **`AccordionItem` was missing `data-state="open|closed"`.** Required for any downstream `[data-state=open]:` Tailwind variants.
- **`TabsTrigger` was missing `data-state` attribute.** Now emits `active|inactive`.
- **`DropdownMenuSubTrigger` / `ContextMenuSubTrigger` had unsized leading icons.** Added `gap-2 [&>svg:first-child]:size-4 [&>svg:first-child]:shrink-0`.
- **`DialogConfirmation` had no `<slot />`.** Callers can now render arbitrary children between header and footer.
- **`ScrollFrame` over-counted height on mobile.** `100vh` → `100dvh`; new `--mobile-nav-offset` CSS variable (default `0px`) lets forks subtract a fixed bottom nav.
- **Horizontal layout shifted when content scrollbar toggled.** New `.scroll-gutter-stable` CSS utility (`scrollbar-gutter: stable`) applied by `Content.vue` when scrollable. Reserves the gutter so nothing reflows.
- **`Caster::castToJson` crashed on non-string scalars.** Added an `is_string` guard before `json_decode`.
- **`ModelService::listPaginated` returned flickering pages on tied sort values.** Stable `orderBy('id', 'asc')` tiebreaker. Also accepts an optional `$options['page']` override so non-HTTP callers (commands, jobs, MCP tools) can drive pagination directly.
- **`ModelService` lacked reusable search/with helpers.** Added `applySearch()` (grouped `where`, so the OR-chain doesn't leak across filters) and `applyWith()`. `UserService` updated to use the helper.
- **`LoginRequest::authenticate` revealed account existence via timing.** Dropped the `User::where('email')->first()` pre-flight; calls `Auth::attempt(...)` directly.
- **`EnsureUserIsActive` crashed on sessionless requests.** Returns `403` for `expectsJson()` or `api/*` instead of invalidating a non-existent session.
- **`TrackLastSeen::updateLastSeen` had untyped `$user` parameter.** Now `User $user` — PHPStan / IDE win.
- **`StartFresh` `Laravel\Prompts\info()` crashed under non-TTY runs.** Switched to `$this->info(...)`.
- **`pest` ran with PHP's 128M default and OOM'd on parallel workers.** `check:php` now invokes pest with `php -d memory_limit=512M`.
- **`eslint` linted generated Ziggy output.** Added `resources/js/ziggy.js` to ESLint ignores.
- **`vite build` printed a 500 kB chunk advisory.** Bumped `chunkSizeWarningLimit` to 600 — shadcn-vue's bundled primitives exceed the default.
- **`<html>` was missing `lang` attribute.** Added `lang="en"`.
- **Dark-mode FOUC on first paint.** Synchronous inline script in `app.blade.php` reads the VueUse `vueuse-color-scheme` storage key and adds `.dark` to `<html>` before Vite mounts.
- **Error pages and Header used hardcoded `text-gray-*` / `text-slate-*`.** Replaced with `text-foreground` / `text-muted-foreground` tokens; added `min-w-0` to `Header.vue`'s flex grow container so long titles truncate.
- **`Login.vue` / `Register.vue` / `Index.vue` used hardcoded paths.** Now `$route('login')` / `$route('register')` / `$route('auth.logout')`.

### Migration

See [`migrations/template-v4.5.0.md`](migrations/template-v4.5.0.md) for the agent-runnable guide with verification `grep` commands for every group. After applying, bump the fork's `template-version.json` → `4.5.0` and run `pnpm check` (must be green).

> Note (deferred): `useScroll.ts` rewrite (ref-counted iOS body-pin) — user-visible, wants its own release. `HandleInertiaRequests.php` user-prop narrowing to `->only([...])` — breaks forks reading user fields beyond `id|name|email`; needs a fork-side audit. Pest `--parallel` — needs a parallel-safety audit of the suite first.

## v4.4.0 - 04/26/2026

Optional Google Analytics (gtag.js) scaffold and a `template-version.json` lineage marker so every fork can declare which template version it's aligned with — regardless of whether the fork keeps its own product `CHANGELOG.md`. Minor/additive.

### Added

- **`services.google_analytics.measurement_id`** config block reading `GOOGLE_ANALYTICS_ID`.
- **gtag.js loader** — `@if ($gaId = config('services.google_analytics.measurement_id'))` block in `resources/views/app.blade.php` that emits the standard gtag.js snippet only when the id is set.
- **`tests/Feature/GoogleAnalyticsTest.php`** — verifies the snippet is emitted when configured and omitted when empty.
- **`GOOGLE_ANALYTICS_ID=`** placeholder in `.env.example` with usage notes.
- **`template-version.json`** at the repo root with four fields: `template` (always `template-laravel-app`), `repo` (canonical template URL), `version` (highest template version fully applied), `updated` (ISO date of the last bump).

### Migration

- Copy `tests/Feature/GoogleAnalyticsTest.php` into your project.
- In `config/services.php`, add the `google_analytics` block reading `env('GOOGLE_ANALYTICS_ID')` (or hard-code your property as the `env()` default to render without env wiring).
- In `resources/views/app.blade.php`, paste the `@if ($gaId = config('services.google_analytics.measurement_id')) ... @endif` block immediately after `@inertiaHead` and before `</head>`.
- Add `GOOGLE_ANALYTICS_ID=` to your `.env.example`.
- Copy `template-version.json` into your project. Set `version` to the highest template version whose migration is *fully* applied. A surveyor can run `jq -r .version */template-version.json` to see every fork's template version at a glance.

## v4.3.0 - 04/20/2026

Deployment and CI reliability — new storage bootstrap command, removal of composer scripts that broke `composer install --no-dev`, and a PHPStan memory fix so `pnpm check:php` passes consistently. Minor.

### Added

- **`app:ensure-storage` Artisan command** — idempotently creates missing `storage/` subdirectories and writes the standard Laravel `.gitignore` inside each. Safe to run on every deploy. Passport OAuth key generation is gated behind a string-based `class_exists` check, so the command works whether or not `laravel/passport` is installed — and produces no IDE/static-analysis errors when absent.
- **Feature tests** covering directory creation, idempotency, partial-tree recovery, `.gitignore` restoration, and the Passport-skip path.

### Changed

- **PHPStan memory limit → `512M`** in both `composer.json` (`analyse` script) and `package.json` (`check:php` script). The default 128M was intermittently crashing PHPStan's parallel worker on this codebase.
- **Removed: `post-install-cmd` from `composer.json`** — it ran `ide-helper:generate`, which fails under `composer install --no-dev` (`barryvdh/laravel-ide-helper` is a dev dependency). This was the common "had to use `--no-scripts` on deploy" failure.
- **Removed: `post-update-cmd` from `composer.json`** — it ran the same `ide-helper:generate` plus a stale `vendor:publish --tag=laravel-assets` (no package in this stack publishes under that tag).

### Migration

- Copy `app/Console/Commands/EnsureStorage.php` and `tests/Feature/EnsureStorageTest.php` into your project.
- In `composer.json`, delete the `post-install-cmd` and `post-update-cmd` entries if they still contain `ide-helper:generate` or `vendor:publish --tag=laravel-assets`. Keep `post-autoload-dump`, `post-root-package-install`, `post-create-project-cmd`, and the named scripts.
- In `composer.json`, change the `analyse` script to `"phpstan analyse --memory-limit=512M"`.
- In `package.json`, change the `check:php` script's `phpstan analyse` invocation to `phpstan analyse --memory-limit=512M`.
- Run `composer update --lock` to refresh the lock file's content hash.
- (Optional but recommended) Add `php artisan app:ensure-storage` to your deploy script.

## v4.2.0 - 02/14/2026

Convention compliance pass across `app/` and `site/` components. Minor.

### Changed

- Replaced Tabler icons with Lucide in all `app/` components (AppLayout, ProfileMenu, Header, SideNav).
- Removed `lang="ts"` from 9 `app/` components — converted to plain JS with runtime `defineProps`.
- Replaced barrel imports with direct file imports across ~55 consumer files.
- **Removed: 7 barrel `index.ts` files** from `app/` and `site/` directories.
- Replaced the inline SVG breadcrumb chevron in Header with Lucide `ChevronRight`.

## v4.1.0 - 02/01/2026

Adds a VitePress documentation site and parallelizes CI checks. Minor.

### Added

- **VitePress documentation site** in `/docs/` — Getting Started, Architecture, and Guidelines sections. New commands: `pnpm docs:dev`, `pnpm docs:build`.

### Changed

- Added ESLint and Stylelint to docs with 4-space indentation.
- Parallelized `pnpm check` using `concurrently` for faster CI.
- Updated AGENTS.md to reference VitePress docs instead of PRDs.

## v4.0.0 - 12/26/2025

Major overhaul: new UI framework, comprehensive testing, real-time features, and optional multi-tenancy. This is the foundation the current template is built on (PrimeVue → shadcn-vue).

### Added

- **UI components (50+)** — Combobox, Command palette, TagsInput, MaskInput, NumberInput, Tiptap rich text editor, Charts (Area, Bar, Line, Pie, Donut, Radar), Carousel, Resizable panels, Context menus, Dropzone file uploads, Loading states, Alerts.
- **Real-time & WebSockets** — Laravel Reverb support, Echo composables (`useChannel`, `usePrivateChannel`, `useListen`), optional WebSocket via `VITE_REVERB_ENABLED`.
- **Security** — SecurityHeaders middleware (X-Frame-Options, HSTS, etc.), custom rate limiting (API, auth, uploads), Horizon access control via allowed-emails config, custom error pages (403, 404, 500, 503), password-rules enforcement.
- **Testing & CI** — Pest PHP (`composer test`), Vitest for JS/Vue (`pnpm test`), a GitHub Actions workflow, and new pnpm scripts (`check`, `check:php`, `check:js`, `lint`, `lint:fix`). All existing PHP tests converted to Pest.
- **SEO & production** — SSR support with docs, SEO/social meta tags (Open Graph, Twitter Cards), sitemap generation command (`php artisan sitemap:generate`), last-seen tracking middleware, user activation status + enforcement.
- **Stylelint** for CSS linting (`pnpm lint:css`).

### Changed

- **Replaced PrimeVue with shadcn-vue** (Radix Vue primitives).
- **Tailwind CSS v3 → v4** with new CSS theme variables.
- **Switched from npm to pnpm** for package management.
- **Removed: `atlas-ui` dependency.**
- Updated AGENTS.md and README.md with comprehensive docs.

### Migration

No formal migration guide — v4.0.0 predates the `migrations/` guides. Forks adopting it rebuilt from the template (PrimeVue/atlas-ui → shadcn-vue is not an in-place upgrade).
