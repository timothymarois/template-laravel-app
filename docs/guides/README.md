# Guides

One task each, start to finish. Steps in order, with the check that confirms each one worked.

| Guide | How to |
|---|---|
| [releasing.md](./releasing.md) | Cut a production release: version, deploy, verify live, publish the tag |
| [git-conventions.md](./git-conventions.md) | Name a branch and write a commit: types, slugs, imperative summaries |
| [writing-tests.md](./writing-tests.md) | Write and run the Pest and Vitest suites before committing |
| [tenancy-using.md](./tenancy-using.md) | Enable and use multi-tenancy in a fresh clone or a fork that just turned it on |
| [tenancy-migrating.md](./tenancy-migrating.md) | Adopt multi-tenancy in a fork that already has user data |

Both tenancy guides describe a capability that ships **inert** — `TENANCY_ENABLED=false`, and a fork that
never enables it still keeps the scaffolding and its suite. Their paths are printed to whoever runs
`tenancy:enable` or `tenancy:migrate-existing`, and two tests assert on those strings, so the filenames
are part of the contract rather than a preference.

`tenancy-using.md` runs past the ~200-line guide budget. It stays one file: its steps are cited
individually from `tenancy-migrating.md`, so splitting it would break those cross-references.
