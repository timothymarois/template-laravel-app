---
name: using-postgres
description: Use when creating or altering PostgreSQL tables and columns, choosing types, writing migrations, designing indexes, writing row-level-security policies, granting privileges, configuring connection limits or a pooler, or writing SQL; when reviewing an existing schema, index set, or policy, or checking whether a claimed improvement actually holds; and when diagnosing slow queries, bad EXPLAIN plans, timeouts, connection exhaustion, lock contention, bloat or vacuum problems, or rows visible to the wrong tenant. Do not use for MySQL, SQLite, or engine-independent data modelling.
---

# Use PostgreSQL

Rules for Postgres running anywhere, across eight categories ordered by how much damage getting
them wrong does. Each reference file states the rule, shows the incorrect and the correct SQL,
and explains why the planner or the server behaves that way.

## How to Use

Read the reference file for the rule at hand — do not read all of them. The category prefix tells
you where to look, and [references/_sections.md](references/_sections.md) defines the categories.

| Priority | Category | Impact | Prefix |
|----------|----------|--------|--------|
| 1 | Query Performance | CRITICAL | `query-` |
| 2 | Connection Management | CRITICAL | `conn-` |
| 3 | Security & RLS | CRITICAL | `security-` |
| 4 | Schema Design | HIGH | `schema-` |
| 5 | Concurrency & Locking | MEDIUM-HIGH | `lock-` |
| 6 | Data Access Patterns | MEDIUM | `data-` |
| 7 | Monitoring & Diagnostics | LOW-MEDIUM | `monitor-` |
| 8 | Advanced Features | LOW | `advanced-` |

## Query Performance

- Index what you filter, join, and sort on; confirm with `EXPLAIN` rather than assuming.
- Composite index order is equality columns first, then the range or sort column.
- A partial index over the rows you actually query beats a full one over rows you never touch.

References:
- [query-missing-indexes](references/query-missing-indexes.md)
- [query-composite-indexes](references/query-composite-indexes.md)
- [query-covering-indexes](references/query-covering-indexes.md)
- [query-partial-indexes](references/query-partial-indexes.md)
- [query-index-types](references/query-index-types.md)

## Connection Management

- Postgres connections cost memory. Pool them; do not open one per request.
- `max_connections` follows from RAM and `work_mem`, not from peak client count.
- Transaction-mode pooling breaks anything that assumes session state survives a statement.

References:
- [conn-pooling](references/conn-pooling.md)
- [conn-limits](references/conn-limits.md)
- [conn-prepared-statements](references/conn-prepared-statements.md)
- [conn-idle-timeout](references/conn-idle-timeout.md)

## Security & RLS

- Enforce tenant isolation in the database, not only in application code.
- Wrap a function call in a policy in `(select ...)` so it runs once per statement, not per row.
- Grant the minimum, and revoke the defaults `PUBLIC` starts with.

References:
- [security-rls-basics](references/security-rls-basics.md)
- [security-rls-performance](references/security-rls-performance.md)
- [security-privileges](references/security-privileges.md)

## Schema Design

- Pick the narrowest type that holds the data, and state `NOT NULL` where it is true.
- Constraints belong in the database; application-only validation drifts.
- Index every foreign key column — Postgres does not do it for you.

References:
- [schema-primary-keys](references/schema-primary-keys.md)
- [schema-data-types](references/schema-data-types.md)
- [schema-constraints](references/schema-constraints.md)
- [schema-foreign-key-indexes](references/schema-foreign-key-indexes.md)
- [schema-lowercase-identifiers](references/schema-lowercase-identifiers.md)
- [schema-partitioning](references/schema-partitioning.md)

## Concurrency & Locking

- Keep transactions short and do network I/O outside them.
- Touch rows in a consistent order across code paths to avoid deadlocks.
- `for update skip locked` is how you build a queue on a table.

References:
- [lock-short-transactions](references/lock-short-transactions.md)
- [lock-deadlock-prevention](references/lock-deadlock-prevention.md)
- [lock-skip-locked](references/lock-skip-locked.md)
- [lock-advisory](references/lock-advisory.md)

## Data Access Patterns

- One query returning N rows, never N queries returning one row.
- Keyset pagination, not `OFFSET`, once the table is large.
- Batch writes; `COPY` for bulk loads.

References:
- [data-n-plus-one](references/data-n-plus-one.md)
- [data-pagination](references/data-pagination.md)
- [data-batch-inserts](references/data-batch-inserts.md)
- [data-upsert](references/data-upsert.md)

## Monitoring & Diagnostics

- `EXPLAIN (analyze, buffers)` on the real query with real parameters; the plan is the evidence.
- `pg_stat_statements` tells you which queries are worth optimizing at all.
- Autovacuum keeping up is a precondition for every other performance rule here.

References:
- [monitor-explain-analyze](references/monitor-explain-analyze.md)
- [monitor-pg-stat-statements](references/monitor-pg-stat-statements.md)
- [monitor-vacuum-analyze](references/monitor-vacuum-analyze.md)

## Advanced Features

- Full-text search needs a stored `tsvector` and a GIN index, not a per-query `to_tsvector`.
- JSONB is queryable, but index the access pattern you actually use.

References:
- [advanced-full-text-search](references/advanced-full-text-search.md)
- [advanced-jsonb-indexing](references/advanced-jsonb-indexing.md)

## Guardrails

- Measure before and after. A rule that does not show up in the plan or the timings did not apply.
- Note version-specific behavior; Postgres changes planner and index capabilities between majors.
- Get explicit human approval before destructive operations — drops, truncates, unbounded deletes,
  and index builds that are not `concurrently` on a live table.

## Confirm the version before applying a version-sensitive rule

```sql
select version();
```

Postgres supports five major versions at a time and changes planner behavior and index capabilities
between them. State the major an answer assumes. Read
[references/sources.md](references/sources.md) when auditing or changing a factual claim, a version
statement, or an example.
