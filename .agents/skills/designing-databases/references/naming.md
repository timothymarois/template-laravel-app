# Naming schema objects

Read this when adding or renaming a table, column, key, index, constraint, or stored enum value.
Names in a schema outlive every screen built on them, and every rename is a migration. General
terminology — one canonical term across code, API and interface — is decided with the wider naming
guidance; this page covers the forms a schema needs.

## Follow the stack, then be consistent

Plurality, casing, key and timestamp forms are ecosystem conventions, and the ecosystems disagree.
Follow whichever one the stack already uses, and never mix two inside one schema.

| Element | Usual form | Where it comes from, and what it costs |
|---|---|---|
| Foreign key | `<singular_parent>_id` — `account_id` | Rails, Laravel, and Django all derive this automatically. A role prefix (`approved_by_user_id`) is right when one table links to the same parent twice, but it breaks that derivation and the relation must be configured by hand. |
| Primary key | `id` | The three frameworks mandate it; `sqlstyle.guide` argues for `account_id` instead. Pick the one your stack enforces. |
| Timestamps | `<past-tense verb>_at` — `paid_at`; `<event>_on` for a civil date | Rails and Laravel convention, from `created_at`/`updated_at`. `sqlstyle.guide` uses `_date`; there is no cross-ecosystem winner. |
| Booleans | `is_`/`has_`/`can_`, stated positively | Common default; follow the language's own predicate convention where it differs. |
| Identifier case | `snake_case`, unquoted | PostgreSQL folds unquoted names to lower case, so `NamesLikeThis` must be quoted everywhere, forever. |
| Table number | No consensus | Rails and Laravel pluralize, Django keeps the singular, `sqlstyle.guide` prefers a collective noun. Consistency within the schema is the only rule that holds. |

## Tables

| Don't | Do | Failure prevented |
|---|---|---|
| `tbl_invoice`, `invoice_tb` | The framework's established form | A storage prefix adds no domain meaning |
| `data`, `records`, `misc_settings` | The actual concept | Generic nouns make unrelated tables indistinguishable |
| `invoice_tag_map`, `link_table` | `invoice_tags`, or the relationship's own noun (`memberships`) once it has attributes | A junction named for its mechanism hides what the row is |

## Columns

Compose the name from the concept, its property, and a representation term so that `table.column`
reads as one fact about one row. Omit the concept when the table supplies it (`invoices.name`, not
`invoices.invoice_name`), but qualify a generic word so the property does not disappear:
`publication_status`, `sale_amount`. A bare `type` column is also a behaviour change in Rails, which
reads it as single-table inheritance.

| Don't | Do | Why |
|---|---|---|
| `reason_text`, `name_str`, `count_int` | `reason`, `name`, `count` | The type is in the schema and it changes |
| `col1`, `field_2`, `notes2` | The meaning, or a second column for the second meaning | A number in a name is a missing concept |
| `qty_rcv`, `dt_crt` | `quantity_received`, `created_at` | Non-domain abbreviations cost comprehension forever |
| `invoice_ids` holding one id | `invoice_id` | The name promises a cardinality the column does not have |
| `deleted` when the time matters | `deleted_at`, null while live | Records when, and is still an `IS NULL` test |
| `is_active` + `is_archived` + `is_draft` | One `publication_status` | Three booleans allow eight combinations, of which perhaps three are legal |
| `price` as a float, `timeout` | `price_cents` as an integer, `timeout_seconds` | A unit that lives only in a comment gets misread |
| `amount` with no currency | `amount_cents` plus `currency_code` | The same number means different money |
| `received_at` and `created_at` conflated | Both, when both facts exist | When the event happened and when the row was inserted are different facts |

## Keys, indexes, and constraints

Name constraints and indexes explicitly in migrations, so an error message, a lock report, or a
later `DROP` can refer to them without looking up a generated name.

| Object | Form | Example |
|---|---|---|
| Foreign key column | `<role_or_parent>_id` | `parent_invoice_id`, `created_by_id`, `approved_by_id` — never `user_id` and `user_id_2` |
| Primary key | `<table>_pkey` | `invoices_pkey` |
| Unique constraint | `<table>_<columns>_key` | `invoices_account_id_number_key` |
| Foreign key constraint | `<table>_<column>_fkey` | `invoices_account_id_fkey` |
| Check constraint | `<table>_<rule>_check` | `invoices_total_cents_check` |
| Index | `<table>_<columns>_idx` | `invoices_account_id_created_at_idx` |

These are PostgreSQL's generated forms; follow the stack's own pattern where it generates different
ones, such as Laravel's `invoices_account_id_foreign`. Keep identifiers under the engine's length limit
(63 bytes in PostgreSQL, 64 characters in MySQL) — PostgreSQL truncates a longer name with only a notice.

## Stored enum values

Stored values are `snake_case` (or `SCREAMING_SNAKE`, chosen once per schema) and name **states**:
`archived`, not `archive`. Never store integers whose meaning lives in code, presentation text such as
`Pending review` (a copy change becomes a data migration), or a value with a note appended
(`'Archived (see notes)'`).

## Renaming stored data

A stored or published name is a contract. Before renaming, list the full fanout — column, indexes,
constraints, views, triggers, API fields, reports, exports, and saved filters — then stage it: add the
new name, write both, backfill, move readers, then drop the old one. The sequence is in
[advanced-patterns.md](advanced-patterns.md#renaming).
