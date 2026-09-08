# Concepts

How a subsystem works, and how it fails. One page per subsystem; that page is the source of truth for it.

| Page | Answers |
|---|---|
| [platform.md](./platform.md) | What a fork inherits, who touches it, and how the fork relationship fails |
| [deployment-endpoints.md](./deployment-endpoints.md) | What `/up`, `/health` and `/release` each prove, and why they are not interchangeable |
| [health-checks.md](./health-checks.md) | What `/up` and `/health` check, how each check self-gates to the services a fork runs, and how failures are notified |
| [logging.md](./logging.md) | Where logs go locally and in a container, and how to ship them to a central store |
| [api-keys.md](./api-keys.md) | How API keys authenticate machine callers, why `api.key` is not redundant with `auth:sanctum`, and how keys fail |
| [seo.md](./seo.md) | What is server-rendered, why absolute URLs come from the server, and the two settings that keep an environment out of the index |
| [ziggy-routes.md](./ziggy-routes.md) | How Laravel route names reach Vue, and why `route()` stops finding a route you just added |
