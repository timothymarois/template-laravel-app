+++
title = "template-laravel-app"
subtitle = "a Laravel starter with accounts, an admin area, API keys, health checks and a release process"
status = "approved"
goals = false
intent = """
The front page exists to send a reader to the part of the template they came for, in one screen.
"""
+++

**template-laravel-app** is the starter a product forks from instead of a bare framework install.[^manifest]
Every page here is checked on every pull request, so the wiki is read instead of the source.[^check]

The [brief](brief.md) says in one screen what it is, who it is for and what it refuses.

The [goals page](goals.md) collects what every goal page is for.

A person has an [account](accounts.md), an operator works in the [admin area](admin.md), and a machine
calls the [API](api.md) with an [API key](api-keys.md).

A visitor meets the [public site](site.md) and a crawler its [SEO](seo.md); a builder assembles screens
from the [component kit](components.md).

In production the [health checks](health.md) say which services work, a [release](releases.md) is tagged
only once its deploy is proved live, the [deployment](deployment.md) image runs every process,
[background work](background-work.md) runs under Horizon, and [logging](logging.md) goes to one central
store.

A new project starts with [setup](setup.md), and an operator at a terminal has the [commands](commands.md).

[^manifest]: `template-manifest.json` — `template` names the starter and `version` the release a checkout is
    aligned with.
[^check]: `.github/workflows/wiki.yml` — the `Wiki check` job runs on every pull request and on a push to `main`.
