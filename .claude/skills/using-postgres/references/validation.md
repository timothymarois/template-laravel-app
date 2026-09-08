# Using PostgreSQL Validation

This is the current validation record for `using-postgres`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for PostgreSQL behavior — types and constraints, index classes, execution
plans, MVCC and locking, connection and pool behavior, vacuum and bloat, row-level security and
privileges, and migration safety. It should not activate for MySQL, SQLite, or engine-independent
data modelling.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| PGS-T01 | Write an RLS policy that isolates tenants | Load |
| PGS-T02 | "Some customers can see another account's rows" on a Postgres app | Load |
| PGS-T03 | Diagnose an InnoDB deadlock | Do not load; `using-mysql` owns it |
| PGS-T04 | Decide whether history belongs in a separate table | Do not load; engine-independent modelling |
| PGS-T05 | Rename a UI label in an app backed by Postgres | Do not load |
| PGS-T06 | Add an index to a live table without blocking writes | Load |
| PGS-T07 | Laravel migration plus the Postgres locking behavior it triggers | Compose with `using-laravel`; each owns its half |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| PGS-W01 | An RLS policy appears to do nothing in testing | Identify the owner bypass and `FORCE ROW LEVEL SECURITY`, not a policy syntax error |
| PGS-W02 | A policy calls a function per row | Wrap it as `(select ...)` so it evaluates once per statement, and say why per-row evaluation is the cost |
| PGS-W03 | A foreign key exists and deletes are slow | Add the index on the referencing column; state that Postgres does not create it |
| PGS-W04 | `max_connections` proposed from peak client count | Derive it from RAM and `work_mem`, noting `work_mem` is per operation, not per query |
| PGS-W05 | `CREATE INDEX` proposed on a live table | Require `CONCURRENTLY`, note it cannot run in a transaction block, and cover the invalid-index cleanup if it fails |
| PGS-W06 | "The query is optimized now" | Reject fluent assurance; require `EXPLAIN (analyze, buffers)` on the real query with real parameters, before and after |
| PGS-W07 | Server major version cannot be determined | Run `select version()` or stop and name the unknown; do not assume planner or index capability |
| PGS-W08 | Index-only scan proposed as the fix on a bloated table | Keep the visibility-map dependency: without adequate vacuuming the scan degrades |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. Cases marked for the sampled run are PGS-T01, PGS-T03, PGS-W06, and
  PGS-T05, plus a baseline comparison without the package on PGS-W01.
- Codex: not run.

No case below is marked passed. Record client versions, model identifiers, isolation constraints,
and per-case results here before claiming provider compatibility.

## Limits

No case runs against a live PostgreSQL server. PGS-W01 is the highest-value baseline comparison
because the owner-bypass behavior is the failure most likely to be missed without this package.
