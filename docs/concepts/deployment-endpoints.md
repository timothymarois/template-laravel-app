# Deployment endpoints

Three unauthenticated GET endpoints make a deployment observable from outside the container. Each answers a
different question, and they are not interchangeable.

| Endpoint | Question it answers | Backed by |
|---|---|---|
| `/up` | Is the application booting and serving? | Laravel, wired in `bootstrap/app.php` (`health: '/up'`) |
| `/health` | Are the services this fork depends on actually working? | `spatie/laravel-health`, checks registered in `AppServiceProvider` |
| `/release` | Which version is running right now? | `ReleaseController` reading `config('release.version')` |

Together they let a deploy be **polled rather than guessed at**: wait for `/release` to report the version
you shipped, then confirm `/up` and `/health` are green. That is exactly what
`scripts/publish-production-release` does before it will publish a tag — see
[`../guides/releasing.md`](../guides/releasing.md).

## `/up` — the container gate

Laravel's own lightweight endpoint. It proves the framework booted and can serve a request. It does not
touch the database, Redis, or the queue.

**This is the one a container health check and a load balancer point at.** Pointing them at `/health`
instead means a degraded dependency takes the container out of rotation, turning a recoverable problem into
an outage.

## `/health` — the dependency report

A JSON snapshot, `503` when a check fails. Meant for an uptime monitor, not a load balancer.

Checks **self-gate on configuration**, so a fork only reports on what it actually runs:

| Check | Runs when |
|---|---|
| Used disk space | Always |
| Database | A database name is configured — skipped on a DB-less fork |
| Redis | Redis backs cache, queue, or sessions |
| Horizon | The Horizon class is present |
| Queue | `queue.default` is not `sync` |
| Schedule | Always, tolerating a 2-minute heartbeat age |

The scheduler runs `health:check` every minute and stores the result, so `/health` serves the latest
snapshot rather than running every check on each request. **A fork with no scheduler gets a stale or empty
snapshot** — that is the trade, and it is why `/up` stays the container gate.

Detail, tailoring, and failure notifications: [health-checks.md](./health-checks.md).

## `/release` — the deployed version

```json
{"version": "1.4.2"}
```

Read from `composer.json` via `config/release.php`, and sent with `Cache-Control: no-store`. Caching it
would be worse than not having it: a cached response reports a version that is no longer deployed, which
is precisely the question the endpoint exists to answer.

On `main` it reports `0.0.0`, because `main` never carries a release version. A real version appears only
on a release branch and on `production`.

## How it fails

| Failure | What you see | Why |
|---|---|---|
| `/release` reports the previous version after a deploy | The publish script keeps polling and eventually refuses | The deploy has not finished, or the container did not restart. Check the deployment log before retrying |
| `/release` reports `0.0.0` in production | Same refusal | `production` was deployed from a branch that never had its manifests bumped |
| `/release` returns HTML instead of JSON | The publish script treats it as unavailable | The route is missing — the deployed build predates this endpoint. A substring match on that HTML can look like success; the script parses strict JSON to avoid exactly that |
| `/health` is `503` but the site works | Monitor alerts, users unaffected | A gated check is enabled for a service the fork does not run. Remove the check, do not silence the monitor |
| `/up` is green while the site is broken | No alert | By design — `/up` proves boot, not correctness. That is what `/health` is for |

## What is not here

None of the three is authenticated, so none may disclose anything a visitor should not see. `/release`
returns one version string and nothing else; `/health` reports check names and status, never connection
strings or credentials. Adding a check that echoes configuration into `/health` would leak it publicly.
