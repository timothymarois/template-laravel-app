+++
title = "Releases"
subtitle = "the version on main and production, the /release address and the four release steps"
status = "approved"
goals = false
intent = """
Releases exist so that a version is published only for code that is proved live and healthy in
production, and so that main never claims to be a release. An operator should be able to read from one
address which version is running.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Address", value = "/release", cite = "endpoint" },
  { label = "Branch", value = "production", cite = "manifest" },
  { label = "Settings", value = "template-manifest.json, deploy block", cite = "manifest" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Version on main", value = "0.0.0", cite = "neutral" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Tag", value = "after a verified deploy", cite = "order" },
  { label = "Version address", value = "never cached", cite = "endpoint" },
  { label = "Placeholders", value = "refused", cite = "manifest" },
]
+++

A release writes a version on a release branch, merges it into `production`, waits for the deploy to
prove itself live, and only then publishes the tag.[^order] The version lives in `composer.json` and
`package.json`; on `main` both read `0.0.0`, and a real version exists only on a release branch and on
`production`.[^neutral] The four steps, in order, are [Preparation](releases/01-preparation.md),
[Production pull request](releases/02-production-pull-request.md),
[Publication](releases/03-publication.md) and [Reconciliation](releases/04-reconciliation.md).

## Version address

`GET /release` answers with the running version as JSON and `Cache-Control: no-store, max-age=0`, so a
monitor or the publish script reads the version of the container that answered, never an earlier
one.[^endpoint] The version is read from `composer.json`, and reads `missing` when that file carries
none.[^config] On `main`, and on a local site, the answer is the JSON below, copied from a local
site.[^neutral]
```json
{"version":"0.0.0"}
```

## Identity

The publish script reads the repository, the production address and the production branch from the
`deploy` block of `template-manifest.json`, and refuses the shipped placeholders `owner/repo` and
`https://example.com`, so a fork fills that block in before its first release.[^manifest]

```json
"deploy": {
    "repository": "owner/repo",
    "productionUrl": "https://example.com",
    "productionBranch": "production"
}
```

## Neutral main

A change bound for `main` whose manifests carry a version is refused by the neutral-version guard with
`Neutral version check refused: composer.json carries release version '1.2.3'; main must stay at 0.0.0 —
reset both manifests on the reconciliation branch`.[^neutral] The guard runs for pull requests to `main`
and pushes to `main`, never for a release branch, which is versioned on purpose.[^ci] A versioned `main`
blocks the next release, because preparation requires both manifests at `0.0.0` and a branch that starts
at `origin/main`.[^neutral]

[^order]: `scripts/prepare-production-release` — the closing lines print `Review and commit these
    changes, then open a pull request to production.` and `Do not tag or publish the release until the
    production deployment is approved and verified.`; `scripts/publish-production-release` — the
    verification loop runs before `gh release create`.
[^neutral]: `scripts/assert-neutral-main-version` — the header comment and the Python block, which
    exits with that message when either manifest is not `0.0.0`; `composer.json` and `package.json` —
    `version` is `0.0.0`.
[^endpoint]: `app/Http/Controllers/ReleaseController.php` — `__invoke()` returns `config('release.version')`
    as JSON with `Cache-Control: no-store, max-age=0`.
[^config]: `config/release.php` — reads `version` from `composer.json`, else `missing`.
[^manifest]: `scripts/publish-production-release` — `manifest_deploy()` reads `deploy.repository`,
    `deploy.productionUrl` and `deploy.productionBranch` from `template-manifest.json` and exits with
    `deploy.{key} is still the template placeholder ({value})` for the two placeholders.
[^ci]: `.github/workflows/js-checks.yml` — the `Require neutral package versions on main` step runs only
    when `github.base_ref` or `github.ref_name` is `main`.
