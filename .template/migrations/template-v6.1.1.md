# template-laravel-app v6.1.1 — wiki-builder v0.6.0

**Every fork acts.** The wiki tool gains PDFs and a new check, and fixes a local server that stopped
the site's own script from running. Nothing in the application changes.

## Prerequisites

```sh
jq -r .version template-manifest.json     # 6.1.0
pnpm check                                # green before you start
```

## Part A — Take the release (every fork)

Bump the pin everywhere it appears, then record it:

```sh
sed -i '' 's/wiki-builder@v0\.5\.0/wiki-builder@v0.6.0/g; s/WIKI_VERSION=v0\.5\.0/WIKI_VERSION=v0.6.0/g' \
  scripts/dev-wiki.sh .github/workflows/wiki.yml wrangler.jsonc
./scripts/dev-wiki.sh sync                # rewrites the skill, records 0.6.0 in wiki.toml
./scripts/dev-wiki.sh build && ./scripts/dev-wiki.sh check
```

Your own pages quote the pin too: search `docs/wiki/pages` for `v0.5.0` and correct each sentence and
its reference. On Linux, `sed -i` takes no `''` argument.

**`wiki check` now refuses a link to a PDF that would be dead once the site is published.** Each
refusal names the page, the file and the fix. A wiki that links no PDF passes as it did before.

If you publish your docs on a Cloudflare Worker, the dashboard build command names the release as well:

```sh
pip install "git+https://github.com/timothymarois/wiki-builder@v0.6.0" && wiki check && wiki publish _site
```

## PDFs, if you want them

A PDF a page links lives in `docs/wiki/files/`, and the build publishes only the ones a page links. A
file over 20 MiB is refused. A PDF is never a citation: cite the code, and link the document.

```md
The signed policy is kept with the wiki: [Refund policy](../files/refund-policy.pdf).
```

## Verify

```sh
grep -rc "wiki-builder@v0.6.0" .github/workflows/wiki.yml        # 1
grep -c "WIKI_VERSION=v0.6.0" scripts/dev-wiki.sh                # 1
grep -A1 '^\[tool\]' docs/wiki/wiki.toml                         # version = "0.6.0"
./scripts/dev-wiki.sh check                                      # ends "0 problems"
pnpm check                                                       # exit 0
```

## Finally

Bump the manifest:

```diff
-    "version": "6.1.0",
+    "version": "6.1.1",
```

Then update the workspace tracker row for this fork.
