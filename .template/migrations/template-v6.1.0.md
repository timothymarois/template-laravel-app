# Migrating a fork to template v6.1.0

v6.1.0 replaces the hand-written `docs/` tree with a **wiki-builder wiki** under `docs/wiki/`: every page
is written from the code, every sentence cites the file and function it came from, and `wiki check` runs
in `pnpm check` and in CI, so a page that stops being true fails the gate. Every fork. Minor — docs,
tooling and agent rules only; no application code, schema, dependency or Docker-core change.

**Why:** `docs/` had no check. A page went stale the commit after it was written and nothing said so;
the codemap itself called docs correctness "a review concern". wiki-builder refuses the ways
documentation stops being true — an uncited sentence, a heading that asks a question, a page edited
since its date was recorded, a page nobody can reach — and its skill teaches an agent to write pages a
person reads *instead of* the code.

> ⚠️ **Three required actions.** Install the wrapper and run `wiki sync` (Part A), convert your fork's
> own docs into wiki pages (Part C), and take the template's `AGENTS.md`/`CLAUDE.md` wholesale (Part D).
> **`pnpm check` now needs `uv` on every developer machine** — `scripts/dev-wiki.sh` runs wiki-builder
> through `uvx`.

> **Merge conflicts are expected.** This release deletes `docs/BRIEF.md`, `docs/README.md`,
> `docs/concepts/` and `docs/guides/`. If your fork edited any of those, `git merge` reports
> `deleted by them`. Part F says how to resolve each one without losing what you wrote.

## Prerequisites

- `jq -r .version template-manifest.json` → `6.0.2`. On `6.0.1` or below, apply the intervening
  migrations first.
- Baseline `pnpm check` is green.
- `uv` installed: `uv --version` works (<https://docs.astral.sh/uv/getting-started/installation/>).
  Python 3.11 or newer is fetched by `uv` as needed.
- A clone of this template at v6.1.0 to copy from. Referred to below as `<t>`.
- **List every file under `docs/` your fork added or changed** before touching anything:
  `git log --oneline --diff-filter=AM -- docs/ | head` and `git diff --stat <template-v6.0.2-sha> -- docs/`.
  Those are the pages Part C converts and Part F reconciles.

---

## Part A — Install wiki-builder (every fork)

### 1. The wrapper

```bash
cp <t>/scripts/dev-wiki.sh scripts/dev-wiki.sh
chmod +x scripts/dev-wiki.sh
```

It pins one release (`WIKI_VERSION=v0.5.0`) and passes `--root` so it works from any directory. An update
is a one-line change to that script, followed by `./scripts/dev-wiki.sh sync`.

### 2. The wiki folder

```bash
mkdir -p docs/wiki/pages
cp <t>/docs/wiki/wiki.toml docs/wiki/wiki.toml
cp <t>/docs/wiki/pages/index.md <t>/docs/wiki/pages/goals.md docs/wiki/pages/
```

Set `site.name` in `docs/wiki/wiki.toml` to **your product's name**, and delete the `[tool]` table at the
foot — `wiki sync` writes it. Leave the sections for now; Part C settles which pages you keep.

### 3. Ignore the generated site

```diff
+# wiki-builder — the rendered site, a published build and wrangler state
+docs/wiki/site/
+/_site/
+.wrangler/
```

### 4. Sync the skill and record the release

```bash
./scripts/dev-wiki.sh sync
```

It writes `writing-wiki-pages` into `.agents/skills/` (which is a link to `.claude/skills/` in every
fork on v5.7.0 or later, so the skill lands once) and records `version = "0.5.0"` in `wiki.toml`.
Commit the skill: agents read it from the repository, and a change to how pages must be written arrives
as a diff somebody reviews.

**If your fork removed `.agents/skills`**, `sync` writes into `.claude/skills/` instead. Either way, the
folder must be committed.

---

## Part B — The gate and CI (every fork)

### 1. `package.json`

```diff
         "check:deploy": "bash tests/scripts/preflight-php.sh && bash tests/scripts/php-ini.sh && bash tests/scripts/nginx-config.sh && bash tests/scripts/post-deployment.sh",
+        "check:wiki": "./scripts/dev-wiki.sh check",
         "check:build": "pnpm build && pnpm exec vite build --ssr",
-        "check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:release\" \"pnpm check:deploy\" && pnpm check:build"
+        "check": "concurrently -g \"pnpm check:php\" \"pnpm check:js\" \"pnpm check:release\" \"pnpm check:deploy\" \"pnpm check:wiki\" && pnpm check:build"
```

**If your fork renamed or added gates to `check`**, keep yours and add `"pnpm check:wiki"` to the
`concurrently` list.

### 2. The workflow

```bash
cp <t>/.github/workflows/wiki.yml .github/workflows/wiki.yml
```

It runs `timothymarois/wiki-builder@v0.5.0` — the same release the wrapper pins — on every pull request
and on a push to `main` only, so a pull-request branch is not checked twice. **If your default branch is
not `main`**, change the `push.branches` list.

The action installs the tool with `pip` from its own checkout, so CI does not need `uv`.

---

## Part C — Convert your docs into wiki pages (every fork)

**Read `.claude/skills/writing-wiki-pages/SKILL.md` and every file in its `references/` in full before
writing a page.** The rules below are the ones a first migration got wrong; the skill holds the rest.

### 1. Take the template's pages

```bash
cp -R <t>/docs/wiki/pages/. docs/wiki/pages/
cp <t>/docs/wiki/worker.js docs/wiki/worker.js     # only if you take Part G
```

Every template page describes the shared stack (accounts, the admin area, API keys, health checks,
releases, deployment, background work, logging, the public site and SEO, the component kit, setup, the
commands). **Read each one against your code, sentence by sentence**, and correct what differs — a
route you renamed, a check you removed, a default you changed, a screen you do not ship. A page for
something your code does not have is deleted, together with its entry in `wiki.toml` and any link to it.

Rewrite `docs/wiki/pages/brief.md` and `index.md` for **your product**. Scope and external systems are
in your code; who it is for and what it refuses are not — those are the owner's words, or `{missing}`.

### 2. Convert your own pages

For each file your fork added under `docs/concepts/` or `docs/guides/`, decide where its content lives now:

| It described | Goes to |
|---|---|
| Behaviour a person meets — a screen, an endpoint, a command, a job, an outside integration | A wiki page, or a section of one, **written from the code**: every sentence cited `` `file` — `function()` ``, `{missing}` where nothing in the code establishes it. The old page is a pointer to what to look at, never a source to copy |
| A trap only a builder meets — a gate that passes without checking, a shell quirk, a library's silent failure | `docs/CODEMAP.md`, under the layer it concerns |
| A rule about how to work here | `AGENTS.md` — but see Part D: a rule that is not a stack difference goes upstream as a PR, not into your copy |
| Steps a person follows in order (a release, a setup) | A parent page with ordered children named `01-…`, `02-…` — wiki-builder sorts siblings by file name and has no other order setting |

Rules the checks do **not** enforce, and the owner does:

- **Coverage is behaviour and requirements.** Do not document small internals (exceptions, DTOs,
  helpers), table columns nobody meets, or configuration. **No page mirrors a config file or
  `.env.example`**: a setting is one sentence on the page whose behaviour it changes, and only for an
  outside integration or for how the app is set up. Standard Laravel settings are not documented.
- **Goals are few.** Only the pages that say what your product is *for* are goal pages; every other page
  says `goals = false`. **`goals = false` switches off the citation checks**, so cite those pages by hand
  and prove it (step 5).
- **A nested page's title names only what sets it apart within its parent.** Under `health.md`, the child
  is "Notifications", not "Health notifications".
- **Intents come from the owner.** Draft each from their words, list them, and set `status = "approved"`
  only once they agree. A draft renders with a banner and stays out of search and the goals page.
- British English; interface text and names the software owns are formatted as code; a diagram follows
  `references/flowcharts.md` exactly (Yes before No, no full stops on end labels, `accTitle`/`accDescr`,
  `End` never `end`, every box something the code does).

### 3. Convert your requirement contracts

If your fork kept `docs/requirements/` (the closed schema from v5.6.0), each row becomes part of the page
that describes the behaviour: the assertion is a cited sentence, and its `R-<NS>-<n>` goes into an
infobox row's hidden `guaranteed` field — comma-separated when one row carries several. A ✅ row cites the
test that proves it (`tests/Feature/….php` — `it('…')`); a ❌ row is written as the behaviour it asks
for and marked `{missing}`. **An ID is never reused or renumbered**; a withdrawn requirement leaves its
number missing. **Requirement IDs never appear in prose.** Then delete `docs/requirements/`.

```toml
[[infobox]]
group = "Rules"
rows = [
  { label = "Expiry", value = "90 days by default", cite = "expiry", guaranteed = "R-APIKEY-2" },
]
```

### 4. Delete the old tree

```bash
git rm -r docs/BRIEF.md docs/README.md docs/concepts docs/guides
```

Then `ls docs` must show `CODEMAP.md` and `wiki` only.

### 5. Build, check, and prove the exempt pages

```bash
./scripts/dev-wiki.sh build      # records each page's date in docs/wiki/UPDATED.toml — commit it
./scripts/dev-wiki.sh check      # must end "0 problems"
```

Edits move page dates, so **always `build` before `check`**, and commit `UPDATED.toml` with the pages.

Pages that say `goals = false` are exempt from the citation checks. Prove they are cited anyway:

```bash
S=$(mktemp -d)/wiki && cp -R docs/wiki "$S" && rm -rf "$S/site"
find "$S/pages" -name '*.md' -exec sed -i '' '/^goals = false$/d' {} +
printf '\n[budget]\ngoals = 100000\n' >> "$S/wiki.toml"
./scripts/dev-wiki.sh build --wiki "$S" >/dev/null && ./scripts/dev-wiki.sh check --wiki "$S"
```

Loop until no `states something and cites nothing` and no infobox-row problem remains. (On Linux,
`sed -i` takes no `''` argument.)

Words the check refuses even inside a quoted label or diagram label: *currently*, *the one*, *may*,
*just*, *anyone*, *no one*, and pointing words such as *this repository*. Write interface text as code,
and name the part of the system that acts.

---

## Part D — `AGENTS.md` and `CLAUDE.md` (every fork)

**Take the template's file wholesale**, then `cp AGENTS.md CLAUDE.md` — a test enforces that they are
byte-identical. The file is template-managed: the only permitted divergence is a genuine **stack**
difference. What changed:

- The skill table routes `Any page under docs/wiki/` to `writing-wiki-pages`, `docs/CODEMAP.md` to
  `maintaining-project-docs`, and `Any change in behaviour` to `testing-code` **and** `writing-wiki-pages`.
- "Read the docs" names `docs/wiki/pages/brief.md` and `docs/CODEMAP.md`, then the wiki page for the area.
- A new **`## Git conventions`** section carries what `docs/guides/git-conventions.md` held; the one-line
  no-machine-authorship rule stays in Hard rules on purpose.
- **`## Documentation duties`** is rewritten for the wiki. It is the contract every later agent follows;
  read it once now.
- The gate description and the Definition of done include `wiki check`, and say `pnpm check` needs `uv`.

Before you overwrite, list every rule in your `AGENTS.md` that is not in the template's and rehome it:

| A fork rule about | Goes to |
|---|---|
| What the product is, who it serves, what it refuses | `docs/wiki/pages/brief.md` |
| How a subsystem behaves for a person | its wiki page |
| A trap only a builder meets | `docs/CODEMAP.md`, under its layer |
| A domain invariant worth enforcing | a test, not prose |

A rule you cannot rehome belongs in the template for everyone — send it upstream as a PR.

---

## Part E — Repoint every citation of an old docs path (every fork)

Source comments, `.env.example`, `README.md`, `docker/README.md` and `.template/ADOPT.md` cite
documentation paths. Find every one:

```bash
grep -rn "docs/" app config routes tests resources docker .github .env.example README.md AGENTS.md CLAUDE.md .template/ADOPT.md .template/README.md \
  | grep -v "laravel.com/docs\|docs.sentry\|docs.astral\|tailwindcss.com/docs\|developer.mozilla\|spatie.be/docs\|shadcn-vue.com/docs\|docs/wiki\|docs/CODEMAP"
```

`.template/CHANGELOG.md` and the older files in `.template/migrations/` are history and keep their old
paths; they are left out of the scan on purpose. Every hit is repointed to the wiki page that owns the fact (in this template: `docs/concepts/health-checks.md`
→ `docs/wiki/pages/health.md`; `docs/concepts/deployment-endpoints.md` → `docs/wiki/pages/releases.md`;
`docs/concepts/logging.md` → `docs/wiki/pages/logging.md`; `docs/guides/centralized-logging.md` →
`docs/wiki/pages/logging/central-store.md`; `docs/guides/releasing.md` → `docs/wiki/pages/releases.md`;
`docs/guides/adding-tenancy.md` → `docs/wiki/pages/setup/tenancy.md`; `docs/concepts/ziggy-routes.md`,
`docs/guides/troubleshooting.md`, `docs/guides/writing-tests.md` → `docs/CODEMAP.md`). Then prove every
cited page exists:

```bash
for p in $(grep -rhoE "docs/wiki/pages/[A-Za-z0-9_./-]+" AGENTS.md README.md docker/README.md .env.example app routes .template | sort -u); do test -e "$p" || echo "MISSING $p"; done
```

Take `<t>/.template/ADOPT.md` with this release if your fork still carries `.template/`: its adoption
prompt now rewrites the wiki for the product, and its upgrade prompt applies a migration's page changes
to the fork's copy.

---

## Part F — Resolving the `docs/` conflicts when merging this release (every fork)

Merging `v6.1.0` deletes the old tree. For each file your fork had changed, `git status` shows
`deleted by them: docs/concepts/<page>.md`. Resolve each one by hand:

1. `git show :2:docs/concepts/<page>.md > /tmp/<page>.md` — your version, kept aside.
2. Work its content into the wiki page that owns the subject (Part C step 2), **from the code**, not by
   pasting the prose.
3. `git rm docs/concepts/<page>.md` — accept the deletion.

A file your fork **added** under `docs/concepts/` or `docs/guides/` survives the merge untouched (it was
never in the template). Convert it the same way, then `git rm` it: after this release nothing under
`docs/` but `wiki/` and `CODEMAP.md` is read by an agent, so a page left there is a page nobody reads.

`docs/CODEMAP.md` merges with a modify/modify conflict if your fork edited it. Take the template's
structure (it gained the traps and the Ziggy notes) and re-apply your counts and your layers.

---

## Part G — A password-locked documentation site (forks that publish their docs)

Optional. The wiki can be published on a Cloudflare Worker behind a user name and password:

```bash
cp <t>/wrangler.jsonc wrangler.jsonc
cp <t>/docs/wiki/worker.js docs/wiki/worker.js
```

**`name` in `wrangler.jsonc` is a knob**: set it to your Worker's name in the Cloudflare dashboard, or
the deploy targets the template's. Everything else in the file is managed. In the dashboard, the Worker's
build command is

```sh
pip install "git+https://github.com/timothymarois/wiki-builder@v0.5.0" && wiki check && wiki publish _site
```

with the root directory left empty. `WIKI_USER` and `WIKI_PASSWORD` are Worker secrets, and the
dashboard accepts them only once a script-backed version has deployed — deploy once, add the secrets,
deploy again. Details: `docs/wiki/pages/setup/documentation-site.md`.

A fork that will not publish its docs deletes both files and the `setup/documentation-site.md` page with
its link from `setup.md`.

---

## Verify

```sh
uv --version                                  # the machine can run the wrapper
./scripts/dev-wiki.sh build && ./scripts/dev-wiki.sh check      # ends "0 problems"
ls docs                                       # CODEMAP.md  wiki
cmp AGENTS.md CLAUDE.md && echo identical
grep -c "check:wiki" package.json             # 2 (the script, and its place in `check`)
test -f .github/workflows/wiki.yml && grep -c "wiki-builder@v0.5.0" .github/workflows/wiki.yml   # 1
test -d .claude/skills/writing-wiki-pages && ls .claude/skills/writing-wiki-pages/references | wc -l  # 7
git status --short docs/wiki/UPDATED.toml     # committed, not dirty, after the last build
pnpm check                                    # includes check:wiki and AgentInstructionsTest
```

Then open `./scripts/dev-wiki.sh serve` and read `brief.md` as somebody who has never seen the
repository: if a sentence reads as invented rather than sourced, it is marked `{missing}` or it goes.

## Finally

Bump the manifest:

```diff
-    "version": "6.0.2",
+    "version": "6.1.0",
```

Then update the workspace tracker row for this fork.
