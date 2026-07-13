# Migrating a fork to template v5.4.0

v5.4.0 replaces the VitePress **`docs/` site** with a Markdown-only **`.ai/` knowledge system** and
restructures `AGENTS.md` onto it. Every fork. Minor — docs / agent-tooling only; no schema, API, runtime,
or Docker-core change.

**Why:** documentation and agent context now live beside the code as plain Markdown under `.ai/` —
always-loaded orientation (`BRIEF`, `CODEMAP`) plus an on-demand knowledge ladder
(`research → design → PRD`, with `guides` and `lessons` alongside). No build step, no `node_modules`, no
drift between a separate docs site and the app. `AGENTS.md` points into it and gains a light-by-default
load order and a code-documentation convention.

> **Local dev and runtime are unchanged.** This touches docs and `AGENTS.md` only. `pnpm check` drops its
> `docs` build/lint step but is otherwise identical.

## Prerequisites

- Fork is on template **v5.3.0**. Check: `jq -r .version template-manifest.json` → expect `5.3.0`.
- Baseline `pnpm check` is green.
- A fresh clone/pull of the template is available as `<template>` to copy from.

---

## Part A — Add the `.ai/docs` knowledge system (every fork)

Copy the knowledge homes and the scratch space into your existing `.ai/`:

```sh
cp -R <template>/.ai/docs .ai/docs
cp -R <template>/.ai/tmp  .ai/tmp
```

You now have `.ai/docs/README.md` (the map) and six homes — `research/ references/ design/ PRD/ guides/
lessons/` — each with its own `README.md` (rules) + `TEMPLATE.md`, plus the template's how-to guides
(`write-tests`, `logging`, `health-checks`, `tenancy-usage`, `tenancy-migrations`) and lessons. **Read
`.ai/docs/README.md` before adding to any home.**

> `.ai/tmp/` is a git-ignored scratch space (keeps only `.gitkeep` + `.gitignore`). Use it for throwaway
> AI-generated files; never for durable knowledge.

## Part B — Fold `.ai/MEMORY.md` into `.ai/docs/lessons/` (forks that have it)

The new model has **no `MEMORY.md`** — learned knowledge lives in `.ai/docs/lessons/<area>.md`, split by
area. Move your fork's content:

- Each **Known Trap / Gotcha** or **Lesson** → an entry in the matching `lessons/<area>.md`
  (`backend.md`, `frontend.md`, …), in the lessons format (`What happened → Root cause → Fix → Rule`,
  `L-<AREA>-<n>` IDs — see `.ai/docs/lessons/README.md`).
- **Preferences** about how *you* like to work do **not** belong in the repo — drop them (they live in
  your own agent memory, not this codebase).

Then remove the file:

```sh
git rm .ai/MEMORY.md
```

> The template's own MEMORY content already shipped as `lessons/backend.md` + `lessons/template-machinery.md`
> in Part A. Only migrate lessons your fork added on top.

## Part C — Restructure `AGENTS.md` onto the model (every fork)

`AGENTS.md` is reorganized to: identity → **Before You Work** (always read `.ai/BRIEF.md` +
`.ai/CODEMAP.md`; pull `.ai/docs/*` on demand) → Hard Gates → Never → Tech Stack → Architecture → Best
Practices → **Code documentation** → Directory Structure → Build/Test/Run → Documentation Duties (incl.
"read a home's `README.md` before writing a doc") → Definition of Done. Every reference to the VitePress
`docs/` site is replaced with the `.ai/docs/` homes.

- **If your fork has NOT customized `AGENTS.md`** — take the template's:

  ```sh
  cp <template>/AGENTS.md AGENTS.md
  ```

- **If your fork HAS customized `AGENTS.md`** — start from the template's new file and re-apply your
  fork's own additions (extra stack rules, project-specific gates) into the matching sections. Then
  confirm no VitePress `docs/` references remain:

  ```sh
  grep -niE 'vitepress|docs/guidelines|docs/architecture|check:docs|pnpm docs:' AGENTS.md   # expect no matches
  ```

New and worth keeping: the **Code documentation** convention — complex Services/Actions get a PHPDoc
block (intent · contract · edge cases; TSDoc for non-trivial composables/utils), and a method
implementing a `PRD/` requirement cites its `R-<AREA>-<n>`. Trivial code stays uncommented.

## Part D — Migrate & remove the VitePress `docs/` site (forks that have it)

Skip this Part if your fork already deleted its `docs/` VitePress site.

**1. Migrate fork-specific pages.** Any `docs/guidelines/*` your fork added → `.ai/docs/guides/<slug>.md`
using `.ai/docs/guides/TEMPLATE.md`. Fold `docs/architecture/*` into `.ai/CODEMAP.md` / `AGENTS.md`, and
`docs/getting-started/*` into your root `README.md`. (The template's own guideline pages already shipped
as guides in Part A — only migrate what your fork added.)

**2. Remove the site and its wiring.**

```sh
git rm -r docs
```

`package.json` — drop the docs scripts and the docs step from `check`:

```diff
-    "check:docs": "pnpm docs:lint && pnpm docs:lint:css",
-    "check:build": "pnpm build && pnpm exec vite build --ssr && pnpm docs:build",
-    "check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:docs\" && pnpm check:build",
+    "check:build": "pnpm build && pnpm exec vite build --ssr",
+    "check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" && pnpm check:build",
```

```diff
-    "docs:dev": "cd docs && pnpm dev",
-    "docs:build": "cd docs && pnpm build",
-    "docs:lint": "cd docs && pnpm lint",
-    "docs:lint:fix": "cd docs && pnpm lint:fix",
-    "docs:lint:css": "cd docs && pnpm lint:css",
-    "docs:lint:css:fix": "cd docs && pnpm lint:css:fix",
-    "docs:check": "cd docs && pnpm check"
```

Make `check:all` the last script (no trailing comma).

`.gitignore` — drop the VitePress block:

```diff
-# VitePress
-docs/.vitepress/cache/
-docs/.vitepress/dist/
-docs/node_modules/
```

`README.md` — replace the "Documentation … powered by VitePress" section with a pointer to `.ai/` (see the
template's `README.md`).

Sanity check — only `.template/` history should still mention it:

```sh
grep -rniE 'vitepress|docs:build|check:docs|docs:dev' --include='*.json' --include='*.md' --include='*.yml' . \
  | grep -vE 'node_modules|vendor|\.ai/|\.template/'   # expect no matches
```

---

## Verify

```sh
php artisan optimize:clear
test ! -d docs && echo "docs/ removed"
ls .ai/docs/README.md .ai/docs/guides .ai/docs/lessons .ai/tmp/.gitkeep   # new system present
test ! -f .ai/MEMORY.md && echo "MEMORY.md gone"
grep -c '\.ai/docs' AGENTS.md          # AGENTS points into the new system (> 0)

pnpm check                              # PHP + JS + build, all green (no docs step)
```

Open `.ai/docs/README.md` and confirm the map reads right; open `AGENTS.md` and confirm **Before You Work**
opens `.ai/BRIEF.md` + `.ai/CODEMAP.md`.

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.4.0"`. Don't copy this changelog into the fork —
the manifest `version` is the record (see the template's [`.template/README.md`](../README.md)).
