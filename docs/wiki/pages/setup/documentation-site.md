+++
title = "Documentation site"
subtitle = "the published wiki on a Cloudflare Worker behind a user name and password"
status = "approved"
goals = false
intent = """
The documentation site exists so that the wiki can be read by people who never open the repository, and
by no other reader: every request is answered only after the browser sends the user name and password the
Worker holds as secrets. Cloudflare builds it from the repository on every push, and only from a wiki
that passed its checks.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Settings file", value = "wrangler.jsonc", cite = "wrangler" },
  { label = "Worker script", value = "docs/wiki/worker.js", cite = "wrangler" },
  { label = "Secrets", value = "WIKI_USER, WIKI_PASSWORD", cite = "secrets" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Assets folder", value = "_site", cite = "assets" },
  { label = "Worker name", value = "template-laravel-app-docs", note = "a fork names its own", cite = "wrangler" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Build", value = "stops when wiki check fails", cite = "build" },
  { label = "Unsigned request", value = "401, the browser asks", cite = "worker" },
]
+++

The published wiki is a folder of static pages served by a Cloudflare Worker that asks the browser for a
user name and password before it serves anything, the markdown copies included.[^worker] Writing the pages
is a builder's task, held to the `writing-wiki-pages` skill under `.claude/skills`, and is not described
here.[^skill]

## Settings

`wrangler.jsonc` at the repository root names the Worker, its script and the assets folder.[^wrangler] The
`name` must match the Worker created in the Cloudflare dashboard, so a fork changes that one line and
nothing else in the file.{missing} The script runs before any asset is served, and serves the asset itself
through the `ASSETS` binding once the request is accepted.[^assets] No `workers.dev` address is enabled, so
the site answers only on the domain given to the Worker.[^workersdev]

| Dashboard setting | Value |
|---|---|
| Build command | `pip install "git+https://github.com/timothymarois/wiki-builder@v0.6.0" && wiki check && wiki publish _site`[^build] |
| Root directory | empty[^root] |
| Deploy command | the default, `npx wrangler deploy`[^root] |

The build installs the same wiki-builder release `scripts/dev-wiki.sh` runs, checks the wiki and stops on
any problem, then publishes the site into `_site`.[^build] `pnpm check:wiki` and the `Wiki check` job on
every pull request run that check before a change reaches `main`.[^check]

## Sign-in

The Worker answers a request without credentials, or with wrong ones, with `401` and a
`WWW-Authenticate` header, which makes the browser ask for a user name and password; it compares both in
constant time.[^worker] It answers `500` with `WIKI_USER and WIKI_PASSWORD are not set.` while either
secret is missing.[^worker] Basic authentication sends the password unencrypted, so the site is only safe
over HTTPS, which the Worker's domain provides.[^basic]

## Secrets

`WIKI_USER` and `WIKI_PASSWORD` are added in the dashboard under the Worker's settings, Variables and
Secrets, as type Secret, and reach the script through its `env`.[^secrets] The dashboard accepts them only
once a version of the Worker with a script has deployed, so the first deployment answers `500` until they
are added and the Worker is deployed again.{missing}

[^worker]: `docs/wiki/worker.js` — `fetch()` returns `signIn()`, a `401` carrying
    `WWW-Authenticate: Basic realm="docs", charset="UTF-8"`, when the `Authorization` header is missing,
    not `Basic`, undecodable or wrong; compares with `timingSafeEqual()`; returns `500` with
    `WIKI_USER and WIKI_PASSWORD are not set.` when either is empty; and otherwise returns
    `env.ASSETS.fetch(request)`.
[^skill]: `.claude/skills/writing-wiki-pages/SKILL.md` — the instructions `scripts/dev-wiki.sh sync` puts
    in the project.
[^wrangler]: `wrangler.jsonc` — `name` is `template-laravel-app-docs`, `main` is `docs/wiki/worker.js`,
    and `assets.directory` is `./_site` with `binding` `ASSETS`.
[^assets]: `wrangler.jsonc` — `assets.run_worker_first` is `true`; Cloudflare Docs —
    [Static assets binding](https://developers.cloudflare.com/workers/static-assets/binding/):
    `run_worker_first` set to `true` runs the Worker script before an asset that would otherwise match,
    and `env.ASSETS.fetch()` forwards a request to the project's static assets.
[^workersdev]: `wrangler.jsonc` — `workers_dev` is `false`; Cloudflare Docs —
    [Configuration](https://developers.cloudflare.com/workers/wrangler/configuration/): `workers_dev`
    enables the `*.workers.dev` subdomain and defaults to `true`.
[^build]: `wrangler.jsonc` — the comment recording the build command; `scripts/dev-wiki.sh` —
    `WIKI_VERSION=v0.6.0`; Cloudflare Docs —
    [Builds configuration](https://developers.cloudflare.com/workers/ci-cd/builds/configuration/): the build
    command runs before the deploy command.
[^root]: Cloudflare Docs —
    [Builds configuration](https://developers.cloudflare.com/workers/ci-cd/builds/configuration/): the
    root directory is optional and defines where the build command runs, and the deploy command defaults
    to `npx wrangler deploy`.
[^check]: `package.json` — `check:wiki` runs `./scripts/dev-wiki.sh check`; `.github/workflows/wiki.yml` —
    the `Wiki check` job runs `timothymarois/wiki-builder@v0.6.0` on every pull request and a push to `main`.[^basic]: Cloudflare Docs — [HTTP Basic Authentication](https://developers.cloudflare.com/workers/examples/basic-auth/):
    Basic authentication sends credentials unencrypted and must be used over HTTPS; credentials are
    compared with `crypto.subtle.timingSafeEqual`, and a `401` with `WWW-Authenticate` makes the browser
    prompt.
[^secrets]: Cloudflare Docs — [Secrets](https://developers.cloudflare.com/workers/configuration/secrets/):
    a secret is added under the Worker's Settings, Variables and Secrets, with type Secret, then
    deployed, and is read from the `env` parameter of `fetch`.
