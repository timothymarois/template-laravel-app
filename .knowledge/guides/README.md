# guides/ — writing standards & how-tos (catalog)

**`docs-*.md`** — the shipped writing standards (versioned by `knowledge-template`; don't edit per project). **`<verb-noun>.md`** — project how-tos you author for recurring tasks in this repo.

## Contents — maintained by hand

Add a row when you add a guide; `doc-lint` fails the build if one is missing.

### Shipped standards

| Guide | Standard for |
|---|---|
| [docs-prd.md](./docs-prd.md) | PRDs in `../prd/` (and drafts) |
| [docs-research.md](./docs-research.md) | Research notes in `../research/` |
| [docs-brief.md](./docs-brief.md) | `../BRIEF.md` |
| [docs-codemap.md](./docs-codemap.md) | `../CODEMAP.md` |
| [docs-memory.md](./docs-memory.md) | `../MEMORY.md` |
| [docs-overview.md](./docs-overview.md) | `../OVERVIEW.md` |
| [docs-agents.md](./docs-agents.md) | `AGENTS.md` |

### Project how-tos

| Guide | How to |
|---|---|
| [health-checks.md](./health-checks.md) | Configure, tailor, and read the spatie/laravel-health checks and wire failure notifications |
| [logging.md](./logging.md) | Read application logs locally, and ship container logs to a central searchable store on deploy |
| [stack-examples.md](./stack-examples.md) | Look up the worked ✅/❌ code patterns for this stack (companion to the AGENTS.md Best Practices rules) |
| [tenancy-migrations.md](./tenancy-migrations.md) | Adopt multi-tenancy in a fork that already has user data (data-migration path) |
| [tenancy-usage.md](./tenancy-usage.md) | Enable and use multi-tenancy in a fresh clone or a fork that just upgraded to v5.0.0 |
| [write-tests.md](./write-tests.md) | Write and run the Pest (backend) and Vitest (JS/Vue) test suites before committing |

## Rules for a project how-to

One task per file, named for the action. Steps in order with the actual commands and a check that confirms success. Self-contained.
