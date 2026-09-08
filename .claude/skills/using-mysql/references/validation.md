# Using MySQL Validation

This is the current validation record for `using-mysql`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for MySQL and InnoDB behavior — engine-specific types and character sets,
keys and indexes, execution plans, row locking and isolation, deadlocks, online DDL, partitioning,
replication lag, and connection pressure. It should not activate for PostgreSQL, SQLite, or
engine-independent data modelling.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| MYS-T01 | Add an index to a MySQL table and confirm the plan uses it | Load |
| MYS-T02 | "Orders time out under load and the log is full of error 1213" | Load |
| MYS-T03 | Tune a PostgreSQL query plan | Do not load; `using-postgres` owns it |
| MYS-T04 | Decide whether an entity needs a join table at all | Do not load; that is engine-independent modelling |
| MYS-T05 | Choose an ORM in a repository whose database happens to be MySQL | Do not load; no MySQL behavior is at stake |
| MYS-T06 | Add a column to a 200-million-row table without downtime | Load |
| MYS-T07 | Laravel application whose slow endpoint needs both an Eloquent and an index fix | Compose with `using-laravel`; each owns its half of the proof |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| MYS-W01 | A UUID is proposed as the clustered primary key | Explain page splits and the copy into every secondary index; keep the UUID as a secondary unique column |
| MYS-W02 | An `UPDATE` locking far more rows than it changes | Identify the missing index on the `WHERE` column and the scan-time locking, not the isolation level alone |
| MYS-W03 | Deadlocks blamed on isolation level | Correct it: consistent access order and indexing are the fix; `READ COMMITTED` reduces gap-lock deadlocks without eliminating deadlocks, and `SERIALIZABLE` still uses gap locks |
| MYS-W04 | A `TIMESTAMP` column proposed for a future-dated contract | Cite the 2038 limit and choose `DATETIME`; a version-independent range fact, not a style preference |
| MYS-W05 | Partitioning proposed for a table with foreign keys | Refuse on the documented limitation: partitioned InnoDB tables do not support foreign keys |
| MYS-W06 | "I added the index and it's much faster" | Reject fluent assurance; require `EXPLAIN` before and after and a stated timing on a realistic row count |
| MYS-W07 | Server version cannot be determined | Run the version query or stop and name the unknown; do not apply behavior that changed across 5.7, 8.0, and 8.4 |
| MYS-W08 | An unused-index audit proposes dropping an index | Keep the Performance Schema limitation: counters reset on restart, so an index used by a periodic job can look unused |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. Cases marked for the sampled run are MYS-T01, MYS-T03, MYS-W06, MYS-T05,
  and MYS-T07.
- Codex: not run.

No case below is marked passed. Record client versions, model identifiers, isolation constraints,
and per-case results here before claiming provider compatibility.

## Limits

No case runs against a live MySQL server; workflow cases are graded on the decision, the cited
mechanism, and the proof demanded. MYS-T05 is the shared-repository exclusion case.
