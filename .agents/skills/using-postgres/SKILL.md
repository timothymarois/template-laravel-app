---
name: using-postgres
description: Use when creating or altering PostgreSQL tables and columns, choosing types, writing migrations, designing indexes, writing row-level-security policies, granting privileges, configuring connection limits or a pooler, or writing SQL; when reviewing an existing schema, index set, or policy, or checking whether a claimed improvement actually holds; and when diagnosing slow queries, bad EXPLAIN plans, timeouts, connection exhaustion, lock contention, bloat or vacuum problems, or rows visible to the wrong tenant. It supplies Postgres-specific rules with the incorrect and correct SQL and the planner or server behavior behind each. Do not use for MySQL, SQLite, or engine-independent data modelling.
---

# Use PostgreSQL

Postgres-specific rules: each reference states the rule, shows the incorrect and the correct SQL,
and explains why the planner or the server behaves that way. Engine-neutral modelling — which tables
exist, what identifies a row, pagination shape, history and deletion — is decided before this; the
rules here are what Postgres adds to it.

## Work in this order

1. **Confirm the major version.** Postgres supports five majors at a time and changes planner
   behavior and index capabilities between them, so state the major an answer assumes:

   ```sql
   select version();
   ```

2. **Read only the reference for the rule at hand** — the filename prefix names the area.
3. **Measure before and after** with `explain (analyze, buffers)` on the real query with real
   parameters. A rule that does not show up in the plan or the timings did not apply.
4. **Get explicit human approval before destructive or blocking operations** — drops, truncates,
   unbounded deletes, and any DDL that is not lock-safe on a live table.

## Query performance (`query-`)

- Index what you filter, join, and sort on; confirm with `EXPLAIN` rather than assuming.
- Composite index order is equality columns first, then the range or sort column.
- A partial index over the rows you actually query beats a full one over rows you never touch.

References:
- [query-missing-indexes](references/query-missing-indexes.md)
- [query-composite-indexes](references/query-composite-indexes.md)
- [query-covering-indexes](references/query-covering-indexes.md)
- [query-partial-indexes](references/query-partial-indexes.md)
- [query-index-types](references/query-index-types.md)

## Connection management (`conn-`)

- Postgres connections cost memory. Pool them; do not open one per request.
- `max_connections` follows from RAM and `work_mem`, not from peak client count.
- Transaction-mode pooling breaks anything that assumes session state survives a transaction.

References:
- [conn-pooling](references/conn-pooling.md)
- [conn-limits](references/conn-limits.md)
- [conn-prepared-statements](references/conn-prepared-statements.md)
- [conn-idle-timeout](references/conn-idle-timeout.md)

## Security and RLS (`security-`)

- Enforce tenant isolation in the database, not only in application code, and set the tenant
  inside each transaction.
- Wrap a row-independent function call in a policy in `(select ...)` so it runs once per
  statement, not per row.
- Grant the minimum, and revoke the defaults `PUBLIC` starts with.

References:
- [security-rls-basics](references/security-rls-basics.md)
- [security-rls-performance](references/security-rls-performance.md)
- [security-privileges](references/security-privileges.md)

## Schema and migrations (`schema-`)

- Pick the narrowest type that holds the data, and state `NOT NULL` where it is true.
- Index every foreign key column — Postgres does not do it for you.
- Change a live table with lock-safe DDL: `concurrently` for indexes, `not valid` then `validate`
  for constraints, and a `lock_timeout` on every migration.

References:
- [schema-safe-migrations](references/schema-safe-migrations.md)
- [schema-primary-keys](references/schema-primary-keys.md)
- [schema-data-types](references/schema-data-types.md)
- [schema-constraints](references/schema-constraints.md)
- [schema-foreign-key-indexes](references/schema-foreign-key-indexes.md)
- [schema-lowercase-identifiers](references/schema-lowercase-identifiers.md)
- [schema-partitioning](references/schema-partitioning.md)

## Concurrency and locking (`lock-`)

- Keep transactions short and do network I/O outside them.
- Touch rows in a consistent order across code paths to avoid deadlocks.
- `for update skip locked` is how you build a queue on a table.

References:
- [lock-short-transactions](references/lock-short-transactions.md)
- [lock-deadlock-prevention](references/lock-deadlock-prevention.md)
- [lock-skip-locked](references/lock-skip-locked.md)
- [lock-advisory](references/lock-advisory.md)

## Data access (`data-`)

- One query returning N rows, never N queries returning one row.
- Batch writes; `COPY` for bulk loads; `on conflict` for insert-or-update.
- For keyset pagination, a row comparison `where (created_at, id) > ($1, $2)` is served by a
  composite B-tree index on `(created_at, id)`.

References:
- [data-n-plus-one](references/data-n-plus-one.md)
- [data-batch-inserts](references/data-batch-inserts.md)
- [data-upsert](references/data-upsert.md)

## Monitoring and diagnostics (`monitor-`)

- `explain (analyze, buffers)` on the real query is the evidence; `analyze` executes the statement.
- `pg_stat_statements` tells you which queries are worth optimizing at all.
- Autovacuum keeping up is a precondition for every other performance rule here.

References:
- [monitor-explain-analyze](references/monitor-explain-analyze.md)
- [monitor-pg-stat-statements](references/monitor-pg-stat-statements.md)
- [monitor-vacuum-analyze](references/monitor-vacuum-analyze.md)

## Advanced features (`advanced-`)

- Full-text search needs a stored `tsvector` and a GIN index, not a per-query `to_tsvector`.
- JSONB is queryable, but index the access pattern you actually use.

References:
- [advanced-full-text-search](references/advanced-full-text-search.md)
- [advanced-jsonb-indexing](references/advanced-jsonb-indexing.md)

Read [references/sources.md](references/sources.md) when auditing or changing a factual claim, a
version statement, or an example.
