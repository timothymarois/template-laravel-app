---
name: using-mysql
description: Use when creating or changing MySQL tables, columns, indexes, or queries; when planning a MySQL schema migration or online DDL; when reviewing an existing schema, index set, or query, or checking whether a claimed improvement actually holds; or when diagnosing slow queries, bad execution plans, lock contention, deadlocks, replication lag, or connection exhaustion on MySQL or InnoDB. It supplies engine-specific schema, indexing, transaction, and operational rules with the evidence that proves each change. Do not use for PostgreSQL, SQLite, or engine-independent data modelling.
---

# Use MySQL

Use this skill to make safe, measurable MySQL/InnoDB changes.

## Workflow
1. Define workload and constraints (read/write mix, latency target, data volume, MySQL version, deployment topology — single node, replicas, proxy).
2. Read only the relevant reference files linked in each section below.
3. Propose the smallest change that can solve the problem, including trade-offs.
4. Validate with evidence (`EXPLAIN`, `EXPLAIN ANALYZE`, lock/connection metrics, and production-safe rollout steps).
5. For production changes, include rollback and post-deploy verification.

## Schema Design
- Prefer narrow, monotonic PKs (`BIGINT UNSIGNED AUTO_INCREMENT`) for write-heavy OLTP tables.
- Avoid random UUID values as clustered PKs; if external IDs are required, keep UUID in a secondary unique column.
- Always `utf8mb4` / `utf8mb4_0900_ai_ci`. Prefer `NOT NULL`, `DATETIME` over `TIMESTAMP`.
- Lookup tables over `ENUM`. Normalize to 3NF; denormalize only for measured hot paths.

References:
- [primary-keys](references/primary-keys.md)
- [data-types](references/data-types.md)
- [character-sets](references/character-sets.md)
- [json-column-patterns](references/json-column-patterns.md)

## Indexing
- Composite order: equality first, then range/sort (leftmost prefix rule).
- Range predicates stop index usage for subsequent columns.
- Secondary indexes include PK implicitly. Prefix indexes for long strings.
- Audit via `performance_schema` — drop indexes with `count_read = 0`.

References:
- [composite-indexes](references/composite-indexes.md)
- [covering-indexes](references/covering-indexes.md)
- [fulltext-indexes](references/fulltext-indexes.md)
- [index-maintenance](references/index-maintenance.md)

## Partitioning
- Partition time-series (>50M rows) or large tables (>100M rows). Plan early — retrofit = full rebuild.
- Include partition column in every unique/PK. Always add a `MAXVALUE` catch-all.

References:
- [partitioning](references/partitioning.md)

## Query Optimization
- Check `EXPLAIN` — red flags: `type: ALL`, `Using filesort`, `Using temporary`.
- Cursor pagination, not `OFFSET`. Avoid functions on indexed columns in `WHERE`.
- Batch inserts (500–5000 rows). `UNION ALL` over `UNION` when dedup unnecessary.

References:
- [explain-analysis](references/explain-analysis.md)
- [query-optimization-pitfalls](references/query-optimization-pitfalls.md)
- [n-plus-one](references/n-plus-one.md)

## Transactions & Locking
- Default: `REPEATABLE READ` (gap locks). Use `READ COMMITTED` for high contention.
- Consistent row access order prevents deadlocks. Retry error 1213 with backoff.
- Do I/O outside transactions. Use `SELECT ... FOR UPDATE` sparingly.

References:
- [isolation-levels](references/isolation-levels.md)
- [deadlocks](references/deadlocks.md)
- [row-locking-gotchas](references/row-locking-gotchas.md)

## Operations
- Use online DDL (`ALGORITHM=INPLACE`) when possible; test on replicas first. On very large tables, use `gh-ost` or `pt-online-schema-change`.
- Tune connection pooling — avoid `max_connections` exhaustion under load.
- Monitor replication lag; avoid stale reads from replicas during writes.

References:
- [online-ddl](references/online-ddl.md)
- [connection-management](references/connection-management.md)
- [replication-lag](references/replication-lag.md)

## Confirm the version and engine first

```sql
select version(), @@default_storage_engine;
```

Every rule here assumes InnoDB. Several changed between 5.7, 8.0, and 8.4, so name the version an
answer assumes. Read [references/sources.md](references/sources.md) when auditing or changing a
factual claim, a version statement, a threshold, or an example.

## Guardrails
- Prefer measured evidence over blanket rules of thumb.
- Note MySQL-version-specific behavior when giving advice.
- Ask for explicit human approval before destructive data operations (drops/deletes/truncates).
