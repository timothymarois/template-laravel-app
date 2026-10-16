---
name: using-mysql
description: Use when creating or changing MySQL tables, columns, indexes, or queries; when planning a MySQL schema migration or online DDL; when reviewing an existing schema, index set, or query, or checking whether a claimed improvement actually holds; or when diagnosing slow queries, bad execution plans, lock contention, deadlocks, replication lag, or connection exhaustion on MySQL or InnoDB. It supplies engine-specific schema, indexing, transaction, and operational rules with the evidence that proves each change. Do not use for PostgreSQL, SQLite, or engine-independent data modelling.
---

# Use MySQL

Make MySQL/InnoDB changes that are safe on the running server and proven by its own evidence.
Engine-neutral modelling — which tables exist, what identifies a row, how history and deletion are
represented — is decided before this; the rules here are what InnoDB adds to it.

## Work in this order

1. **Confirm the version and engine.** Several rules here changed between 5.7, 8.0, and 8.4, so name
   the version an answer assumes:

   ```sql
   select version(), @@default_storage_engine;
   ```

2. **Define the workload**: read/write mix, latency target, data volume, and topology (single node,
   replicas, proxy). A rule that helps one workload can hurt another.
3. **Read only the reference the change reaches** (listed below).
4. **Propose the smallest change that solves the problem**, with its trade-off.
5. **Prove it with the server's evidence**: `EXPLAIN` or `EXPLAIN ANALYZE` before and after, lock
   and connection metrics, and the DDL algorithm the server actually chose. A change that does not
   show up in that evidence did not apply.
6. **For production, state the rollout, the rollback, and the post-deploy check.** Get explicit human
   approval before any drop, delete, truncate, or partition removal.

## Schema

- **Narrow, monotonic primary keys** (`BIGINT UNSIGNED AUTO_INCREMENT`) for write-heavy tables. InnoDB
  stores rows in primary-key order and copies the key into every secondary index, so a random UUID
  key scatters inserts and widens every index. Keep an external UUID in a secondary unique column.
- **`utf8mb4` with `utf8mb4_0900_ai_ci`** — MySQL's `utf8` is the 3-byte `utf8mb3` and cannot store
  emoji or many CJK characters.
- **`DATETIME` over `TIMESTAMP`** when a value can pass 2038-01-19, the `TIMESTAMP` range limit.
- **A lookup table over `ENUM`** when the set changes: every `ENUM` change is an `ALTER TABLE`.

References: [primary-keys](references/primary-keys.md), [data-types](references/data-types.md),
[character-sets](references/character-sets.md), [json-column-patterns](references/json-column-patterns.md).

## Indexing

- Composite order: equality columns first, then the range or sort column. A range predicate stops
  the index filtering on the columns after it.
- Secondary indexes carry the primary key, so `INDEX(status)` already covers `SELECT id … WHERE
  status = ?`.
- Audit unused indexes through `performance_schema`, but its counters reset on restart: an index
  used only by a monthly job looks unused for weeks. Make it invisible before dropping it.

References: [composite-indexes](references/composite-indexes.md),
[covering-indexes](references/covering-indexes.md), [fulltext-indexes](references/fulltext-indexes.md),
[index-maintenance](references/index-maintenance.md).

## Partitioning

- Partitioning is for very large, time-ordered tables with a retention rule. Every unique key,
  including the primary key, must contain the partition columns, and partitioned InnoDB tables
  support neither foreign keys nor `FULLTEXT` indexes — plan it before the table fills.

References: [partitioning](references/partitioning.md).

## Queries

- Read `EXPLAIN`: `type: ALL`, `Using filesort`, and `Using temporary` are signals to investigate, not
  defects by themselves.
- Keep indexed columns bare in `WHERE` — a function, arithmetic, or implicit cast on the column
  prevents index use.
- Large `OFFSET` values scan and discard every skipped row; page by a cursor on an indexed key.

References: [explain-analysis](references/explain-analysis.md),
[query-optimization-pitfalls](references/query-optimization-pitfalls.md),
[n-plus-one](references/n-plus-one.md).

## Transactions and locking

- Stay on InnoDB's default `REPEATABLE READ`. Switch a session to `READ COMMITTED` only when gap-lock
  contention is confirmed in `SHOW ENGINE INNODB STATUS`; see the decision guide in
  [isolation-levels](references/isolation-levels.md).
- Access rows in a consistent order, keep transactions short, do network I/O outside them, and
  retry error 1213 with backoff only when the transaction is safe to repeat.

References: [isolation-levels](references/isolation-levels.md), [deadlocks](references/deadlocks.md),
[row-locking-gotchas](references/row-locking-gotchas.md).

## Operations

- Request the DDL algorithm explicitly (`ALGORITHM=INSTANT`, else `INPLACE, LOCK=NONE`) so the server
  fails instead of silently copying the table. On very large or hot tables, use an online
  schema-change tool such as `gh-ost` or `pt-online-schema-change`.
- Pool connections and size the pool from measurement; raising `max_connections` adds memory and
  contention, not throughput.
- Assume replicas lag; route reads that must see a just-committed write to the primary or wait for
  its GTID.

References: [online-ddl](references/online-ddl.md),
[connection-management](references/connection-management.md),
[replication-lag](references/replication-lag.md).

Read [sources](references/sources.md) when auditing or changing a factual claim, a version
statement, a threshold, or an example.
