# Template

This directory is **tooling for maintaining the template** — its change history, the migration guides for upgrading a fork against it, and the rules for writing them. It's used by whoever manages the template or runs a fork upgrade (typically an agent), **not** by the forked app: nothing here is referenced at runtime or build. A fork can delete `.template/` outright — it's hidden (`.github`-style) precisely because it's meta, not product. (It still ships in clones so an in-place upgrade can read the guides; deleting it just means upgrading from the upstream repo instead.)

> **In a fork?** If this repo is **not** `template-laravel-app` itself, these docs are a snapshot from whenever the fork last synced and may be out of date. For the current changelog, migration guides, and rules, go to the source: **<https://github.com/timothymarois/template-laravel-app>** (`.template/` directory).

## Files

- **[`CHANGELOG.md`](CHANGELOG.md)** — the template's change history. The migration log forks read to stay compatible; each release links its upgrade steps in [`migrations/`](migrations/).
- **[`CHANGELOG-LEGACY.md`](CHANGELOG-LEGACY.md)** — archived pre-v4.0.0 history, frozen for reference.
- **[`migrations/`](migrations/)** — per-release upgrade guides (`template-vX.Y.Z.md`).

## Two changelogs — don't confuse them

| File | Belongs to | Purpose |
|------|------------|---------|
| **`/CHANGELOG.md`** (repo root) | the **forked project** | A stub where a fork tracks *its own* product changes and versions, in whatever format it likes. Ships empty. |
| **`.template/CHANGELOG.md`** (here) | the **template** | The template's own change history — tracking changes to the template itself. The rules below govern this one. |

A fork records which template version it's aligned with in `template-manifest.json` (`version`) — never by copying `.template/CHANGELOG.md` into its own.

---

## Changelog rules

Entries in [`CHANGELOG.md`](CHANGELOG.md) tell a developer, at a glance, **what changed** in a release and **whether upgrading takes work**. Keep them short and skimmable — the technical "how" lives in the migration guide, never here.

Pre-v4.0.0 history is frozen in [`CHANGELOG-LEGACY.md`](CHANGELOG-LEGACY.md) — don't reformat or extend it.

### Entry shape

```
## vX.Y.Z - MM/DD/YYYY

<one-line summary + release type (patch / minor / major)>

> ⚠️ <optional: a breaking change or required action, one line>

### Added
- New capability, one line.

### Changed
- Changed behavior, one line.

### Fixed
- Bug fixed, one line.

### Migration
- See migrations/template-vX.Y.Z.md   (or: None — drop-in.)
```

- Newest release at the **top**, under `# Released`. Date `MM/DD/YYYY`.
- Include only the sections that apply, always in the order **Added → Changed → Fixed → Migration**.

### The sections

- **Added** — a genuinely **new** capability that didn't exist before (a new command, component, config block, opt-in feature). Moving, renaming, reorganizing, or replacing something that already existed is **Changed** — even when new files appear. If nothing net-new shipped, omit this section.
- **Changed** — changes to existing behavior; also dependency bumps and removals (prefix a removal with `Removed:`). Flag a breaking one with `⚠️`.
- **Fixed** — bugs fixed in shipped scaffolding.
- **Migration** — a one-line pointer to `migrations/template-vX.Y.Z.md`, or `None — drop-in.` Required whenever a fork must do anything to upgrade. **Steps, diffs, and rationale go in the guide — not here.**

### Keep it short

- **One line per bullet.** Name the change and why it matters to a developer — not how it works.
- No code walkthroughs, file-by-file lists, "why this matters" essays, or `> Note:` asides — that detail belongs in the migration guide.
- Backtick file paths and symbols. Drop filler ("now", "the ability to"). Fold tightly-coupled changes into one bullet.

---

## Migration guide rules

Every release that needs fork action ships a guide at `migrations/template-vX.Y.Z.md`. Where the changelog says *what* changed in one line, the guide is the **technical "how"** — precise enough that an agent (or developer) can apply the upgrade to a fork without guessing. Detail that's banned from the changelog belongs here; depth is the point.

### Structure

```
# Migrating a fork to template vX.Y.Z

<one-line summary> + who it affects (every fork / tenancy-only / Docker / …).

> ⚠️ <breaking changes or required prep, if any>

## Prerequisites      — previous version (jq -r .version template-manifest.json) + green baseline
## Part A — <change>  — one Part per logical change; exact diffs, file paths, commands
## Part B — <change>  — labeled by who it affects, so a fork can skip what doesn't apply
## Verify             — copy-paste commands that prove it worked
## Finally            — bump template-manifest.json to the new version
```

### Rules

- **Show the exact change** — `diff` blocks with real file paths, full commands, before/after. No "update the config" hand-waving.
- **One Part per logical change**, labeled by audience (every fork, tenancy-only, Docker, …) so readers skip what doesn't apply.
- **Account for fork variations** — "if your fork extended X / wired its own Y, also do Z." A real fork has diverged; say where.
- **`Verify` must be runnable** — the actual commands a reader pastes to confirm success (`pnpm check:php`, a `grep`, a `curl`), not prose.
- **Always end by bumping `template-manifest.json`** to the new version — the record that the fork is now aligned.
- **Add a drift/sync check** when the release touches managed core — confirm the fork's template-managed + Docker-core files match the template, excluding its knobs (see v5.1.3's `Part D` for the pattern).

---

## Versioning

- **Patch** (`x.y.Z`) — bug fixes, dependency/security refreshes, drop-in config corrections.
- **Minor** (`x.Y.0`) — new backward-compatible capabilities (a command, component, config block, opt-in feature).
- **Major** (`X.0.0`) — breaking library majors, schema changes forks must run, or removals that break forks. Deliberate sign-off only.
- **Docker-core changes** carry a `Docker:` tag on the lead line and follow `AGENTS.md` → "Optional Docker / Deployment" for the bump.

A release that touches the managed core also updates `template-manifest.json` (`version`) and the workspace `VERSIONS.md` tracker.
