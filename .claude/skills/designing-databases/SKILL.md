---
name: designing-databases
description: Use before writing or changing a migration, adding or altering a table or column, choosing which index a query needs, or naming a table, column, key or index — and when shaping or reviewing a relational data model before or across engine choice, including normalization, identity and keys, relationships, constraints, many-to-many, parent-child, polymorphic, hierarchical and temporal data, audit trails, soft deletes, optimistic locking, keyset pagination, whether data belongs in columns, JSON, or separate tables, and choosing a shape against the queries and growth it must survive. Load the engine package alongside it for anything the engine decides: identifier length caps, index tuning, engine-specific SQL, and database operations.
---

# Design databases

Modelling decisions that are true before an engine is chosen: what the tables are, what identifies a
row, which relationships exist, and how time and deletion are represented. These outlive the
engine — a schema you regret survives every migration you run on it, because the application is
written against its shape.

## Scope

This skill owns the model. It does not own engine tuning, and does not restate it:

| Question | Skill that owns it |
|---|---|
| PostgreSQL indexes, planner behaviour, RLS, pooling, `EXPLAIN` | `using-postgres` |
| MySQL/InnoDB indexes, locking, online DDL, replication, connection limits | `using-mysql` |
| SQLite PRAGMAs, WAL, FTS5, table rebuilds, the single-file hazards | `using-sqlite` |

When a modelling decision below has an index or a locking consequence, this skill names the
consequence and points you at whichever of those three you are running. What index type serves it,
and what the planner does with it, is their answer, not this one's — and where that package is not
installed, the consequence still stands and belongs in the engine's own documentation. This package
is complete on its own; none of the three is a dependency.

## How to Use

Read the reference for the pattern at hand — not all of them, and not all of any one.

| Read | When |
|---|---|
| [references/advanced-patterns.md](references/advanced-patterns.md) | Polymorphic associations and inheritance, hierarchies (adjacency list, materialized path, closure table), temporal and bitemporal history, JSON columns, denormalization and summary tables, keyset pagination, and the shape of a safe schema change |
| [references/security-examples.md](references/security-examples.md) | Storing credentials or PII, multi-tenant isolation, roles and permissions tables, audit logs, immutable records, optimistic locking, retention and anonymization, and constraints that encode a business rule |
| [references/naming.md](references/naming.md) | Naming tables, columns, booleans, timestamps, foreign keys, enum values, and units; what absence means; keeping one canonical term across the column, the API field, and the label; and renaming stored data safely |
| [references/design-for-performance.md](references/design-for-performance.md) | Choosing a shape against the queries it must serve: access paths, key and cardinality choices, growth, what makes a shape indexable at all, read/write trade-offs, and when a measurement — not a prediction — justifies denormalizing |

Read [references/sources.md](references/sources.md) when auditing or changing a naming lesson in this
package; it maps that section's rules to the standards, style guides, framework conventions, and
studies behind them, and records where those conflict.

Both reference files write their DDL in SQLite dialect, because it is the smallest and every engine
reads it. The *shapes* are portable; the spellings are not — `datetime('now')` is `now()` in
PostgreSQL and `NOW()` in MySQL, `TEXT` timestamps are `timestamptz` and `DATETIME`, and
`INTEGER PRIMARY KEY` is `bigserial`/`BIGINT AUTO_INCREMENT`. Translate through the engine skill you
are using.

## Normalization

- **Start at 3NF.** Every non-key column depends on the key, the whole key, and nothing but the key.
  Anything else is a claim you will have to keep true by hand.
- **Denormalize from a measurement, not a prediction.** A duplicated column is a second source of
  truth, and the trigger or job that keeps it current is code that can be wrong. Pay that only when
  a profile shows the join is the problem.
- **A repeating group is a table.** `tag1, tag2, tag3` and `tags TEXT -- "a,b,c"` are the same
  mistake: you cannot index them, constrain them, or join through them.
- **Under-normalizing is the common failure, not over-normalizing.** The usual real symptom is one
  wide table carrying several entities' worth of columns, most of them null for most rows.

## Naming

Naming is part of the design, not a pass afterwards: a column you cannot name is usually one you
have not finished modelling. The rules below are the ones that change the model itself. The full
dos and don'ts for tables, columns, booleans, timestamps, foreign keys, enums, units, absence, and
safe renames are in [references/naming.md](references/naming.md) — read it whenever you add or
rename anything stored.

A name designates a value; a sentence defines it. `how_an_invoice_is_paid` is a definition sitting in
the name's slot, and it leaves the values it gestures at — an account, a method, a timestamp — unnamed
and usually unmodelled.

- **Name the value, not the question it answers.** Compose the name from the entity, its property,
  and a representation term, in that order: `payment_method`, `paid_at`, `payer_account_id`.
  Interrogatives (`who`, `why`, `when`, `how`) and connecting words (`a`, `the`, `of`, `is`) belong to
  no name. Each maps to a value instead: the actor to `payer_account_id`, the event time to
  `paid_at`, the reason to `failure_reason_id`, the mechanism to `payment_method`.
- **A generic word is a suffix, never a whole name.** `publication_status` and `sale_amount` are
  well-formed, because the trailing term says how the value is represented. Bare `status`, `value`,
  `state`, or `type` name nothing a reader can act on. `data`, `info`, `details`, `meta`, and `thing`
  do not even work as suffixes — `product_info` is a `product`, `name_string` is a `name`.
- **A bare `type` column is a behaviour change, not just a vague one.** A column named `type` reads
  as a class discriminator — the convention ORMs use for polymorphic and inherited models, Laravel's
  `<relation>_type` among them — not as a domain attribute. Qualify it — `payment_method_type` — and
  the ambiguity goes with it.
- **Take the words from the domain's own vocabulary.** The business says "buyer" and "account"; a
  schema that says `party` or `entity` has invented a superordinate nobody speaks, so every
  conversation about it now carries a translation step. When the domain's word changes, rename the
  model to match rather than keeping a synonym alive in the schema.
- **Model the connection; do not describe it.** If "how an invoice is paid" is a real fact, it is a
  row — `invoice_payments(invoice_id, payer_account_id, payment_method, paid_at)` — and the foreign
  key carries the connection. A column holding prose about the payment cannot be joined, constrained,
  or aggregated.
- **Read `table.column` aloud as a fact about one row.** `invoice_payments.payer_account_id` is one.
  If stating what the column holds takes a sentence, either the name is wrong or the column is on the
  wrong table.

The conventions below are ecosystem conventions, not universals, and they disagree with each other.
Follow whichever one the stack already uses, and never mix two inside one schema:

| Element | Usual form | Where it comes from, and what it costs |
|---|---|---|
| Foreign key | `<singular_parent>_id` — `account_id` | Rails, Laravel, and Django all derive this automatically. A role prefix (`approved_by_user_id`) is the right call when one table links to the same parent twice, but it breaks that derivation and needs the relation configured by hand. |
| Primary key | `id` | The three frameworks mandate it; `sqlstyle.guide` argues the opposite, that a bare `id` should be avoided in favour of `account_id`. Pick the one your stack enforces. |
| Timestamps | `<past-tense verb>_at` — `paid_at` | Rails and Laravel convention, from `created_at`/`updated_at`. `sqlstyle.guide` uses `_date` instead; there is no cross-ecosystem winner. |
| Booleans | `is_`/`has_` in most languages | Documented as a prefix by typescript-eslint; PHP and SQL carry no equivalent rule. Follow the language, not this table. |
| Identifier case | `snake_case`, unquoted | PostgreSQL's own "Don't Do This" page: `NamesLikeThis` has to be quoted everywhere, forever. |
| Table number | No consensus | Rails and Laravel pluralize, Django and WIPO ST.96 keep the singular, `sqlstyle.guide` prefers a collective noun (`staff` over `employees`). Consistency within the schema is the only rule that holds. |

**No database linter checks any of this.** SQLFluff, schemalint, and Squawk cover casing, keyword
collisions, and migration safety; meaning is left to review. If a mechanical guard is wanted, the
precedent is a denylist — ESLint's `id-denylist`, Pylint's `bad-names` — and it exists in no SQL
tool, so a name that survives the linter has not been checked.

## Keys and Identity

- **Every table has a primary key.** A table without one has no way to name a row, so no update or
  delete can be trusted to hit exactly one.
- **Separate the surrogate key from the business key.** The surrogate identifies the row forever;
  the business key (an email, an SKU, an invoice number) is what people mean, and it changes. Give
  the business key a `UNIQUE` constraint and never make it the target of a foreign key.
- **Prefer narrow, monotonic surrogates.** Every foreign key and index carries a copy of it. Random
  UUIDs as the clustered key cost write locality on MySQL/InnoDB and PostgreSQL both — see those
  skills for the mechanics, and keep the UUID in a unique column if an external identifier is
  required.
- **A composite primary key is right for a pure junction table** — `(document_id, tag_id)` — and
  wrong once that relationship acquires attributes of its own.
- **Null means "unknown", not "none" and not "zero".** A nullable column with three meanings is
  three columns, or an enum, in disguise.

## Relationships

- **One-to-many** is a foreign key on the many side. Choose the `ON DELETE` action deliberately:
  `CASCADE` when the child cannot exist alone, `RESTRICT` when the delete should be refused,
  `SET NULL` when the link is optional. The default — no action — is the one nobody chose.
- **Many-to-many** is a junction table, always. Give it its own columns when the relationship has
  attributes (a role, a position, a joined-at), and a surrogate key at that point.
- **One-to-one** is usually one table. It earns a second when the columns have a different lifetime,
  a different access pattern, or a different sensitivity — a credential row apart from a profile row.
- **Self-referential** relationships are hierarchies, and how you store them decides which queries
  are cheap. Adjacency list, materialized path, and closure table are in
  [references/advanced-patterns.md](references/advanced-patterns.md).
- **Index the child side of every foreign key.** No engine here does it for you on the referencing
  column, and an unindexed one turns a parent delete into a scan. The index syntax and what the
  planner does with it belong to `using-postgres`, `using-mysql`, or `using-sqlite`.

## Constraints

Constraints are the last line, and the only line that holds when a second application, a migration
script, or a person with a SQL prompt writes to the table.

- `NOT NULL` on everything that is genuinely required. Retrofitting it means backfilling.
- `UNIQUE` on every business key, including the composite ones.
- `CHECK` for enumerations, ranges, and cross-column rules — an `end_date >= start_date` is one line
  and removes a class of bug permanently.
- `FOREIGN KEY` wherever a column holds another table's key, even when the application "always" sets
  it correctly.

A constraint that only exists in application code is a convention. Conventions do not survive the
second writer.

## Polymorphism and Inheritance

One relationship, several possible target types. Three shapes, and the choice is about where the
nulls and the integrity go:

- **Single table** — every type in one table with type-specific nullable columns. Simplest, but the
  table fills with nulls and no constraint stops an email-only column from being set on an SMS row
  unless you write it.
- **Class table** — a base table plus one table per type. Every column is meaningful and every
  foreign key is real; reading a full object needs a join.
- **Separate nullable foreign keys** with a `CHECK` that exactly one is set — real referential
  integrity, and it stops scaling around the fourth or fifth type.

Worked out, with the constraints and partial indexes each needs, in
[references/advanced-patterns.md](references/advanced-patterns.md).

## History and Time

- **A row that is updated in place has no history.** If anyone will ask what the price was in March,
  the current value is not enough, and reconstructing it later is not possible.
- **Versioned rows (SCD Type 2)** carry `valid_from`/`valid_to` with the current version open-ended.
  Enforce "one current version" with a partial unique index on the business key where the row is
  open — a plain `UNIQUE(business_key, valid_to)` does **not** do it, because nulls do not collide.
- **Bitemporal** separates when something was true from when you recorded it. Real for finance,
  contracts, and anything with retroactive corrections; overkill everywhere else.
- **An audit log answers "who changed this", which is a different question** from "what did it say
  then". A log of changes is not a version history, and a version history has no actor. Decide which
  one you actually need; build both only if both get read.

## Deletion

- **Soft delete is a state, so model it as one.** A `deleted_at` timestamp beats a boolean: it
  records when, and it is still a clean `IS NULL` test.
- **Every query then has to remember.** Give readers a view that filters, and index for the
  filtered set — a partial or filtered index over the live rows, on whichever engine you are on.
- **Soft-deleted rows still occupy their unique constraints.** A retired user keeps their email
  address, and the next signup with it fails. Decide up front: release the value on delete, or
  include the delete marker in the uniqueness.
- **Erasure requests need real deletion or anonymization**, not a flag. Anonymizing in place
  preserves referential integrity where a hard delete would break it.

## Concurrency in the Model

- **Optimistic locking is a `version` column**, incremented on every write, checked in the `WHERE`
  clause. Zero rows affected means someone else got there first, and the application decides what to
  do about it. This is the portable answer and it costs one integer.
- Pessimistic locking — `SELECT ... FOR UPDATE`, isolation levels, deadlock ordering — is engine
  behaviour. See `using-postgres` or `using-mysql`.

## Pagination

- **Keyset pagination, not `OFFSET`,** for any list that can grow. `OFFSET 10000` makes the engine
  produce and discard ten thousand rows on every page.
- **The sort key must be total.** Order by the timestamp *and* the primary key, and carry both in
  the cursor — ties on a non-unique sort column skip and repeat rows across pages.
- **The model owes the index its shape.** Design the sort so a single index can serve it; see
  `using-postgres`, `using-mysql`, or `using-sqlite` for how to declare it.

## Common Mistakes

| Mistake | Instead |
|---|---|
| No primary key | Surrogate key on every table |
| A list in a column: `tags TEXT` = `"a,b,c"` | Junction table |
| Foreign key column with no constraint | `REFERENCES` with a chosen `ON DELETE` |
| Business key as the foreign key target | Surrogate as the target, `UNIQUE` on the business key |
| `is_deleted BOOLEAN` | `deleted_at` timestamp |
| `UNIQUE(key, valid_to)` for "one current row" | Partial unique index where `valid_to IS NULL` |
| Storing a money amount as a float | Integer minor units, or the engine's exact decimal type |
| Local timestamps with no zone | UTC, in the engine's timestamp-with-zone type |
| Denormalizing before measuring | Normalize, measure, then denormalize with a maintainer |
| "The table has 10M rows, so it scans" | Row count is not the cause; the missing or unusable index is |

## Guardrails

- Design from the queries. A schema nobody has written a query against is a guess.
- Say which normal form you left and why, whenever you leave one.
- Schema changes on a live table are engine-specific and are the engine skill's territory:
  `using-mysql` for online DDL, `using-sqlite` for the table rebuild procedure,
  `using-postgres` for lock-taking DDL.
- Get explicit human approval before any migration that drops a column, drops a table, or rewrites
  data — and before running one without a restore you have tested.
