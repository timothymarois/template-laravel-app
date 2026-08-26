# Concepts

How a subsystem works, and how it fails. One page per subsystem; that page is the source of truth for it.

| Page | Answers |
|---|---|
| [health-checks.md](./health-checks.md) | What `/up` and `/health` check, how each check self-gates to the services a fork runs, and how failures are notified |
| [logging.md](./logging.md) | Where logs go locally and in a container, and how to ship them to a central store |
| [ziggy-routes.md](./ziggy-routes.md) | How Laravel route names reach Vue, and why `route()` stops finding a route you just added |
