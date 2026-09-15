+++
title = "Production pull request"
subtitle = "the pull request from the release branch to production, its checks and the deploy it starts"
status = "approved"
goals = false
intent = """
The production pull request exists so that a deploy is a reviewed merge, checked on the exact commit
that ships, and so that nothing else reaches production. The version guard for main should stay out of
its way, because a release branch is versioned on purpose.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Base branch", value = "production", cite = "manifest" },
  { label = "Head branch", value = "release/vMAJOR.MINOR.PATCH", cite = "branch" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Checks", value = "PHP, JavaScript, Docker configuration", cite = "workflows" },
  { label = "Neutral-version guard", value = "not run", cite = "guard" },
  { label = "Deploy", value = "the merged production branch", missing = true },
]
+++

The release branch, named `release/` and the tag, is opened as a pull request against `production`, the
branch the publish script reads from the manifest, not against `main`.[^branch][^manifest] It is the second of the four steps described on
[Releases](../releases.md), after [Preparation](01-preparation.md) and before
[Publication](03-publication.md).

```sh
gh pr create --base production --head release/v1.2.3 --title "Release v1.2.3"
```

## Checks

Every pull request to `production`, and every push to it, runs the PHP and JavaScript workflows, and the
Docker configuration workflow when a Docker file changed.[^workflows] The neutral-version guard does not
run here: it runs only for changes bound for `main`.[^guard]

## Deploy

Merging the pull request puts the release commit on `production`, and Coolify deploys that branch as
the new container.{missing} The deploy itself, its phases and the container gate are described on
[Deployment](../deployment.md). Once the new container answers, the publish script waits for the
production address to serve the new version, as [Publication](03-publication.md) describes.[^wait]

[^manifest]: `scripts/publish-production-release` — `manifest_deploy()` reads `deploy.productionBranch`
    from `template-manifest.json`, whose shipped value is `production`.
[^branch]: `scripts/prepare-production-release` — `expected_branch` is `release/` followed by the tag.
[^workflows]: `.github/workflows/php-checks.yml` and `.github/workflows/js-checks.yml` — `on` lists
    `push` and `pull_request` for `main` and `production`; `.github/workflows/docker-config.yml` — the
    same branches, filtered to `Dockerfile`, `docker/**` and the Docker tests.
[^guard]: `.github/workflows/js-checks.yml` — the `Require neutral package versions on main` step runs
    only when `github.base_ref` or `github.ref_name` is `main`.
[^wait]: `scripts/publish-production-release` — the verification loop polls `deploy.productionUrl` until
    `/release` reports the stored version.
