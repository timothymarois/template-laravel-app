# Migrating a fork to template v5.6.0

v5.6.0 replaces the `.knowledge/` documentation payload with a plain **`docs/`** tree, and adds a
**production release process** plus a `/release` endpoint. Every fork. Minor — docs, tooling, and one
additive route; no schema, dependency, or Docker-core change.

**Why:** the writing standards now live in a skill (`structuring-project-docs`) rather than in a versioned
payload every repo carries a copy of. That removes the second version line, the checksum manifest, the
linter, and the `.knowledge/.version` stamp — a fork tracks one version, this template's. Separately, no
fork could mark a version before this: `production` was deployed with nothing recording what was on it,
and nothing outside the container could answer "which version is live?"

> ⚠️ **Two required actions.** Convert your docs home, and fill in the new `deploy` block in
> `template-manifest.json`. The release scripts refuse to run against an unedited placeholder.

> **Local dev and runtime are unchanged**, apart from one new public route. `pnpm check` swaps `check:docs`
> for `check:release`.

## Prerequisites

- `jq -r .version template-manifest.json` → `5.5.0`. On `5.4.0` or below, apply the intervening migrations
  first; they are cheap and docs-only.
- Baseline `pnpm check` is green.
- `python3 --version` works — the release scripts are stdlib-only, nothing to install.
- A clone of this template at v5.6.0 to copy `scripts/`, `config/release.php`, the controller, and the test
  suites from. Referred to below as `<t>`.

---

## Part A — Stand up `docs/` (every fork)

Create the tree and move your real content into it. **Do not create a home you have no page for** — an
empty directory holding a placeholder teaches the next reader that the layout is decoration.

```text
docs/
  README.md      the index — lists the homes
  BRIEF.md       what this is, who it serves, what it refuses
  CODEMAP.md     where each layer lives, with counts
  concepts/      how a subsystem works, and how it fails
  guides/        one task each, start to finish
```

Move, do not rewrite:

| From | To |
|---|---|
| `.knowledge/BRIEF.md` | `docs/BRIEF.md` — headings become Story / Why it exists / Users / Scope (Covers, Refuses) / External systems |
| `.knowledge/CODEMAP.md` | `docs/CODEMAP.md` |
| `.knowledge/OVERVIEW.md` | `docs/concepts/platform.md`, reshaped: a concept page also says **how it fails** |
| `.knowledge/guides/<your how-tos>` | `docs/guides/` (a task) or `docs/concepts/` (a subsystem) |
| `.knowledge/guides/stack-examples.md` | Folded into `AGENTS.md` as a closing appendix |

**Carry the owner-knowledge sections of `BRIEF.md` forward verbatim.** Scope and external systems are in
the code; who it is for and what it refuses are not. Never re-derive an audience — a plausible invented one
reads exactly as confidently as a sourced one and nobody re-checks it.

**`MEMORY.md` has no home in this layout.** Take each entry in turn: a fact about how a subsystem behaves
belongs in that subsystem's page under *how it fails*; a rule about working here belongs in `AGENTS.md`; a
trap that has since been fixed gets deleted. Check the code before carrying an entry — an entry describing
a bug that was fixed is worse than no entry. If nothing survives, write no page.

Every home gets a `README.md` listing its pages, one row each, and `docs/README.md` lists the homes. **Add
the index row in the same change as the page.** A page belongs to exactly one index.

> **Mold this to your fork — and let the code decide.** Verify every path in `CODEMAP.md` against the real
> tree, in both directions: do not carry a description of something you removed, and do not drop something
> you still ship. Count artifacts, not lines. Tenancy is the usual trap — the template ships it inert, so
> most forks still have the code and their docs should keep it, marked inert.

## Part B — Grep for doc paths your code cites (every fork)

**Do this before deleting anything.** Source comments, command output, and tests cite documentation paths,
and a moved page silently breaks them.

```sh
grep -rn "\.knowledge/\|docs/guidelines/" app config routes tests database bootstrap resources .env.example README.md docker/ 2>/dev/null
```

Repoint every hit in the same commit as the move. Two kinds need care:

- **A test that asserts on a path string** passes whether or not the file exists, so it will not warn you.
  This template shipped two such assertions pointing at a directory deleted two releases earlier.
- **A link label that repeats the path** — the label and the target are the same string — needs both halves
  changed. Fixing
  only the target leaves the label lying while every link checker passes.

Where you can choose the new filename, **choose the one the code already cites**. It turns a repoint into
a no-op.

## Part C — Take the release process (every fork)

```sh
cp -R <t>/scripts scripts
cp <t>/config/release.php config/release.php
cp <t>/app/Http/Controllers/ReleaseController.php app/Http/Controllers/ReleaseController.php
cp -R <t>/tests/scripts tests/scripts
cp <t>/tests/Feature/ReleaseVersionTest.php tests/Feature/ReleaseVersionTest.php
chmod +x scripts/* tests/scripts/*.sh tests/scripts/stubs/*
```

Register the route in `routes/web.php`, beside `/health`:

```diff
 use App\Http\Controllers\PageController;
+use App\Http\Controllers\ReleaseController;
@@
 Route::get('health', HealthCheckJsonResultsController::class)->name('health');
+
+Route::get('release', ReleaseController::class)->name('release.version');
```

Add the `deploy` block to `template-manifest.json` and **fill in your own values**:

```diff
     "version": "5.6.0",
+    "deploy": {
+        "repository": "<owner>/<repo>",
+        "productionUrl": "https://<your-production-host>",
+        "productionBranch": "production"
+    },
     "docker": {
```

Both manifests must read `version: "0.0.0"` on `main`. If yours carry a real version, set them to `0.0.0`
now — `scripts/prepare-production-release` refuses to start otherwise, and it pins the release branch to
`origin/main`, so a versioned `main` cannot be corrected from the release branch later.

```diff
-  "version": "1.4.2",
+  "version": "0.0.0",
```

If your fork has no `production` branch, create one from `main` and point your deployment at it. If it
deploys from `main` instead, set `productionBranch` accordingly — but then the release tag and the deployed
commit are the same thing and the reconciliation step in `docs/guides/releasing.md` does not apply.

## Part D — Swap the gate (every fork)

```diff
-    "check:docs": "python3 .knowledge/scripts/test_doc_lint.py && python3 .knowledge/scripts/doc-lint .knowledge",
+    "check:release": "bash tests/scripts/prepare-production-release.sh && bash tests/scripts/production-release-version.sh && bash tests/scripts/publish-production-release.sh && bash tests/scripts/assert-neutral-main-version.sh",
```

```diff
-  "check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:docs\" && pnpm check:build"
+  "check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:release\" && pnpm check:build"
```

Delete `.github/workflows/doc-lint.yml` and its README badge. In your JS workflow, add the release suites
and the neutral-version guard, and add `production` to both trigger lists in every workflow — a release
pull request targets `production`, and without it that PR arrives with no checks at all:

```yaml
      - name: Test the production release scripts
        run: pnpm check:release

      - name: Require neutral package versions on main
        if: github.base_ref == 'main' || github.ref_name == 'main'
        run: scripts/assert-neutral-main-version
```

**Nothing replaces the doc linter.** Say so in `AGENTS.md` rather than leaving the next agent to notice the
gate stopped running.

## Part E — Rewire `AGENTS.md` (every fork)

Repoint every `.knowledge/` path at `docs/`, drop the `prd/` ratification workflow and the `doc-lint`
instruction, and route friction to the page that owns the subsystem. Add the two rules the layout depends
on and the linter used to enforce: an index row ships with its page, and a page is the source of truth for
its subsystem — cite a fact, never restate it.

If your fork keeps a `stack-examples.md`, fold it in as a closing appendix so the rules stay scannable.

## Part F — Delete the old home (every fork, last)

Only once nothing reads it:

```sh
git rm -r .knowledge
```

`git rm` removes tracked files only. `.knowledge/tmp/` was git-ignored, so anything inside it survives on
disk and `git status` will never mention it:

```sh
find .knowledge -type d 2>/dev/null      # expect no output
ls -a .knowledge 2>/dev/null             # expect "No such file or directory"
```

If a directory remains, look at what is in it before removing it — an ignored file was never committed, so
deleting it is irreversible.

## Verify

```sh
# 1. No reference to the retired system survives.
grep -rn "\.knowledge\|doc-lint\|check:docs" --exclude-dir=vendor --exclude-dir=node_modules \
  --exclude-dir=.git --exclude-dir=.template .        # expect no output

# 2. No dangling doc path remains, and each replacement resolves.
grep -rn "docs/guidelines/" --exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=.git \
  --exclude-dir=.template .                            # expect no output

# 3. The old home is gone from the index and from disk.
git ls-files .knowledge | wc -l                        # expect 0
find .knowledge -type f 2>/dev/null | wc -l            # expect 0

# 4. Every page is in exactly one index, and every index row resolves.
#    Read docs/README.md and each home's README.md against the tree:
find docs -name '*.md' | sort

# 5. The release scripts refuse an unedited manifest, and accept yours.
scripts/production-release-version                      # expect: 0.0.0 is not a production release
scripts/assert-neutral-main-version                     # expect: neutral at 0.0.0

# 6. The endpoint answers, uncached.
php artisan route:list --name=release                   # expect release.version -> ReleaseController
php -d memory_limit=512M ./vendor/bin/pest tests/Feature/ReleaseVersionTest.php

# 7. The whole gate, including the release suites.
pnpm check
pnpm check:tenancy                                      # if your fork keeps tenancy
```

Read `docs/BRIEF.md` last, as somebody who has never seen the repository. If a section reads as invented
rather than sourced, mark it and ask the owner — do not leave it level with the rest.

## Finally

Bump the manifest:

```diff
-    "version": "5.5.0",
+    "version": "5.6.0",
```

Then update the workspace tracker row for this fork. If your fork was listed as running a `.knowledge/`
version, that record no longer applies — the documentation system is no longer separately versioned.
