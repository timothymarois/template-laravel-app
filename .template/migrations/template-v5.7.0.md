# Migrating a fork to template v5.7.0

v5.7.0 vendors the **agent skills** this stack needs into `.claude/skills/`, adds a fork-owned nginx
extension point at **`docker/project/`**, and puts the production image's own configuration under a
**`check:deploy`** gate. Parts A-D: every fork. Parts E-F: forks that run the Docker setup. Minor — no
schema or dependency change, but `Dockerfile` and `docker/config/nginx.conf` are managed core and move.

**Why:** a skill only loads from the working directory an agent starts in, or from the machine-wide
`~/.claude/skills`. A fork that relies on the machine-wide copy gets a different set on every machine and
none at all in CI or on a fresh clone. Since v5.6.0 the documentation standards have lived in a skill that `.template/ADOPT.md` names and no
fork actually carried — the pointer resolved to nothing. Committing the set makes a clone
self-sufficient. That skill ships here as `maintaining-project-docs`; it was called
`structuring-project-docs` upstream, and the rename is part of this release.

> **Nothing runtime changes.** No dependency, no route, no schema. The image gains two `COPY` lines and a
> wildcard `include` that matches nothing until you add a file, so it builds exactly as before.

> ⚠️ **If your fork already edits `docker/config/nginx.conf` or the `Dockerfile`** — for upload capacity or
> anything else — read Part E before merging. It is very likely the thing your edit was working around, and
> the exception can retire.

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

## Part E — Take the nginx extension point (Docker forks)

Before this release a fork that needed more upload capacity had one move: raise `client_max_body_size` on
the server block. That is a trap. It applies to **every** route, and nginx buffers a request body to temp
disk **before PHP is reached** — so the global hands every endpoint, unauthenticated ones included, that
much temp disk and inbound bandwidth per concurrent request. `docker/project/` is where the capacity goes
instead, and the template never writes into it, so it never conflicts on an upgrade.

Copy the directory and its README from `<t>`:

```bash
cp -R <t>/docker/project docker/project
```

Then apply the two managed-core changes. In `Dockerfile`, after the `entrypoint` `chmod`:

```diff
 COPY docker/deploy/entrypoint.sh    /usr/local/bin/entrypoint
 RUN chmod +x /usr/local/bin/entrypoint
+
+COPY docker/project/nginx/http/   /etc/nginx/conf.d/
+COPY docker/project/nginx/server/ /etc/nginx/snippets/
```

Copy the directories whole, not by `*.conf` glob — a glob fails the build when a fork has added nothing.

In `docker/config/nginx.conf`, immediately **before** `location ~ \.php$`:

```diff
+    include /etc/nginx/snippets/*.conf;
+
     location ~ \.php$ {
```

Position is the whole point: nginx matches regex locations in file order, so a fork's location must be
seen before the PHP one or that block wins the URI and replaces every directive the fork set — before the
request body is read.

**If your fork carries its own upload exception**, this is where it retires. Move the values into
`docker/project/nginx/`, restore the managed-core globals to the template's, and delete the exception note
from your `docker/README.md`. `aprillaneart-site` is the worked example.

## Part F — Take the deploy gate (Docker forks)

The application suite runs under its own ini and never executes the deploy scripts, so nothing has been
checking the configuration the container actually ships. Copy the three contract tests and wire them in:

```bash
cp <t>/tests/scripts/php-ini.sh         tests/scripts/php-ini.sh
cp <t>/tests/scripts/nginx-config.sh    tests/scripts/nginx-config.sh
cp <t>/tests/scripts/post-deployment.sh tests/scripts/post-deployment.sh
chmod +x tests/scripts/php-ini.sh tests/scripts/nginx-config.sh tests/scripts/post-deployment.sh
```

```diff
 "check:release": "bash tests/scripts/prepare-production-release.sh && …",
+"check:deploy": "bash tests/scripts/php-ini.sh && bash tests/scripts/nginx-config.sh && bash tests/scripts/post-deployment.sh",
-"check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:release\" && pnpm check:build",
+"check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:release\" \"pnpm check:deploy\" && pnpm check:build",
```

These assert **agreement between files**, not runtime behavior: that the `Dockerfile` still installs
`php.ini` where PHP reads it last (the `zz-` prefix — conf.d loads alphabetically, so a rename leaves the
file loaded and then silently overridden), that the snippets include still precedes the PHP location, that
the nginx and PHP transport ceilings can be reached, and that `post-deployment.sh` still runs
`migrate --force` exactly once.

A fork whose values differ from the template's passes as long as they are internally consistent —
`client_max_body_size` >= `post_max_size` >= `upload_max_filesize`. If your fork legitimately changed a
setting these pin, adjust the assertion rather than dropping the test.

## Part G — Reconcile `CLAUDE.md` with `AGENTS.md` (every fork)

Your `AGENTS.md` is your own — this part changes three things in it and then makes `CLAUDE.md` a copy.

**1. Give "Before you work" a skills step and a scope rule.** After Part A your fork has skills it never had, and nothing
tells an agent to load them. Replace the stale workflow item — if your fork still carries the template's,
it routes through `research/ -> prd-drafts/ -> prd/`, which has not been the documentation home since
v5.6.0, and it breaks mid-sentence on the word "Run" — with a step that says: review the skills available
to you, load **every** one the task touches rather than the most obvious one, compose them, and reassess
when the task moves into a new area. Copy the wording from `<t>/AGENTS.md`.

Do not name `.claude/skills/` in the step. The runtime discovers that directory on its own and already
carries every skill's name and description; a path in the instructions is redundant for one runtime and
wrong for another.

Then add the scope rule as a final step: make the smallest change that accomplishes the goal, touch
nothing adjacent to it, do not refactor or rename what the task did not send you to, mention improvements
rather than making them, and never widen scope unless directed. An agent that tidies on the way produces
a diff nobody can review and nobody can revert cleanly.

**2. Extend the file's own approval rule to both filenames**, so the copy below cannot be edited alone:

```
- **This file — ask first.** `CLAUDE.md` is a byte-identical copy; change both in the same commit.
  `pnpm check` fails when they drift, so a change to one is unfinished until the other matches.
```

The template also folded its "Hard gates" and "Never" sections into one **"Hard rules"** list, each bullet
carrying its own force word ("ask first" / "never") rather than inheriting it from a heading. That is
optional for a fork — but two headings for one idea invites a rule landing under the wrong one.

**3. Make `CLAUDE.md` the copy, and guard it:**

```bash
cp AGENTS.md CLAUDE.md
cp <t>/tests/Feature/AgentInstructionsTest.php tests/Feature/AgentInstructionsTest.php
```

The guard is a Pest test, so it runs inside `check:php` and in CI's existing PHP workflow — no new gate,
no new job. `rocketquote-os` has run the byte-identical convention by hand for months; this is that
convention with something enforcing it.

> **If your fork deliberately keeps `CLAUDE.md` as a pointer**, skip this part and do not take the test.
> But know what you are choosing: the pointer only works if the runtime reading it follows the link every
> time, and a rule that reaches one runtime and not the other fails silently.

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

# 6. The two agent instruction files are one document
cmp AGENTS.md CLAUDE.md && echo "identical"

# 7. The deploy gate, and the baseline
pnpm check:deploy
pnpm check
```

Step 1 prints `13`, step 2 prints `13`, step 3 names the ignore rule and a non-zero count of tracked skill
files, and step 4 prints only fences this stack uses — `bash`, `blade`, `css`, `dotenv`, `html`, `http`,
`ini`, `js`, `json`, `md`, `php`, `pseudocode`, `sh`, `sql`, `text`, `ts`, `typescript`, `vue`. A
`python`, `cpp`, or `jsx` in that list means an untrimmed copy.

Then open an agent in the repository root and confirm the thirteen appear in its available skills. A skill that
does not appear is almost always a `SKILL.md` that is missing, malformed, or over the size limit — not a
path problem.

**What Part E is not proven against.** This repository does not run nginx and has no container build in CI,
so `check:deploy` asserts agreement between files and nothing more. That a wildcard `include` matching zero
files loads cleanly, and that the server block still parses, are checked on a built image:

```bash
docker build -t template-check .
docker run --rm --entrypoint nginx template-check -t
```

Run that once before trusting a fragment you add to `docker/project/` on a real deploy, and read the
effective configuration with `nginx -T` if a location does not behave as written.

## Finally

```diff
 {
     "template": "template-laravel-app",
-    "version": "5.6.0",
-    "updated": "2026-08-26",
+    "version": "5.7.0",
+    "updated": "2026-09-08",
```
