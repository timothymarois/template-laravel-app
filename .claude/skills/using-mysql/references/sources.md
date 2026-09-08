# MySQL source basis

Use this mapping to verify a rule before changing it. The MySQL Reference Manual establishes InnoDB
behavior and the syntax contracts; the release model establishes what "supported" means. Links are
to the 8.4 LTS manual and were opened and checked on 23 August 2026.

## Version scope

- [MySQL releases: Innovation and LTS](https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html)
  establishes the two release tracks: an LTS series that receives only bug fixes within the series,
  and Innovation releases that may add features and change behavior. On 23 August 2026 the current
  LTS was 8.4, with 8.4.11 the latest community server in that series.
- This package is written against 8.4 LTS and InnoDB. Confirm the server with
  `select version(), @@default_storage_engine` before applying anything below described as
  version-sensitive. The guardrail in `SKILL.md` requiring version-specific behavior to be named
  exists because several rules here changed between 5.7, 8.0, and 8.4.

## Schema design

- [Clustered and secondary indexes](https://dev.mysql.com/doc/refman/8.4/en/innodb-index-types.html)
  establishes that InnoDB stores the table in primary-key order and that every secondary index
  carries the primary-key columns. Both facts drive `primary-keys.md`: a wide primary key is copied
  into every secondary index, and a non-monotonic key such as a random UUID causes page splits and
  fragmentation on insert instead of appending.
- [Data type storage requirements](https://dev.mysql.com/doc/refman/8.4/en/storage-requirements.html)
  and [The DATE, DATETIME, and TIMESTAMP types](https://dev.mysql.com/doc/refman/8.4/en/datetime.html)
  establish the storage sizes and, critically for `data-types.md`, that `TIMESTAMP` covers only
  1970-01-01 to 2038-01-19 while `DATETIME` covers 1000 to 9999. That range limit — not style — is
  the reason `DATETIME` is the default recommendation.
- [The utf8mb4 character set](https://dev.mysql.com/doc/refman/8.4/en/charset-unicode-utf8mb4.html)
  and [Configuring application character set and collation](https://dev.mysql.com/doc/refman/8.4/en/charset-applications.html)
  establish that `utf8mb4` is the only Unicode set covering the full BMP and supplementary planes —
  emoji included — and that `utf8mb4_0900_ai_ci` is the default collation in 8.0 and later. They
  support `character-sets.md`, including that a mismatched collation across joined columns prevents
  index use.
- [The JSON data type](https://dev.mysql.com/doc/refman/8.4/en/json.html) establishes validated
  storage, the binary format, and that JSON columns cannot be indexed directly. `CREATE INDEX`'s
  [multi-valued index section](https://dev.mysql.com/doc/refman/8.4/en/create-index.html) establishes
  indexing through a generated column or a multi-valued index. Together they are the basis of
  `json-column-patterns.md`.

## Indexing

- [Multiple-column indexes](https://dev.mysql.com/doc/refman/8.4/en/multiple-column-indexes.html)
  establishes the leftmost-prefix rule: an index on `(a, b, c)` serves lookups on `a`, `(a, b)`, and
  `(a, b, c)` but not on `b` alone. It is the source of the composite-order rule in `SKILL.md` and
  `composite-indexes.md`, and of the rule in `query-optimization-pitfalls.md` that a range predicate
  stops the index being used for ordering on later columns.
- [Column indexes](https://dev.mysql.com/doc/refman/8.4/en/column-indexes.html) establishes prefix
  indexes on string columns and their length limits, supporting `covering-indexes.md` and the
  long-string guidance in `composite-indexes.md`.
- [Full-text search](https://dev.mysql.com/doc/refman/8.4/en/fulltext-search.html) establishes
  `FULLTEXT` indexes, natural-language and boolean modes, and the default minimum word length. It
  supports `fulltext-indexes.md`.
- [The sys schema `schema_unused_indexes` view](https://dev.mysql.com/doc/refman/8.4/en/sys-schema-unused-indexes.html)
  establishes the supported way to find indexes with no recorded reads, which is the mechanism
  behind the audit step in `index-maintenance.md` and `SKILL.md`. It reports only what
  Performance Schema has observed since the last restart — a limitation `index-maintenance.md` must
  keep, because an index used only by a monthly job looks unused for four weeks.

## Query optimization

- [EXPLAIN output format](https://dev.mysql.com/doc/refman/8.4/en/explain-output.html) establishes
  the `type` column values including `ALL`, and the `Extra` values `Using filesort` and
  `Using temporary`. It is the source of the red-flag list in `SKILL.md` and `explain-analysis.md`.
  The manual is explicit that `Using filesort` does not necessarily mean a disk file, which is why
  `explain-analysis.md` treats these as signals to investigate rather than as defects.
- [Obtaining execution plan information](https://dev.mysql.com/doc/refman/8.4/en/explain.html)
  establishes `EXPLAIN ANALYZE`, which executes the statement and reports actual timings and row
  counts. That it executes is the reason it must not be aimed at a write statement outside a
  transaction that is rolled back.
- [Optimizer hints](https://dev.mysql.com/doc/refman/8.4/en/optimizer-hints.html) establishes the
  supported hint syntax and scope. It backs the position in `query-optimization-pitfalls.md` that
  hints are a last resort after statistics and indexing have been addressed, because a hint freezes
  a decision the optimizer would otherwise revisit as data changes.
- [`LIMIT` optimization](https://dev.mysql.com/doc/refman/8.4/en/limit-optimization.html) establishes
  how the server handles `LIMIT` with `ORDER BY`, and is the basis for preferring cursor or keyset
  pagination over large `OFFSET` values in `query-optimization-pitfalls.md`.

## Transactions and locking

- [InnoDB locking](https://dev.mysql.com/doc/refman/8.4/en/innodb-locking.html) establishes shared
  and exclusive row locks, gap locks, next-key locks, insert intention locks, and `AUTO-INC` locks.
  It is the basis of `row-locking-gotchas.md`, including that a lock is taken on every row the scan
  examines, so an `UPDATE` without a usable index locks far more than it changes.
- [Transaction isolation levels](https://dev.mysql.com/doc/refman/8.4/en/innodb-transaction-isolation-levels.html)
  establishes `REPEATABLE READ` as InnoDB's default and that it uses next-key locking to prevent
  phantoms, while `READ COMMITTED` disables gap locking for most searches. This supports
  `isolation-levels.md` and the contention advice in `SKILL.md`.
- [Deadlocks in InnoDB](https://dev.mysql.com/doc/refman/8.4/en/innodb-deadlocks.html) and
  [Deadlock detection](https://dev.mysql.com/doc/refman/8.4/en/innodb-deadlock-detection.html)
  establish automatic detection, rollback of the transaction with the fewest changed rows, error
  1213, and the guidance to keep transactions small and access rows in a consistent order. They are
  the source of `deadlocks.md`, including the correction it already carries: `SERIALIZABLE` also
  uses gap and next-key locks, and `READ COMMITTED` reduces gap-lock deadlocks without eliminating
  deadlocks caused by inconsistent ordering or missing indexes.
- [AUTO_INCREMENT handling in InnoDB](https://dev.mysql.com/doc/refman/8.4/en/innodb-auto-increment-handling.html)
  establishes the three `innodb_autoinc_lock_mode` values, that interleaved mode (2) is the default
  in 8.0 and later, and the replication implications of each. `deadlocks.md` must keep the
  qualification that mode 2 is only safe for a workload that tolerates non-consecutive values.

## Partitioning

- [Partitioning keys, primary keys, and unique keys](https://dev.mysql.com/doc/refman/8.4/en/partitioning-limitations-partitioning-keys-unique-keys.html)
  establishes that every unique key, including the primary key, must include all columns of the
  partitioning expression. This is the hard constraint in `partitioning.md`, and the reason
  retrofitting partitioning onto an existing table is a rebuild rather than an `ALTER`.
- [Partitioning limitations](https://dev.mysql.com/doc/refman/8.4/en/partitioning-limitations.html)
  establishes the remaining restrictions verbatim: "Partitioned tables using the `InnoDB` storage
  engine do not support foreign keys." It also states that partitioned tables support neither
  `FULLTEXT` indexes nor spatial column types, and caps a table at 8192 partitions. These are
  limitations `partitioning.md` must preserve rather than soften: the foreign-key restriction rules
  partitioning out for many normalized schemas, and the `FULLTEXT` restriction means the technique
  cannot be combined with the search guidance in `fulltext-indexes.md` on the same table.

## Operations

- [Online DDL operations](https://dev.mysql.com/doc/refman/8.4/en/innodb-online-ddl-operations.html)
  establishes, per operation, whether it can run in place, whether it rebuilds the table, whether it
  permits concurrent DML, and whether it only changes metadata. It is the source of `online-ddl.md`
  and of the rule that `ALGORITHM=INPLACE, LOCK=NONE` should be stated explicitly so the server
  fails fast rather than silently choosing a blocking copy.
- [Too many connections](https://dev.mysql.com/doc/refman/8.4/en/too-many-connections.html)
  establishes `max_connections`, the extra connection reserved for a privileged account, and the
  per-connection memory cost. It supports `connection-management.md`.
- [Replication replica status](https://dev.mysql.com/doc/refman/8.4/en/replication-administration-status.html)
  establishes how replication delay is observed and that `Seconds_Behind_Source` measures the
  applied position, not the round trip. `replication-lag.md` keeps that distinction, since the
  metric can read zero while a replica is stalled on a long transaction.

## Catalog conclusions

These are this package's judgments, not claims made by the manual:

- The numeric thresholds — partition time-series tables above roughly 50 million rows, other tables
  above roughly 100 million, batch inserts of 500 to 5000 rows — are operational rules of thumb.
  MySQL publishes no such numbers, and they must be re-derived from the actual workload rather than
  applied as limits.
- The workflow order in `SKILL.md` and the preference for the smallest measurable change are catalog
  process, not database behavior.
- Requiring explicit human approval before a drop, delete, or truncate is a catalog safety rule.
- Recommending `gh-ost` or `pt-online-schema-change` for very large tables points at third-party
  tools outside the manual; verify their current compatibility with the server version before use.

Omitted on purpose: undated tuning posts, `my.cnf` values copied without the hardware and workload
they were measured on, advice that does not state which major version it applies to, and MyISAM-era
guidance presented as current InnoDB behavior.

## Attribution

The guidance adapted into this package originates with PlanetScale, published under the MIT License
and republished by Rundesk AI in the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, path `skills/mysql-patterns/`. The upstream package
carried its notice as an in-package `LICENSE.txt`, which this catalog's package contract does not
permit; the notice is therefore reproduced here in full:

```text
MIT License

Copyright (c) 2026 PlanetScale

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

Material modifications: renamed to `using-mysql`; per-file YAML metadata removed; the routing
description and workflow adapted to this catalog's package contract; this source map authored, as
the upstream package shipped no `references/sources.md`; and a maintainer validation record added.

## Attribution

This package adapts `skills/mysql-patterns/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, published by Rundesk AI under the MIT License.
