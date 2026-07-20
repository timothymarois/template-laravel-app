# Adopting & upgrading this template

Two prompts for coding agents — one to turn a fresh clone into your own product, one to pull template
upgrades into an existing fork. Hand your agent the relevant block; each tells it what to do and where the
rules live.

> **This file is template machinery.** The new-project flow deletes `.template/` (including this file) once
> you've adopted — a shipped product shouldn't carry "how to adopt the template." An existing fork upgrades
> by reading the template's `.template/` (this file + `migrations/`) from a fresh clone of the template, not
> from itself.

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

3. MAKE THE KNOWLEDGE OURS. .knowledge/ currently describes the starter. Rewrite it for OUR product:
   - Read .knowledge/README.md and the guides/docs-*.md standards, then rewrite BRIEF.md, CODEMAP.md, and
     OVERVIEW.md to describe what WE are building — researching this codebase, not guessing. Leave no
     <project> placeholder. MEMORY.md starts empty.
   - Declare OUR components (the ontology) in .knowledge/prd/README.md — this is MY call, so PROPOSE a set
     and get my sign-off before writing it in.
   - Keep the writing standards (guides/docs-*.md) and the linter (scripts/) EXACTLY as shipped — they are
     versioned by knowledge-template and .payload-manifest checksums them; editing them fails the build.

4. KEEP OR CUT THE OPTIONAL STACKS, based on what we actually use:
   - If we are NOT multi-tenant: I'll confirm, then remove the tenancy guides and the tenancy sections from
     AGENTS.md/CODEMAP (a persisted-state + deletion change — get my approval first).
   - If we do NOT deploy via Docker/Coolify: same, for the docker/ setup and its AGENTS.md section.

5. VERIFY. `pnpm check` is green (it includes check:docs → the knowledge linter). No <project> placeholder
   remains. AGENTS.md, README, and .knowledge/ describe OUR product, with no stray "template" references
   except template-manifest.json.

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
run destructive git commands. Commit nothing until I approve.

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
   says, and run that section's checks (`pnpm check` — includes check:docs — plus anything the guide names).
   Don't move to the next release until the current one is green.

5. REPORT. For each release: what you applied, what you adapted for this fork and why, any file where our
   customization met the template's change, and every point you stopped to ask.

Target version: <e.g. latest, or a specific vX.Y.Z>. If I didn't name one, use the template's head from
.template/CHANGELOG.md and confirm it with me before starting.
```
