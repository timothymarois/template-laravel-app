# Adopting & upgrading this template

Two prompts for coding agents — one to turn a fresh clone into your own product, one to pull template
upgrades into an existing fork. Hand your agent the relevant block; each tells it what to do and where the
rules live.

> **This file is template machinery.** The new-project flow deletes `.template/` (including this file) once
> you've adopted — a shipped product shouldn't carry "how to adopt the template." An existing fork upgrades
> by reading the template's `.template/` (this file + `migrations/`) from a fresh clone of the template, not
> from itself.

> **One version line.** As of v5.6.0 the documentation standards live in a skill
> (`structuring-project-docs`), not in a versioned payload each repo carries. There is no
> `.knowledge/.version` to keep current any more — a fork tracks exactly one version, this template's, in
> `template-manifest.json`.

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

3. MAKE THE DOCS OURS. docs/ currently describes the starter. Rewrite it for OUR product:
   - docs/BRIEF.md and docs/CODEMAP.md describe the TEMPLATE. Rewrite both for us, researching THIS
     codebase rather than guessing. CODEMAP maps the real tree with counts of artifacts, not lines; BRIEF
     says what we are, who we serve, and what we refuse.
   - Scope and external systems are in the code. WHO IT IS FOR and WHAT IT REFUSES are not — ask me, do not
     infer them. An invented audience reads exactly as confidently as a sourced one and nobody re-checks it.
   - docs/concepts/ and docs/guides/ describe the shared stack (health checks, logging, Ziggy, releasing,
     tenancy, testing). They stay true in a fork — keep them. If we will not use a stack, you MAY delete its
     guide and its index row, but see step 4 before touching any code.
   - Keep every home's README.md level with its directory: one row per page, and a row lands in the same
     change as the page. A page belongs to exactly one index. Never create a home with no page in it.

4. SET UP THE RELEASE PROCESS. Fill in template-manifest.json -> deploy with OUR values: repository
   (owner/name), productionUrl (the deployed origin), productionBranch. The release scripts refuse to run
   against the shipped placeholders, so this is required before our first release, not optional. Confirm
   both composer.json and package.json read version "0.0.0" — main never carries a release version. Full
   procedure: docs/guides/releasing.md.

5. MATCH THE DOCS TO WHAT WE SHIP — don't rip out code during adoption. The template documents optional
   stacks (multi-tenancy installed-but-inert; Docker/Coolify):
   - The docs must describe the code THAT EXISTS. While the tenancy code is still present (even inert),
     CODEMAP/AGENTS keep it — marked inert/optional, not deleted. Do NOT remove a feature from the docs
     while its code still ships; that makes the docs lie about the repo.
   - If we won't use a stack, you MAY delete its how-to GUIDES now (e.g. the tenancy guides + their
     guides/README.md rows) and trim BRIEF/OVERVIEW to our product — those are docs-only.
   - Actually removing a stack's CODE, config, migrations, and dependency (e.g. stancl/tenancy) is a
     SEPARATE, hard-gated change (schema + dependency + deletion). Do NOT do it during adoption — raise it
     and I'll run it as its own task. Only once the code is gone do CODEMAP/AGENTS drop the feature.

6. VERIFY. No "template-laravel-app" reference remains in docs/, AGENTS.md, or README except in
   template-manifest.json. Every relative link in docs/ resolves and every page appears in exactly one
   index. `scripts/assert-neutral-main-version` passes. Run `pnpm check` if the toolchain is set up — it
   includes check:release, which proves the release scripts still work after we edited the manifest.

Then stop and show me: what you renamed, what you removed, the component ontology you propose, and anything
you had to infer.
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
   - Persisted-state, schema, dependency, or deletion changes are HARD GATES — get my approval before each.

4. STAMP + VERIFY per release: update template-manifest.json "version" as each migration's Verify section
   says, and run that section's checks (`pnpm check` — includes check:release — plus anything the guide names).
   Don't move to the next release until the current one is green.

5. REPORT. For each release: what you applied, what you adapted for this fork and why, any file where our
   customization met the template's change, and every point you stopped to ask.

Target version: <e.g. latest, or a specific vX.Y.Z>. If I didn't name one, use the template's head from
.template/CHANGELOG.md and confirm it with me before starting.
```
