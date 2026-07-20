# Migrating a fork to template v5.5.0

v5.5.0 replaces the `.ai/` knowledge system with **`.knowledge/`**, the versioned payload from
[knowledge-template](https://github.com/timothymarois/knowledge-template) (adopts its **v1.0.0**), and
restructures `AGENTS.md` onto it. Every fork. Minor — docs / agent-tooling only; no schema, API, runtime, or
Docker-core change.

**Why:** the agent-docs system is now its own versioned, linted product shared across every repo — not just
this template's forks. `.knowledge/` ships a linter (`doc-lint`) that fails the build when a doc drifts, a
closed PRD and OVERVIEW schema, and a checksum manifest that proves a repo runs the version it claims. `.ai/`
had none of that. The writing standards live in `.knowledge/guides/` and upgrade by version bump, the same
way the Docker core does.

> **Local dev and runtime are unchanged.** This touches docs and `AGENTS.md` only. `pnpm check` regains a
> `check:docs` step (the knowledge linter); nothing else moves.

> **⏭️ Skipping v5.4.0?** If your fork is on **v5.3.0 or below** it never adopted `.ai/`. **Do not migrate to
> v5.4.0 first** — it would install `.ai/` only for this release to delete it. Skip straight here and follow
> **Path 2** below, which adopts `.knowledge/` fresh. Set your manifest to `5.5.0` when done.

## Prerequisites

- **Path 1 (from v5.4.0):** `jq -r .version template-manifest.json` → `5.4.0`, and `.ai/` exists.
- **Path 2 (from v5.3.0 or below):** no `.ai/` directory. You are skipping v5.4.0 deliberately.
- Baseline `pnpm check` is green.
- Python 3 is available (`python3 --version`) — the linter needs it, stdlib only, nothing to install.
- Have the knowledge-template **v1.0.0** payload to copy from: clone
  `https://github.com/timothymarois/knowledge-template` (its `template/.knowledge/` is the payload), or copy
  `.knowledge/` from a fresh clone of this template at v5.5.0. Referred to below as `<kt>/template/.knowledge`.

---

## Part A — Copy in the `.knowledge/` payload (every fork)

```sh
cp -R <kt>/template/.knowledge .knowledge
```

You now have the homes (`prd/`, `prd-drafts/`, `research/`, `references/`, `tmp/`), the writing standards
(`guides/docs-*.md`), the linter (`scripts/`), the orientation trio + `OVERVIEW.md` as fill-in templates, the
`.version` stamp, and `.payload-manifest` (integrity checksums). **Read `.knowledge/README.md`
before writing to any home.** The full rules for the docs system are knowledge-template's — this migration
only tells you how to get your fork onto it.

> **Take the latest `.knowledge/`, not just what this template bundles.** This template pins a known-good
> knowledge-template version as a **floor, not a ceiling**. Check knowledge-template's head (its `VERSION` /
> latest tag) against the `.version` you just copied; if newer, upgrade `.knowledge/` to latest by applying
> that repo's `.changes/` migrations in order. The docs system is versioned independently of this template —
> keep it current even when the template is behind. The integrity manifest + `doc-lint` verify the result.

## Part B — Move your content into it

**This is the knowledge-template v1.0.0 adoption. Follow its guide, do not reinvent it:** read
`<kt>/.changes/2026-07-16-v1.0.0.md` (the migration off the legacy `.ai/docs` scaffold) and
`<kt>/ADOPT.md`, and do what they say. In short:

- **Path 1 (from v5.4.0)** — your `.ai/` has real content. Move it: `.ai/BRIEF.md`, `.ai/CODEMAP.md`,
  `.ai/MEMORY.md` become `.knowledge/BRIEF.md` / `CODEMAP.md` / `MEMORY.md`, **reshaped to the new
  `guides/docs-*.md` standards** (they are not a byte copy — the formats changed). Your `.ai/docs/guides/*`
  how-tos move under `.knowledge/guides/` and get catalogued in `.knowledge/guides/README.md`. Any real
  `PRD/` or `PRD-drafts/` content converts to the glyph-table format per `guides/docs-prd.md`; placeholder
  `TEMPLATE.md`/`README.md` files are dropped (the payload ships their replacements).
- **Path 2 (from v5.3.0 or below)** — you have no `.ai/`. Adopt fresh per `<kt>/ADOPT.md`: **research the
  codebase** and write `BRIEF.md`, `CODEMAP.md`, `OVERVIEW.md` for your fork, leaving no `<project>`
  placeholder. `MEMORY.md` starts empty.
- **Declare your components** in `.knowledge/prd/README.md` — the ontology is **your call**, the shipped
  `base-`/`entity-`/`flow-` default or names that fit your domain. Do not guess it silently.

> **Mold this to your fork — and let the code decide.** A migration guide cannot know what your fork became.
> The docs must describe the code that ACTUALLY EXISTS: verify every path in `CODEMAP.md` against the real
> tree. Both directions matter — do not carry a description of a feature you removed, and do not drop one you
> still ship. Tenancy is the common trap: the template ships it installed-but-inert, so most forks still have
> the code and their docs should keep it (marked inert), *not* delete it. Only a fork that genuinely tore out
> the tenancy code omits it from the docs.

## Part C — Restructure `AGENTS.md` onto `.knowledge/`

Your `AGENTS.md` points at `.ai/` (Path 1) or a pre-`.ai/` layout (Path 2). Rewrite it following
`.knowledge/guides/docs-agents.md`, whose built-in template is the shape to match. Keep the ship-as-written
sections (the `.knowledge/` load order in *Before you work*, *Documentation duties*, *Definition of done*)
close to verbatim; fill the stack sections from your fork's reality.

- Every `.ai/...` path becomes `.knowledge/...`; every `.ai/docs/PRD/` reference becomes `.knowledge/prd/`.
- Keep the rulebook lean — move long code-example galleries into a `.knowledge/guides/` page (this template
  ships them as `guides/stack-examples.md`) and keep a couple of inline `✅`/`❌` pairs. The rules stay in
  `AGENTS.md`; the galleries are pulled on demand.
- This template's own `AGENTS.md` at v5.5.0 is the worked example — read it, and adapt, don't copy (your
  stack sections differ).

## Part D — Re-add the doc gate

v5.4.0 dropped the old docs step. Put the knowledge linter back into `pnpm check`:

```jsonc
// package.json → scripts
"check:docs": "python3 .knowledge/scripts/test_doc_lint.py && python3 .knowledge/scripts/doc-lint .knowledge",
"check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:docs\" && pnpm check:build",
```

Add the CI workflow `.github/workflows/doc-lint.yml` (copy this template's) so the linter runs on push and PR.

## Part E — Remove `.ai/` (Path 1 only)

Once every real file is migrated and `.knowledge/` lints clean, delete the old home (a `git rm` of migrated
files is a normal, staged, pre-commit step — not a "destructive" git command):

```sh
git rm -r .ai
```

Path 2 forks have no `.ai/` to remove.

## Part F — Apply the shipped bug fixes (every fork)

Three small fixes to template-managed code — apply each if your fork still carries the file. If you haven't
customized the file, copy the template's version wholesale; if you have, apply just the change shown.

**F.1 — Data-table scalar guards** (`app/Http/Concerns/InertiaDataTableOptions.php`). A scalar `filters`
reaches the array-typed caster and throws a 500 (and, submitted via `POST /admin/users/filters`, persists
into the session and crashes the next load); a scalar `viewFields` came back untyped. Guard both back to
their defaults **before** `filters` is cast (after `perPage` is set):

```php
$merged['perPage'] = (int) ($merged['perPage'] ?? $defaults['perPage']);

if (! is_array($merged['filters'])) {       // ← add
    $merged['filters'] = $defaults['filters'];
}

if (! is_array($merged['viewFields'])) {    // ← add
    $merged['viewFields'] = $defaults['viewFields'];
}

$merged['filters'] = Caster::cast($merged['filters'], $filterCasts);
```

**F.2 — `PhoneNumber::normalize()`** (`app/Support/PhoneNumber.php`) kept a stray non-leading `+`
(`415-555-019+` → `(415) 555-019+`). Strip every non-digit before validating the 10-digit number:

```php
$phoneNumber = preg_replace('/\D+/', '', $phoneNumber) ?? '';

if (strlen($phoneNumber) === 11 && $phoneNumber[0] === '1') {
    $phoneNumber = substr($phoneNumber, 1);
}

return strlen($phoneNumber) === 10 ? $phoneNumber : null;
```

The template also adds regression tests (`tests/Feature/UserControllerTest.php` for the scalar params,
`tests/Unit/PhoneNumberTest.php` for the phone helper) — copy them if your fork keeps its suite aligned.

## Part G — Stamp the version

Set your fork's template version:

```sh
jq '.version = "5.5.0"' template-manifest.json > tmp && mv tmp template-manifest.json
```

`.knowledge/.version` stays `1.0.0` — that is the **knowledge-template** version, tracked separately from the
template version. They are two different version lines.

---

## Verify

- `python3 .knowledge/scripts/doc-lint .knowledge` → `doc-lint: OK`.
- `python3 .knowledge/scripts/test_doc_lint.py` → all checks pass.
- `pnpm check` is green, and now includes `check:docs`.
- A scalar `filters` or `viewFields` on the admin users routes returns 200, not 500, and
  `PhoneNumber::normalize('415-555-019+')` returns `null` rather than a `+`-tailed string (Part F).
- No `.ai/` directory remains (Path 1); no `.ai/` path reference remains anywhere except `.template/` history.
- No `<project>` or `_(none yet)_` placeholder remains in `BRIEF.md`, `CODEMAP.md`, `OVERVIEW.md`, or any
  catalog.
- `.knowledge/OVERVIEW.md` describes **your fork's product**, not this template.
- `template-manifest.json` reads `5.5.0`.
