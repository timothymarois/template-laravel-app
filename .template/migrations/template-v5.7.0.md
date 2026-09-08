# Migrating a fork to template v5.7.0

v5.7.0 vendors the **agent skills** this stack needs into `.claude/skills/`, committed with the code.
Every fork. Minor — files and one ignore rule; no schema, dependency, or Docker-core change.

**Why:** a skill only loads from the working directory an agent starts in, or from the machine-wide
`~/.claude/skills`. A fork that relies on the machine-wide copy gets a different set on every machine and
none at all in CI or on a fresh clone. Since v5.6.0 the documentation standards have lived in a skill that `.template/ADOPT.md` names and no
fork actually carried — the pointer resolved to nothing. Committing the set makes a clone
self-sufficient. That skill ships here as `maintaining-project-docs`; it was called
`structuring-project-docs` upstream, and the rename is part of this release.

> **Nothing runtime changes.** No dependency, no route, no schema, no build step. A fork that never opens
> an agent is unaffected by this release apart from the ignore rule in Part C.

## Prerequisites

- `jq -r .version template-manifest.json` → `5.6.0`. On `5.5.0` or below, apply the intervening
  migrations first.
- Baseline `pnpm check` is green.
- A clone of this template at v5.7.0 to copy from. Referred to below as `<t>`.

---

## Part A — Vendor the skills (every fork)

Copy the directory wholesale. These are the thirteen matched to this stack: `using-laravel`, `using-inertia`, `using-vuejs`, `using-tailwindcss`, `using-mysql`, `using-postgres`, `using-sqlite`, `designing-apis`, `designing-databases`, `designing-ui-ux`, `testing-code`, `debugging-code`, `maintaining-project-docs`. Both relational engines ship deliberately; Part D says which to drop.

```bash
mkdir -p .claude
cp -R <t>/.claude/skills .claude/skills
```

If your fork already has a `.claude/skills/` of its own, do not overwrite it — merge, and keep any skill
you added deliberately. Nothing else under `.claude/` is touched.

> **These copies are deliberately diverged from the upstream catalogs.** `debugging-code` has had its
> React, Python, and C++ references removed, and the Python and JSX examples in `testing-code` and
> `using-tailwindcss` are restated in Pest and Vue. Re-copying a skill from its catalog reintroduces all
> of it. Take upstream changes by hand, or accept the wider copy knowingly.

## Part B — Add the neutral path (every fork)

Providers that read `.agents/skills` rather than `.claude/skills` get the same set through a
repository-relative link, so there is no second copy to keep in sync:

```bash
mkdir -p .agents
ln -s ../.claude/skills .agents/skills
```

On a filesystem that does not materialize symlinks, `.agents/skills` arrives as a plain text file. That is
harmless — `.claude/skills` is the path Claude Code discovers — but if your fork needs a real directory
there, copy it instead and keep the two in step yourself.

## Part C — Keep local permissions out of git (every fork)

`.claude/` is now a tracked directory, so a per-machine `settings.local.json` written into it would be
committed and shipped to whoever clones the fork:

```diff
+
+# Claude Code — vendored agent skills (.claude/skills) are committed;
+# per-machine permission rules are not.
+/.claude/settings.local.json
```

If your fork has already committed one, remove it from the index in this same change:

```bash
git rm --cached .claude/settings.local.json
```

## Part D — Trim the set to your fork (fork-specific)

The thirteen are the template's stack, not yours — every one of them from the `rundesk-team-development` catalog, which is the line this set holds to. Adjust deliberately — each skill's `description` is loaded
on every agent request, so an unused one is a running cost.

| Your fork | Do |
|---|---|
| Runs on MySQL only (`DB_CONNECTION=mysql`, no `pgsql*` connection in use) | Remove `using-postgres` |
| Runs on Postgres (including a `pgsql_central` tenancy connection) | Remove `using-mysql` |
| Does not run tests on SQLite (check `phpunit.xml` for `DB_CONNECTION`) | Remove `using-sqlite` |
| Dropped Inertia for a conventional API | Remove `using-inertia` |
| Serves no API anybody else calls (`routes/api.php` is a stub) | Remove `designing-apis` |
| Actually wired up Cashier rather than only requiring it | Add `laravel-stripe-payments` |
| Publishes public marketing pages that must rank | Consider `seo` |

Keep both engines only if the fork genuinely reaches both — a tenancy setup with a Postgres central
connection and MySQL tenant databases does.

Skills that are not specific to this stack — `reviewing-code`, `managing-development-work`,
`writing-plans`, `managing-github`, `naming-grammar-conventions` — belong in the machine-wide
`~/.claude/skills`, not vendored into every repository, where each one has to be updated separately.

## Verify

```bash
# 1. Thirteen skills, each with a SKILL.md
ls .claude/skills | wc -l
for d in .claude/skills/*/; do test -f "$d/SKILL.md" || echo "MISSING $d"; done

# 2. The neutral path resolves to the same set
ls .agents/skills | wc -l

# 3. Local permissions are ignored, skills are not
git check-ignore -v .claude/settings.local.json
git status --porcelain --untracked-files=all | grep -c '\.claude/skills/'

# 4. No reference survives to a stack this fork does not use
grep -rhoE '^```[A-Za-z0-9+#_-]+' .claude/skills | sort -u

# 5. Nothing links outside a skill except the known validation record
grep -rn '\.\./\.\./\.\.' .claude/skills

# 6. Baseline still green
pnpm check
```

Step 1 prints `13`, step 2 prints `13`, step 3 names the ignore rule and a non-zero count of tracked skill
files, and step 4 prints only fences this stack uses — `bash`, `blade`, `css`, `dotenv`, `html`, `http`,
`ini`, `js`, `json`, `md`, `php`, `pseudocode`, `sh`, `sql`, `text`, `ts`, `typescript`, `vue`. A
`python`, `cpp`, or `jsx` in that list means an untrimmed copy.

Then open an agent in the repository root and confirm the thirteen appear in its available skills. A skill that
does not appear is almost always a `SKILL.md` that is missing, malformed, or over the size limit — not a
path problem.

## Finally

```diff
 {
     "template": "template-laravel-app",
-    "version": "5.6.0",
-    "updated": "2026-08-26",
+    "version": "5.7.0",
+    "updated": "2026-09-08",
```
