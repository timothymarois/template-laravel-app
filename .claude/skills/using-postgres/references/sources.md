# Postgres source basis

Use this mapping to verify a rule before changing it. The PostgreSQL manual establishes the
contracts and the planner behavior; the version policy establishes what "supported" means. Every
link below was opened and checked on 23 August 2026.

## Version scope

- [Versioning policy](https://www.postgresql.org/support/versioning/) establishes the five-year
  support window for each major and the annual major release. On 23 August 2026 the supported
  majors were 14 through 18, with 14 reaching end of life on 12 November 2026 and 19 in beta.
  `https://www.postgresql.org/docs/current/` therefore resolves to 18.
- This package targets currently supported majors. Confirm the server's own version with
  `select version()` before applying any rule described below as version-sensitive; the planner and
  the available index capabilities change between majors, which is why the guardrail in `SKILL.md`
  requires stating the version an answer assumes.

## Query performance

- [Index types](https://www.postgresql.org/docs/current/indexes-types.html) establishes B-tree,
  Hash, GiST, SP-GiST, GIN, BRIN, and the operators each supports. It is the basis of
  `query-index-types.md`, including the rule that a non-B-tree index only helps when the query uses
  an operator that index class implements.
- [Multicolumn indexes](https://www.postgresql.org/docs/current/indexes-multicolumn.html)
  establishes that a multicolumn B-tree is most efficient with equality constraints on the leading
  columns and that scanning is less efficient once a leading column is only range-constrained. It
  supports the equality-then-range column order in `query-composite-indexes.md` and `SKILL.md`.
- [Partial indexes](https://www.postgresql.org/docs/current/indexes-partial.html) establishes
  indexing a subset via a predicate, and that the planner uses one only when it can prove the query
  implies the index predicate. It supports `query-partial-indexes.md`.
- [Index-only scans and covering indexes](https://www.postgresql.org/docs/current/indexes-index-only-scans.html)
  establishes `INCLUDE` columns, and — importantly for the caveat in
  `query-covering-indexes.md` — that an index-only scan still consults the visibility map, so it
  degrades when a table is not adequately vacuumed.
- [Using EXPLAIN](https://www.postgresql.org/docs/current/using-explain.html) establishes that
  `EXPLAIN` estimates while `EXPLAIN ANALYZE` executes, the meaning of the cost and row estimates,
  and that `BUFFERS` reports actual I/O. It is the basis of `query-missing-indexes.md` and
  `monitor-explain-analyze.md`, including the warning that `ANALYZE` runs the statement and so must
  not be pointed at a write statement outside a rolled-back transaction.

## Connection management

- [Resource consumption](https://www.postgresql.org/docs/current/runtime-config-resource.html)
  establishes that `work_mem` is allocated per sort or hash operation — several per query, and per
  concurrent connection — which is why `conn-limits.md` derives `max_connections` from memory rather
  than from peak client count.
- [Connections and authentication](https://www.postgresql.org/docs/current/runtime-config-connection.html)
  establishes `max_connections`, `superuser_reserved_connections`, and `idle_session_timeout`,
  supporting `conn-limits.md` and `conn-idle-timeout.md`.
- [`PREPARE`](https://www.postgresql.org/docs/current/sql-prepare.html) establishes that a prepared
  statement lives for the duration of the session and that generic plans may be chosen after
  repeated executions. This is the mechanism behind the transaction-pooling incompatibility in
  `conn-prepared-statements.md`: a pooler that reassigns a server connection between statements
  breaks anything holding session state.

## Security and row-level security

- [Row security policies](https://www.postgresql.org/docs/current/ddl-rowsecurity.html) establishes
  that `ENABLE ROW LEVEL SECURITY` denies by default until a policy exists, that table owners and
  `BYPASSRLS` roles bypass policies unless `FORCE ROW LEVEL SECURITY` is set, and that `USING`
  filters visible rows while `WITH CHECK` constrains new ones. It is the basis of
  `security-rls-basics.md`, including the owner-bypass trap that makes a policy look ineffective in
  testing.
- The same page establishes that policy expressions are evaluated per row. That is why
  `security-rls-performance.md` wraps a function call as `(select current_setting(...))` so the
  planner evaluates it once per statement as an InitPlan instead of once per row.
- [`GRANT`](https://www.postgresql.org/docs/current/sql-grant.html) and
  [`ALTER DEFAULT PRIVILEGES`](https://www.postgresql.org/docs/current/sql-alterdefaultprivileges.html)
  establish the privilege model and the defaults `PUBLIC` holds — notably `CONNECT` and `TEMPORARY`
  on databases and `EXECUTE` on functions — supporting the revoke-the-defaults rule in
  `security-privileges.md`.

## Schema design

- [Data types](https://www.postgresql.org/docs/current/datatype.html) establishes the numeric,
  character, date/time, and `identity` behavior behind `schema-data-types.md` and
  `schema-primary-keys.md`, including that `varchar(n)` and `text` have no performance difference in
  Postgres and that `numeric` is exact but slower than the float types.
- [Constraints](https://www.postgresql.org/docs/current/ddl-constraints.html) establishes check,
  not-null, unique, primary key, foreign key, and exclusion constraints, and the referential
  actions. It supports `schema-constraints.md`.
- The same page states that Postgres creates an index for a unique or primary key constraint but
  does **not** create one on the referencing side of a foreign key. That is the direct source of
  `schema-foreign-key-indexes.md` and the "Postgres does not do it for you" line in `SKILL.md`;
  without that index, a delete or update of the referenced row scans the child table.
- [Lexical structure](https://www.postgresql.org/docs/current/sql-syntax-lexical.html) establishes
  that unquoted identifiers fold to lower case while double-quoted identifiers preserve case and
  must then always be quoted. It is the basis of `schema-lowercase-identifiers.md`.
- [Table partitioning](https://www.postgresql.org/docs/current/ddl-partitioning.html) establishes
  range, list, and hash partitioning, partition pruning, and the requirement that a unique
  constraint on a partitioned table include the partition key. It supports `schema-partitioning.md`.

## Concurrency and locking

- [Explicit locking](https://www.postgresql.org/docs/current/explicit-locking.html) establishes the
  table- and row-level lock modes and their conflicts, and that deadlocks are detected and one
  transaction is aborted. It supports `lock-short-transactions.md` and
  `lock-deadlock-prevention.md`, including the rule that consistent access order is the reliable
  prevention.
- The advisory-lock section of that page establishes session- and transaction-scoped advisory locks
  and that session-scoped locks must be released explicitly — the leak `lock-advisory.md` warns
  about, which is also why transaction-scoped locks are the safer default under a pooler.
- [`SELECT`](https://www.postgresql.org/docs/current/sql-select.html) establishes
  `FOR UPDATE SKIP LOCKED` and `NOWAIT`, and states that `SKIP LOCKED` returns an inconsistent view
  by design. That trade-off is exactly what makes it correct for a work queue and wrong for a
  report, as `lock-skip-locked.md` says.
- [Transaction isolation](https://www.postgresql.org/docs/current/transaction-iso.html) establishes
  read committed as the default, and that repeatable read and serializable can abort with a
  serialization failure that the application must be prepared to retry.

## Data access patterns

- [`INSERT`](https://www.postgresql.org/docs/current/sql-insert.html) establishes
  `ON CONFLICT ... DO UPDATE`, that it requires a unique index or constraint to infer the conflict
  target, and that `excluded` exposes the proposed row. It supports `data-upsert.md`.
- [`COPY`](https://www.postgresql.org/docs/current/sql-copy.html) establishes bulk loading and is
  the basis of the bulk path in `data-batch-inserts.md`.
- [Queries: `LIMIT` and `OFFSET`](https://www.postgresql.org/docs/current/queries-limit.html) states
  that rows skipped by `OFFSET` still have to be computed, which is the cost keyset pagination
  avoids in `data-pagination.md`.

## Monitoring and diagnostics

- [`pg_stat_statements`](https://www.postgresql.org/docs/current/pgstatstatements.html) establishes
  normalized statement statistics, that the extension must be loaded via
  `shared_preload_libraries`, and the meaning of total and mean execution time. It supports
  `monitor-pg-stat-statements.md`, including the point that the highest total time — not the
  slowest single call — identifies what is worth optimizing.
- [Routine vacuuming](https://www.postgresql.org/docs/current/routine-vacuuming.html) establishes
  that `UPDATE` and `DELETE` leave dead tuples, that vacuum reclaims them and updates the visibility
  map, that `ANALYZE` maintains planner statistics, and that transaction-ID wraparound must be
  prevented. It is the basis of `monitor-vacuum-analyze.md` and of the `SKILL.md` claim that
  autovacuum keeping up is a precondition for the other rules.
- [The statistics collector](https://www.postgresql.org/docs/current/monitoring-stats.html)
  establishes `pg_stat_user_tables` and `pg_stat_user_indexes`, the source of the dead-tuple and
  unused-index checks.

## Advanced features

- [Full text search](https://www.postgresql.org/docs/current/textsearch.html) establishes
  `tsvector`, `tsquery`, text-search configurations, and that a GIN index on a stored or generated
  `tsvector` column avoids recomputing it per query. It supports `advanced-full-text-search.md`.
- [JSON types](https://www.postgresql.org/docs/current/datatype-json.html) establishes the
  `json`/`jsonb` distinction and the GIN operator classes, including `jsonb_path_ops` being smaller
  and faster for containment at the cost of supporting fewer operators. It supports
  `advanced-jsonb-indexing.md`.

## Migrations and DDL safety

- [`ALTER TABLE`](https://www.postgresql.org/docs/current/sql-altertable.html) establishes which
  forms require `ACCESS EXCLUSIVE`, that adding a column with a non-volatile default no longer
  rewrites the table, and that adding a constraint `NOT VALID` then validating it separately avoids
  a long blocking scan.
- [`CREATE INDEX`](https://www.postgresql.org/docs/current/sql-createindex.html) establishes
  `CONCURRENTLY`, that it cannot run inside a transaction block, and that a failed concurrent build
  leaves an invalid index that must be dropped. This is the source of the guardrail in `SKILL.md`
  requiring approval for a non-concurrent index build on a live table.

## Catalog conclusions

These are this package's judgments, not claims made by the manual:

- The eight-category priority ordering in `SKILL.md` and `_sections.md`, with query performance and
  connection management rated CRITICAL, is a local operational ranking. PostgreSQL publishes no such
  ranking.
- Requiring measured before-and-after evidence, and treating a rule that does not appear in the plan
  or timings as not having applied, is a local evidence standard.
- Requiring explicit human approval before a destructive or blocking operation is a catalog safety
  rule, not a database behavior.

Omitted on purpose: undated tuning blog posts, configuration numbers copied without the hardware and
workload they were measured on, and advice that does not name the major version it applies to. The
[PostgreSQL wiki's performance optimization page](https://wiki.postgresql.org/wiki/Performance_Optimization)
is retained as community orientation only; it is not treated as normative where it and the manual
differ.

## Attribution

This package adapts `skills/postgres-patterns/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, published by Rundesk AI under the MIT License.

Material modifications: renamed to `using-postgres`; per-file YAML metadata removed; the routing
description adapted to this catalog's package contract; this source map authored, as the upstream
package shipped no `references/sources.md` and its `SKILL.md` carried only two bare links, which are
preserved above; and a maintainer validation record added.
