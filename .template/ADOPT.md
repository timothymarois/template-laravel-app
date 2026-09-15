# Adopting & upgrading this template

Two prompts for coding agents — one to turn a fresh clone into your own product, one to pull template
upgrades into an existing fork. Hand your agent the relevant block; each tells it what to do and where the
rules live.

> **This file is template machinery.** The new-project flow deletes `.template/` (including this file) once
> you've adopted — a shipped product shouldn't carry "how to adopt the template." An existing fork upgrades
> by reading the template's `.template/` (this file + `migrations/`) from a fresh clone of the template, not
> from itself.

> **One version line.** The documentation is a [wiki-builder](https://github.com/timothymarois/wiki-builder)
> wiki under `docs/wiki/`, written to the `writing-wiki-pages` skill that `wiki sync` puts in
> `.claude/skills/`, and pinned to one wiki-builder release in `scripts/dev-wiki.sh`. A fork tracks
> exactly one template version, in `template-manifest.json`; the wiki-builder release is a line in the
> wrapper script, bumped by a template release when the tool moves.

---

## 🌱 Start a new project from this template

You cloned this starter and want to make it *your product* — not a copy of the template. Paste this to your agent:

```
This repo was cloned from template-laravel-app (a Laravel + Inertia/Vue starter). Make it OUR project, not a
copy of the template. Read AGENTS.md first and follow it as law. Work in order, and ASK me anything you must
infer rather than guessing.

1. IDENTITY. Rename the project to <MY PROJECT NAME>:
   - README.md — replace the template's title, description, and CI badges with ours (point the badges at OUR
     repo's Actions, or remove them until CI exists). Keep the sections that document real features we keep;
     delete template-marketing prose that isn't about our product.
   - composer.json / package.json "name", app config name, .env.example APP_NAME.

2. STRIP THE TEMPLATE MACHINERY. This is a fork's starting point, not a template itself:
   - Delete .template/ (the template's own changelog, migration guides, and adoption prompts — not ours).
   - template-manifest.json STAYS: it records which template version we forked from, so we can pull upgrades.
     Leave its "version" as-is; do not bump it.

3. MAKE THE DOCS OURS. The documentation is a wiki-builder wiki under docs/wiki/, and every page of it
   describes THE TEMPLATE. Load the writing-wiki-pages skill (in .claude/skills/) and follow it; run the
   tool through ./scripts/dev-wiki.sh (needs uv). Rewrite the wiki for OUR product:
   - docs/wiki/wiki.toml: site.name becomes OUR product name.
   - docs/wiki/pages/brief.md and index.md describe the template. Rewrite both for us from THIS codebase.
     Scope and external systems are in the code. WHO IT IS FOR and WHAT IT REFUSES are not — ask me, do
     not infer them. An invented audience reads exactly as confidently as a sourced one and nobody
     re-checks it. A statement only I can make that I have not made is marked {missing}, not guessed.
   - Every other page describes the shared stack (accounts, the admin area, API keys, health checks,
     releases, deployment, logging, SEO, the component kit, the commands). Read each one against OUR
     code, sentence by sentence, and correct what differs: a route we renamed, a check we removed, a
     default we changed, a screen we do not ship. A page for something our code does not have is deleted,
     with its wiki.toml entry. A page whose intent no longer says what that part is for in OUR product
     gets a new intent, which I approve — list every intent you changed.
   - The goal pages (those without goals = false) say what the TEMPLATE is for. Which pages define OUR
     product is my decision: propose the set, and set goals = false on the rest.
   - docs/CODEMAP.md is the one document written for builders. Bring it in line with OUR tree: count
     artifacts, not lines, and drop what we removed.
   - Run ./scripts/dev-wiki.sh build, then ./scripts/dev-wiki.sh check, until it reports 0 problems, and
     commit docs/wiki/UPDATED.toml with the pages. Cite every sentence to OUR code; a page that says
     goals = false is not citation-checked, so cite it by hand anyway.

4. SET UP THE RELEASE PROCESS. Fill in template-manifest.json -> deploy with OUR values: repository
   (owner/name), productionUrl (the deployed origin), productionBranch. The release scripts refuse to run
   against the shipped placeholders, so this is required before our first release, not optional. Confirm
   both composer.json and package.json read version "0.0.0" — main never carries a release version. Full
   procedure: docs/wiki/pages/releases.md.

5. MATCH THE DOCS TO WHAT WE SHIP — don't rip out code during adoption. The template documents optional
   stacks (Docker/Coolify; the Cloudflare Worker documentation site; multi-tenancy as a guide a fork
   follows, docs/wiki/pages/setup/tenancy.md):
   - The wiki must describe the code THAT EXISTS. Do NOT remove a page for a feature while its code
     still ships; that makes the wiki lie about the repo. A page for a stack we will never adopt (the
     tenancy guide, the documentation site) MAY go now — it is docs-only — together with its wiki.toml
     entry and any link to it.
   - Actually removing a stack's CODE, config, migrations, and dependency is a SEPARATE, hard-gated
     change (schema + dependency + deletion). Do NOT do it during adoption — raise it and I'll run it as
     its own task. Only once the code is gone do its wiki page and CODEMAP drop the feature.
   - wrangler.jsonc is the documentation site's Worker: set "name" to OUR Worker's name in the Cloudflare
     dashboard if we will publish the wiki there, or delete the file and docs/wiki/worker.js if we won't.

6. VERIFY. No "template-laravel-app" reference remains in docs/, AGENTS.md, wrangler.jsonc or README
   except in template-manifest.json. ./scripts/dev-wiki.sh check reports 0 problems.
   `scripts/assert-neutral-main-version` passes. Run `pnpm check` if the toolchain is set up — it
   includes check:release, which proves the release scripts still work after we edited the manifest, and
   check:wiki, which proves the wiki still describes the code.

Then stop and show me: what you renamed, what you removed, every wiki page you changed and every intent
you rewrote, the goal pages you propose, every {missing} mark, and anything you had to infer.
```

---

## ⬆️ Upgrade an existing fork to a newer template version

Your fork is behind the template and you want to pull upgrades in — safely, without breaking what you've
built. Paste this to your agent:

```
This is a fork of template-laravel-app. Bring it up to a newer template version by following the template's
migration guides — WITHOUT breaking what we've customized. Read AGENTS.md first and follow it as law. Do NOT
run history-rewriting or working-tree-discarding git commands (reset --hard, checkout -- <path>, clean,
rebase, force-push) — but a `git rm` of files a migration tells you to remove is fine (it's staged, not
committed). Commit nothing until I approve.

VERIFY AGAINST THE CODE, NOT ASSUMPTIONS — including mine. If anything I tell you about this fork (what it
uses, what was removed) contradicts what the code actually shows, TRUST THE CODE and flag the contradiction.
Never delete or rewrite a doc for a feature the code still ships.

1. FIND THE GAP. Read template-manifest.json for our current "version". The template's releases and their
   ordered upgrade steps live in the template's HIDDEN .template/ directory (default search skips it — use
   --hidden or read paths directly): .template/CHANGELOG.md (history) and .template/migrations/template-v<x>.md
   (steps). If our fork doesn't carry .template/, read it from a fresh clone of the template. List every
   release between our version and the target, oldest first.

2. READ THE SKIP NOTES. Some releases supersede others — a migration guide may say "if you're below vX and
   heading to vY, skip this release." Honor those: do not adopt something one release installs only for a
   later one to delete. Build the actual ordered path for OUR starting version.

3. APPLY EACH MIGRATION, MOLDED TO THIS FORK. Work one release at a time, in order. For each:
   - Do exactly what its template-v<x>.md says — but ADAPT it to what this fork actually is. A migration
     guide describes the template; our fork may have DROPPED tenancy, RENAMED features, REMOVED admin
     screens, or DIVERGED. Never carry in a description of a feature we don't have, and never overwrite our
     customization with the template's default. When the guide and our reality disagree, STOP AND ASK.
   - Managed vs. ours: template-owned "core" files upgrade to the new version; the documented "knobs" and
     everything WE built stay ours. If a migration touches a file we've customized, show me the conflict
     before resolving it.
   - The docs are a wiki-builder wiki under docs/wiki/. A migration that changes behaviour names the
     wiki page it changes; apply that change to OUR copy of the page, re-read the page against OUR code,
     and finish the release with ./scripts/dev-wiki.sh check at 0 problems. A page we deleted at adoption
     stays deleted.
   - Persisted-state, schema, dependency, or deletion changes are HARD GATES — get my approval before each.

4. STAMP + VERIFY per release: update template-manifest.json "version" as each migration's Verify section
   says, and run that section's checks (`pnpm check` — includes check:release — plus anything the guide names).
   Don't move to the next release until the current one is green.

5. REPORT. For each release: what you applied, what you adapted for this fork and why, any file where our
   customization met the template's change, and every point you stopped to ask.

Target version: <e.g. latest, or a specific vX.Y.Z>. If I didn't name one, use the template's head from
.template/CHANGELOG.md and confirm it with me before starting.
```
