# Designing Databases Validation

This is the current validation record for `designing-databases`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for engine-independent relational modelling — normalization, identity and
keys, relationships, constraints, history and deletion, concurrency control, pagination shape, and
choosing a shape against the access paths and growth it must serve. It should not activate for
engine-specific SQL, index tuning, or database operations.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| DBD-T01 | Model orders, customers, and line items for a new application | Load |
| DBD-T02 | "We keep tags in a comma-separated column and can't search them" | Load |
| DBD-T03 | Tune a PostgreSQL query plan on an existing schema | Do not load; `using-postgres` owns it |
| DBD-T04 | Fix a MySQL deadlock | Do not load; `using-mysql` owns it |
| DBD-T05 | Rename a UI label backed by a database | Do not load |
| DBD-T06 | Add an audit trail and soft deletes to an existing model | Load |
| DBD-T07 | A new model whose shape has an index consequence on the engine in use | Compose with the matching engine package; this one names the consequence, that one answers it |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| DBD-W01 | A wide table carrying several entities, mostly null | Name under-normalization as the common failure and split by dependency, not by preference |
| DBD-W02 | A denormalized counter proposed for speed | Require a measurement first, and make the derivation explicit in the schema rather than implied |
| DBD-W03 | A column named `status`, `data`, or `type` | Apply the naming rule: name the value, qualify the generic term, and note that a bare `type` changes framework behavior |
| DBD-W04 | A random UUID proposed as the primary key | State the model-wide consequence for referencing structures and insert ordering, and offer the separate external identifier |
| DBD-W05 | Polymorphic association proposed | Give the trade-off honestly: no foreign key, integrity moves to application code |
| DBD-W06 | "The schema is fine, it looks normalized" | Reject fluent assurance; require the access paths and a plan read at representative volume |
| DBD-W07 | The target engine is unknown | Keep the model engine-independent and name the decisions that will depend on the engine, rather than assuming one |
| DBD-W08 | Partitioning likely later | Surface that the partition key constrains the primary key from the first migration |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. No case from this package was included in the sampled run performed for the
  ten technology packages, because this package was added afterwards.
- Codex: not run.

No case below is marked passed.

## Limits

DDL in this package's references is written in SQLite dialect for portability of shape, not of
spelling. Cases are graded on the model decision, not on dialect correctness.
