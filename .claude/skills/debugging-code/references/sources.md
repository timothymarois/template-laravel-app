# Debugging source basis

This package is a Rundesk synthesis of framework documentation, the debugging tools each ecosystem
ships, and practitioner writing on debugging as a skill. `SKILL.md` holds the language-agnostic
workflow; the per-framework references hold the mechanics. Use this file to audit or update a claim.

**Read in this order of authority.** Framework documentation states what a tool reports; practitioner
sources carry the judgement about which observation to make next. Verified in **August 2026**,
against Laravel 13, Laravel Herd 1.28.0, and Vue 3.5.

## Debugging as a skill

- [Some ways to get better at debugging](https://jvns.ca/blog/2022/08/30/a-way-to-categorize-debugging-skills/) —
  **Julia Evans**. The five categories: learn the codebase, learn the system, learn your tools, learn
  strategies, get experience. Her finding that experts do not use different strategies but "formed
  more correct hypotheses and were more efficient at finding the fault" is why `SKILL.md` is a
  hypothesis loop rather than a tool list.
- [A debugging manifesto](https://jvns.ca/blog/2022/12/08/a-debugging-manifesto/) and
  [The Pocket Guide to Debugging](https://jvns.ca/blog/2022/12/21/new-zine--the-pocket-guide-to-debugging/) —
  reproduce the bug, be rigorous, divide the problem space in half, print stuff out. Also the
  argument for leaving the bug in place and understanding it before fixing it.
- [What does debugging a program look like?](https://jvns.ca/blog/2019/06/23/a-few-debugging-resources/)

## Laravel

- [Error handling](https://laravel.com/docs/13.x/errors) — the `bootstrap/app.php` exception handler,
  `report()` and `render()` callbacks, global and per-exception log context, log levels, `dontReport`,
  throttling, and the `APP_DEBUG` production warning.
- [Logging](https://laravel.com/docs/13.x/logging) · [Queues](https://laravel.com/docs/13.x/queues) —
  `queue:failed`, the `failed()` hook, `afterCommit`, timeout versus retry_after.
- [Telescope](https://laravel.com/docs/13.x/telescope) — the request-level recorder, and the
  statement that it "is not recommended for production environments."
- [Pulse](https://laravel.com/docs/13.x/pulse) — the production-safe performance view.
- [Nightwatch versus Telescope](https://nightwatch.laravel.com/nightwatch-vs-telescope) — which tool
  answers which question.
- [Configuration](https://laravel.com/docs/13.x/configuration) — the `config:cache` / `env()`
  warning, which is the mechanism behind "works locally, null in production."
- [Eloquent](https://laravel.com/docs/13.x/eloquent) — `preventLazyLoading` as an N+1 detector, and
  mass operations firing no model events.
- [Debugging and logging in Laravel applications](https://laravel-news.com/debugging-and-logging-in-laravel-applications) —
  Laravel News.

## Laravel Herd

- Herd command line for [macOS](https://herd.laravel.com/docs/macos/advanced-usage/herd-cli) and
  [Windows](https://herd.laravel.com/docs/windows/advanced-usage/command-line), plus
  [PHP versions](https://herd.laravel.com/docs/macos/technology/php-versions) — map the exact site,
  isolated PHP binary and ini, debugger, logs, TLS state, and service status before changing the
  application. They support the good/bad runtime-identity pair in `herd.md`. Verified on August 7,
  2026 against the Herd 1.28.0 command surface recorded by the changelog below.
- [Sites](https://herd.laravel.com/docs/macos/getting-started/sites) and
  [managing sites](https://herd.laravel.com/docs/macos/sites/managing-sites) — parked directories,
  explicit links, per-site isolation, and the documented destructive Site Manager delete action;
  the [changelog](https://herd.laravel.com/docs/macos/changelog/index) records `herd link`
  updating `.env` `APP_URL` and later adding `--update-env` to force that rewrite.
- Herd's [macOS](https://herd.laravel.com/docs/macos/troubleshooting/common-issues) and
  [Windows](https://herd.laravel.com/docs/windows/troubleshooting/common-issues) troubleshooting
  pages — a Herd 404, bad gateway, or DNS failure precedes Laravel; the underlying resolver and
  helper differ by platform.
- [Dumps](https://herd.laravel.com/docs/macos/debugging/dumps) — Herd's extension injects during
  early bootstrap, supporting the disable-and-repeat experiment when capture changes behavior.
- [Browser-versus-CLI PHP mismatch](https://github.com/beyondcode/herd-community/issues/831) and
  [CLI ini mismatch](https://github.com/beyondcode/herd-community/issues/267) — Herd maintainers
  traced reproduced failures to an older PHP earlier on `PATH` and to the wrong CLI ini; both
  support comparing plain commands with Herd's site-aware proxies before changing code.

## Vue and Nuxt

- [Vue DevTools features](https://devtools.vuejs.org/getting-started/features) — what each tab
  answers, including the Vite inspector that maps a DOM node to the component that rendered it.
- [Vue DevTools FAQ](https://devtools-v6.vuejs.org/guide/faq) — lazy reactivity, and why the force
  refresh button exists. This is a real source of false conclusions.
- [Reactivity in depth](https://vuejs.org/guide/extras/reactivity-in-depth) — `onRenderTracked` and
  `onRenderTriggered`, and the documented recommendation to put a `debugger` statement in the
  callback.
- [Composition API lifecycle hooks](https://vuejs.org/api/composition-api-lifecycle) ·
  [Watchers](https://vuejs.org/guide/essentials/watchers.html) — `onTrack` / `onTrigger`.
- [Server-side rendering](https://vuejs.org/guide/scaling-up/ssr.html) — the three documented
  hydration-mismatch causes, and that Vue recovers automatically "at a performance loss," which is
  why the warning gets ignored.
- [Performance](https://vuejs.org/guide/best-practices/performance.html) — `app.config.performance`.
- [Nuxt data fetching](https://nuxt.com/docs/4.x/getting-started/data-fetching) — the documented
  double-fetch when `$fetch` is used bare in `setup`.
- [Debugging guide: why your Vue component isn't updating](https://michaelnthiessen.com/debugging-guide-why-your-component-isnt-updating) —
  **Michael Thiessen**. The practical checklist behind the symptom table.

## Related skills in this catalog

The framework references here deliberately stop at *how to observe*. The rules a symptom violates
live with the language:

- `using-laravel` — especially queues, Eloquent, and performance.
- `using-vuejs` — especially reactivity, SSR, and separation of concerns.
- `testing-code` and `reviewing-code` own the surrounding process: proving a correction with a
  test that fails without it, and judging a completed change.

## What this package deliberately does not cite

- Tool round-ups that list every debugger without saying which question each answers.
- Version-specific screenshots and UI walkthroughs, which age faster than the tools.
- "Top N debugging tips" posts with no mechanism behind the advice.


## Herd

- Herd command line for [macOS](https://herd.laravel.com/docs/macos/advanced-usage/herd-cli) and
  [Windows](https://herd.laravel.com/docs/windows/advanced-usage/command-line) — site, PHP, TLS,
  debugging, logging, and service commands; verified against Herd 1.28.0 on August 7, 2026
- [Sites](https://herd.laravel.com/docs/macos/getting-started/sites) and
  [managing sites](https://herd.laravel.com/docs/macos/sites/managing-sites) — parked versus linked
  directories, per-site isolation, and the destructive Site Manager delete action; the
  [changelog](https://herd.laravel.com/docs/macos/changelog/index) records `herd link` updating
  `.env` `APP_URL` and later adding `--update-env` to force that rewrite
- [macOS common issues](https://herd.laravel.com/docs/macos/troubleshooting/common-issues) and
  [Windows common issues](https://herd.laravel.com/docs/windows/troubleshooting/common-issues) —
  platform-specific 404, bad gateway, DNS, helper, and log evidence
- [Dumps](https://herd.laravel.com/docs/macos/debugging/dumps) — early PHP extension injection and
  capture-feature isolation
- [Browser-versus-CLI PHP mismatch](https://github.com/beyondcode/herd-community/issues/831) and
  [CLI ini mismatch](https://github.com/beyondcode/herd-community/issues/267) — Herd maintainer
  diagnoses of real failures caused by a different PHP or ini on the command path

## Laravel

- [Error handling](https://laravel.com/docs/13.x/errors) — the `bootstrap/app.php` handler, `report()`, log context, log levels, throttling, and the `APP_DEBUG` production warning
- [Logging](https://laravel.com/docs/13.x/logging) · [Telescope](https://laravel.com/docs/13.x/telescope) — "not recommended for production environments" · [Pulse](https://laravel.com/docs/13.x/pulse)
- [Queues](https://laravel.com/docs/13.x/queues) — failed jobs, `failed()`, `afterCommit`, timeout vs retry_after
- [Configuration](https://laravel.com/docs/13.x/configuration) — the `config:cache` / `env()` warning
- [Eloquent](https://laravel.com/docs/13.x/eloquent) — `preventLazyLoading`, and mass operations not firing events
- [Nightwatch vs Telescope](https://nightwatch.laravel.com/nightwatch-vs-telescope) — which tool answers which question
- [Debugging and logging in Laravel applications](https://laravel-news.com/debugging-and-logging-in-laravel-applications) — Laravel News
- `using-laravel` in this catalog for the underlying rules these symptoms violate

## Vue

- [Vue DevTools features](https://devtools.vuejs.org/getting-started/features) — what each tab answers
- [Vue DevTools FAQ](https://devtools-v6.vuejs.org/guide/faq) — lazy reactivity and force refresh
- [Reactivity in depth](https://vuejs.org/guide/extras/reactivity-in-depth) — `onRenderTracked` / `onRenderTriggered` and the `debugger` technique
- [Composition API lifecycle hooks](https://vuejs.org/api/composition-api-lifecycle) · [Watchers](https://vuejs.org/guide/essentials/watchers.html) — `onTrack` / `onTrigger`
- [Server-side rendering](https://vuejs.org/guide/scaling-up/ssr.html) — the three documented mismatch causes and the automatic-recovery cost
- [Performance](https://vuejs.org/guide/best-practices/performance.html) — `app.config.performance`, prop stability
- [Nuxt data fetching](https://nuxt.com/docs/4.x/getting-started/data-fetching) — the double-fetch warning
- [Debugging guide: why your Vue component isn't updating](https://michaelnthiessen.com/debugging-guide-why-your-component-isnt-updating) — **Michael Thiessen**; the practical checklist this page's first table is built on
- `using-vuejs` in this catalog for the underlying rules these symptoms violate
## Attribution

This package adapts `skills/debugging-code/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, published by Rundesk AI under the MIT License.

Material modifications: the routing description narrowed against its neighbouring packages in this
catalog; stack pointers retargeted to the `using-` packages and marked as non-dependencies;
a maintainer validation record added; and the React, Python, and C++ references removed, this
copy being vendored into a Laravel/Inertia/Vue application.
