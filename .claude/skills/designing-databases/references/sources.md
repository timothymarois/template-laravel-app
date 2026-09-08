# Database-design source basis

Use this file to audit or revise a lesson, not as another modelling procedure.

**Scope:** this file covers the `## Naming` section of `SKILL.md`. The package's other sections
predate it and are not yet source-mapped; do not read their absence here as a claim that they are
unsourced or as a claim that they are verified. Links and claims were checked on **2026-08-13**.

## Name composition

- [ISO/IEC 11179-5:2005, *Metadata registries — Naming and identification principles*](https://cdn.standards.iteh.ai/samples/35347/775e430277b149ba8fe4c5b823cc8967/ISO-IEC-11179-5-2005.pdf) —
  the standard supplies the vocabulary the naming rule is built on: an **object class term** is "part
  of the name of an administered item which represents the object class to which it belongs", a
  **property term** "expresses a property of an object class", and a **representation term** is the
  "designation of an instance of a representation class" (clause 3). Clause 6.2 requires a naming
  convention to document its semantic, syntactic, and lexical rules, and notes that "an effective
  naming convention can also enforce the exclusion of irrelevant facts about the administered item
  from the name". **Read this boundary carefully before citing it:** the object class + property +
  representation composition appears in **Annex A, which the standard labels informative**, and
  clause 7.5 offers "the property term is always the last part of a name" merely as an example of an
  absolute syntactic rule. 11179-5 therefore does *not* mandate the composition, and writing "ISO
  11179 requires" it would be a citation error. The linked copy is a watermarked publisher preview
  covering clauses 1–7 and the start of Annex A. The current edition, 11179-5:2015, is behind
  [ISO's OBP](https://www.iso.org/obp/ui/en/#!iso:std:60341:en), which returned HTTP 403 and could
  not be verified.
- [WIPO Standard ST.96, Annex I — XML Design Rules and Conventions, v10.0 (2026)](https://www.wipo.int/standards/en/st96/v10-0/annex-i/03-96-i.pdf) —
  this is the normative, MUST-level statement of the composition, and states that its conventions
  "are based on the guidelines and principles described in document ISO 11179 Part 5". **[GD-21]**:
  "the Object Class Term MUST precede the Property Term and the Property Term MUST precede the
  Representation Term." **[GD-05]**: names "SHOULD consist only of nouns, adjectives, and verbs in
  the present tense". **[GD-27]**: "Connecting words like 'and', 'of' and 'the' SHOULD NOT be used
  … unless they are part of the business terminology." GD-05 and GD-27 together are the evidentiary
  basis for excluding interrogatives and sentence-shaped names: `who`, `why`, and `when` are none of
  noun, adjective, or present-tense verb, and `how_an_invoice_is_paid` carries both an article and a
  copula. **[GD-22]** removes a representation term that repeats the property term, which is why the
  skill treats the generic word as a suffix rather than a separate ban. **[GD-25]** — "Word(s) in a
  name SHOULD be in singular form unless the concept itself is plural" — is the ST.96 half of the
  table-number row in the skill's conventions table, and the reason that row reports no consensus
  rather than following the frameworks.
- [ISO/IEC 11179-4:2004, *Formulation of data definitions*](https://cdn.standards.iteh.ai/samples/35346/e1828b73c98b4fbca61fe950da0b7748/ISO-IEC-11179-4-2004.pdf) —
  clause 4.1 requires that a data **definition** "be stated as a descriptive phrase or sentence(s)"
  and clause 4.2 that it "be expressed without embedding rationale, functional usage, or procedural
  information". The standard assigns the sentence to the definition; 11179-5 clause 3.4 defines a
  name separately as a "designation of an object by a linguistic expression". **This is the skill's
  inference, not a literal prohibition:** no located source names question-shaped columns as an
  antipattern, and the skill's opening line — a sentence in the name's slot is a definition in the
  wrong place — is assembled from the two standards' division of labour.

## Domain vocabulary

- [Eric Evans, *Domain-Driven Design Reference*, March 2015](https://www.domainlanguage.com/wp-content/uploads/2016/05/DDD_Reference_2015-03.pdf) —
  the Ubiquitous Language pattern commits a team to one language "in all communication within the
  team and in the code", holds that "a change in the language is a change to the model", and
  directs teams to "refactor the code, renaming classes, methods, and modules to conform". Model-Driven
  Design adds "draw from the model the terminology used in the design". This supports taking the
  schema's words from the domain rather than inventing a superordinate. The Domain Events summary
  separately notes that such an event "typically contains a timestamp for the time the event
  occurred and the identity of entities involved", which is the model-level shape behind
  `paid_at` plus `payer_account_id`. The publisher returns HTTP 403 to some clients; the document
  was retrieved directly and read.
- [Martin Fowler, "Ubiquitous Language", 31 October 2006](https://martinfowler.com/bliki/UbiquitousLanguage.html) —
  a short attribution of the term to Evans and a statement of the practice. It establishes currency
  of the idea outside its source book and nothing more; it supplies no naming mechanics.
- [Feitelson, Mizrahi, Noy, Ben Shabat, Eliyahu, and Sheffer, "How Developers Choose Names",
  arXiv:2103.07487, 12 March 2021](https://arxiv.org/pdf/2103.07487) — 334 subjects across a
  sequence of experiments (the first survey ran on Qualtrics in May 2018: 121 questions, 11
  scenarios, 234 respondents, 70% students, mean 5.8 years of experience). Two findings carry the
  skill's rules. First, naming is not self-correcting: across 47 instances "the median probability"
  that two developers choose the same name "was only 6.9%". Second, a structured procedure measurably
  helps — names produced with the authors' three-step model (select the concepts, choose words for
  each, construct the name) "were judged by two independent judges to be superior … by a ratio of
  two-to-one". That model is the practitioner-facing analogue of the object class / property /
  representation decomposition, and it is the one empirical result that endorses structured name
  construction rather than taste.

## Meaningless and misleading names

- [Tim Ottinger, "Ottinger's Rules for Variable and Class Naming" (1997)](https://exelearning.org/wiki/OttingersNaming/) —
  Rule 14, "No Disambiguation without Differentiation", states plainly that "Info and Data are like
  'stuff': basically meaningless", with the pairs `ProductInfo`/`Product`, `CustomerObject`/`Customer`, and
  `NameString`/`Name`; the skill's `product_info` and `name_string` examples are these, transposed to
  SQL. Rule 11 supplies the disinformation case — do not call something an `AccountList` unless it is
  a list. This is the documented antecedent of the naming chapter in Robert C. Martin's *Clean Code*
  (2008); that chapter has no free primary text and is **not** cited here, because every available
  version of it was a third-party summary. The link above is a mirror — the original at
  `objectmentor.com` is defunct.
- [Avidan and Feitelson, "Effects of Variable Names on Comprehension: An Empirical Study", ICPC 2017](https://www.cs.huji.ac.il/~feit/papers/Names17ICPC.pdf) —
  9 professional developers, 6 methods drawn from production utility packages, 38 recorded sessions
  over roughly 22 hours, with variable names replaced by consecutive letters of the alphabet. The
  result that matters here is the negative one: in 3 of the 6 methods there was no significant
  difference between the real names and the meaningless ones, "due to poor and even misleading
  variable names". A bad name is not merely untidy — it can carry as little as no name at all.
- [Lawrie, Morrell, Feild, and Binkley, "Effective Identifier Names for Comprehension and Memory",
  *Software Quality Journal*](https://www.cs.kent.edu/~jmaletic/cs63902/Papers/Lawrie07.pdf) — 128
  participants, 12 functions of 8–36 lines, three variants differing only in identifier quality
  (single letters, abbreviations, full words), comments stripped. "Full-word identifiers lead to the
  best comprehension", though often without a statistical difference from well-chosen abbreviations.
  **Scope this claim honestly when citing it:** the study contrasts identifier *length*, not
  question-shaped against value-shaped names, so it supports the cost of meaningless names and not
  the specific antipattern. This is the author preprint; the publisher version is paywalled, and the
  ICPC 2006 conference original (DOI 10.1109/ICPC.2006.51) was not openly fetchable.

## Conventions, and where they conflict

The skill presents these as ecosystem conventions precisely because the sources disagree.

- [`sqlstyle.guide`, Simon Holywell (CC BY-SA 4.0, no version or date printed)](https://www.sqlstyle.guide/) —
  publishes the uniform-suffix list the skill's "generic word is a suffix" rule relies on: `_id`,
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
  skill's `type` rule — Rails reads a `type` column as single-table inheritance. Rails and Laravel
  pluralize table names while Django's is `app_model`, singular; there is no consensus to report.
- [PostgreSQL Wiki, "Don't Do This"](https://wiki.postgresql.org/wiki/Don%27t_Do_This) — the
  project's own list: "Don't use upper case table or column names … Don't use `NamesLikeThis`, use
  `names_like_this`", because the mixed-case identifier must then be quoted everywhere.
- [RuboCop `Naming/PredicatePrefix`](https://docs.rubocop.org/rubocop/latest/cops_naming.html) and the
  [Ruby Style Guide](https://rubystyle.guide/) versus
  [`@typescript-eslint/naming-convention`](https://typescript-eslint.io/rules/naming-convention/) —
  a direct contradiction, and the reason the skill scopes the boolean rule to the language. Ruby
  forbids the `is_`/`has_`/`have_` prefixes as "redundant and inconsistent with the style of boolean
  methods in the Ruby core library" (`def tall?`, not `def is_tall?`); typescript-eslint documents
  `prefix: ["is", "should", "has", "can"]` as an ordinary configuration.

## What no tool enforces

- [SQLFluff rules reference](https://docs.sqlfluff.com/en/stable/reference/rules.html) — the
  naming-adjacent rules are capitalisation (CP01–CP05), references (RF01–RF06, including "Keywords
  should not be used as identifiers" and "Do not use special characters in identifiers"), and alias
  length (AL06). **Nothing judges whether an identifier means anything.**
- [schemalint](https://github.com/kristiandupont/schemalint) ships `nameCasing`, `nameInflection`,
  and `requirePrimaryKey`; [Squawk](https://squawkhq.com/docs/rules) covers migration safety only and
  was not fetched directly. No located SQL linter enforces semantic naming, which is why the skill
  states that a name passing the linter has not been checked.
- [ESLint `id-denylist`](https://eslint.org/docs/latest/rules/id-denylist) is the clearest mechanical
  precedent for banning meaningless identifiers, and its own documented configuration is
  `["error", "data", "err", "e", "cb", "callback"]` — `data` first. It applies to declarations and
  assignments, not to call sites or property reads. [Pylint `disallowed-name`](https://pylint.readthedocs.io/en/stable/user_guide/messages/convention/disallowed-name.html)
  is the same shape, with `bad-names` defaulting to `("foo", "bar", "baz", "toto", "tutu", "tata")`
  and its documentation flagging `foo()` in favour of a descriptive `print_fruit()`; the
  `bad-names-rgxs` option is the hook a project would use to enforce a rule like this one.
  [Pylint `invalid-name`](https://pylint.readthedocs.io/en/stable/user_guide/messages/convention/invalid-name.html)
  checks casing per identifier kind and nothing semantic.

## Deliberate exclusions

- No claim that ISO/IEC 11179 mandates the object class + property + representation composition; the
  normative form cited is WIPO ST.96 [GD-21].
- No claim that any source names question-shaped column names as an antipattern. Bill Karwin's *SQL
  Antipatterns* (Pragmatic Bookshelf, 2010) was examined and is **not** cited: its Metadata Tribbles
  chapter is the inverse case — data values promoted into names — and its chapter text could not be
  verified from any authorized copy.
- No citation of Joe Celko's *SQL Programming Style* (2005). Its content is widely restated as
  adopting ISO 11179 naming, but every accessible copy was a third-party or unauthorized
  reproduction, and the publisher's table of contents returned HTTP 403.
- No universal ruling on table pluralization, on bare `id`, or on `_at` against `_date`. The sources
  conflict, and the skill asks only for consistency within one schema.
- No claim that an existing linter can enforce the naming rule.
- No empirical claim that value-shaped names measurably outperform question-shaped ones; the studies
  cited establish the cost of meaningless and misleading names, not that specific contrast.

## Designing for performance

The rules in `design-for-performance.md` are modelling consequences of documented engine behavior.
Each engine page below was opened and checked on 23 August 2026; the engine packages in this catalog
carry the same citations for the tuning side of each fact.

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

### Catalog conclusions

- Listing access paths, selectivity, and result cardinality before drawing tables is this package's
  method, not a documented database procedure.
- The four "shapes that cannot be indexed" are a local diagnostic grouping. Each individual fact is
  engine-documented; the grouping is ours.
- Requiring a measurement with its row count attached, and requiring an unproven shape to state its
  assumption rather than imply evidence, is this catalog's evidence standard.

## Attribution

This package adapts `skills/database-design/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`. The upstream package was released under the Unlicense,
which places it in the public domain and imposes no attribution condition; this note records the
provenance anyway so the lineage of the guidance stays traceable. The upstream notice read:

```text
This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

For more information, please refer to <https://unlicense.org>
```

Material modifications: renamed to `designing-databases`; engine-skill pointers retargeted to this
catalog's packages and marked as non-dependencies; `references/design-for-performance.md` added to
cover design-time performance decisions; `references/naming.md` added, as described below; the
in-package `LICENSE.txt` removed, as the package contract permits only `SKILL.md` and `references/`;
and a maintainer validation record added.

`references/naming.md` is adapted from the naming and grammar guidance in the same catalog and at
the same commit, path `skills/naming-grammar-conventions/`, published by Rundesk AI under the MIT
License. Its table, column, boolean, timestamp, foreign-key, enum, unit, nullability, and
safe-rename sections are carried here and rewritten for a schema-design audience, so stored data can
be named correctly without a second package installed. That package's wider scope — code
identifiers, interface copy, and the product lexicon — is deliberately not reproduced.

The naming source material is cited in its own right:

- The naming guidance's own `references/sources.md` at that commit maps its rules to the standards,
  style guides, framework conventions, and studies behind them, and records where those conflict.
  The rules carried here retain their scope: framework-specific forms are labelled as ecosystem
  conventions rather than universal rules.
