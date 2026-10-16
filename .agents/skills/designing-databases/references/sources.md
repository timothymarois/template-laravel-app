# Database-design source basis

Read this to audit or revise a naming convention or a design-for-performance rule; it is not another
modelling procedure.

## Conventions, and where they conflict

These are ecosystem conventions because the sources disagree; `naming.md` reports them as such.

- [`sqlstyle.guide`, Simon Holywell (CC BY-SA 4.0, no version or date printed)](https://www.sqlstyle.guide/) —
  publishes the uniform-suffix list behind the "generic word is a suffix" rule: `_id`,
  `_status` ("flag value or some other status of any type such as `publication_status`"), `_total`,
  `_num`, `_name`, `_seq`, `_date`, `_tally`, `_size`, `_addr`. It also requires singular column
  names, prefers a collective noun to a plural table name (`staff` over `employees`), and — against
  all three frameworks below — says "where possible avoid simply using `id` as the primary
  identifier for the table". Its timestamp suffix is `_date`, not `_at`.
- [Rails, *Active Record Basics*](https://edgeguides.rubyonrails.org/active_record_basics.html),
  [Laravel 13.x, *Eloquent: Relationships*](https://laravel.com/docs/13.x/eloquent-relationships), and
  [Django 5.2, `ForeignKey`](https://docs.djangoproject.com/en/5.2/ref/models/fields/#django.db.models.ForeignKey) —
  all three derive a foreign-key column as the singular parent name plus `_id` (`order_id`,
  `post_id`, `manufacturer_id`) and all three assume a bare `id` primary key. This is what a role
  prefix such as `approved_by_user_id` costs: the derivation no longer applies and the relation must
  be configured explicitly. Rails and Laravel supply the `created_at`/`updated_at` timestamps behind the
  `_at` convention, and Rails' reserved-column list is the source of the concrete failure behind the
  `type` rule — Rails reads a `type` column as single-table inheritance. Rails and Laravel
  pluralize table names while Django's is `app_model`, singular; there is no consensus to report.
- [PostgreSQL Wiki, "Don't Do This"](https://wiki.postgresql.org/wiki/Don%27t_Do_This) — the
  project's own list: "Don't use upper case table or column names … Don't use `NamesLikeThis`, use
  `names_like_this`", because the mixed-case identifier must then be quoted everywhere.
- [RuboCop `Naming/PredicatePrefix`](https://docs.rubocop.org/rubocop/latest/cops_naming.html) and the
  [Ruby Style Guide](https://rubystyle.guide/) versus
  [`@typescript-eslint/naming-convention`](https://typescript-eslint.io/rules/naming-convention/) —
  a direct contradiction, and the reason the boolean form follows the language. Ruby
  forbids the `is_`/`has_`/`have_` prefixes as "redundant and inconsistent with the style of boolean
  methods in the Ruby core library" (`def tall?`, not `def is_tall?`); typescript-eslint documents
  `prefix: ["is", "should", "has", "can"]` as an ordinary configuration.

## What no tool enforces

- [SQLFluff rules reference](https://docs.sqlfluff.com/en/stable/reference/rules.html) — the
  naming-adjacent rules are capitalisation (CP01–CP05), references (RF01–RF06, including "Keywords
  should not be used as identifiers" and "Do not use special characters in identifiers"), and alias
  length (AL06). **Nothing judges whether an identifier means anything.**
- [schemalint](https://github.com/kristiandupont/schemalint) ships `nameCasing`, `nameInflection`,
  and `requirePrimaryKey`; [Squawk](https://squawkhq.com/docs/rules) covers migration safety only.
  No located SQL linter enforces semantic naming, so a name passing
  the linter has not been checked.

## Designing for performance

The rules in `design-for-performance.md` are modelling consequences of documented engine behavior.

- [PostgreSQL: multicolumn indexes](https://www.postgresql.org/docs/current/indexes-multicolumn.html)
  and [MySQL: multiple-column indexes](https://dev.mysql.com/doc/refman/8.4/en/multiple-column-indexes.html)
  establish leftmost-prefix behavior and that a leading range constraint limits what the remaining
  columns can do. They support ordering a composite key by its access path rather than by how the
  columns read.
- [MySQL: clustered and secondary indexes](https://dev.mysql.com/doc/refman/8.4/en/innodb-index-types.html)
  establishes that the table is stored in primary-key order and that every secondary index carries
  the primary key. This is the documented basis for treating key width and key ordering as
  model-wide decisions rather than table-local ones.
- [PostgreSQL: table partitioning](https://www.postgresql.org/docs/current/ddl-partitioning.html) and
  [MySQL: partitioning keys, primary keys, and unique keys](https://dev.mysql.com/doc/refman/8.4/en/partitioning-limitations-partitioning-keys-unique-keys.html)
  both require a unique constraint to include the partition key. That constraint reaches back into
  the first migration, which is why partitioning intent is a design decision here.
- [PostgreSQL: `LIMIT` and `OFFSET`](https://www.postgresql.org/docs/current/queries-limit.html)
  states that rows skipped by `OFFSET` are still computed. It supports designing a bounded read path
  for an append-only table instead of relying on offsets.
- [PostgreSQL: using EXPLAIN](https://www.postgresql.org/docs/current/using-explain.html) establishes
  that plan choice depends on estimated row counts, which is why the proof step requires
  representative volume rather than a handful of rows.
