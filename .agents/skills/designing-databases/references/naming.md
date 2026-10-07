# Naming stored data

These names outlive every screen built on them: a column survives three interface redesigns and
four engineers. Name it for the person reading it in 2031 with no context.

Naming is part of designing the schema, not a tidy-up pass afterwards. A column you cannot name is
usually a column you have not finished modelling.

## Name the value, never the mechanism or the question

A name states what a value *is*. An explanation of how it works belongs in a comment or the data
dictionary, not in the name's slot.

| Don't | Do | Why |
|---|---|---|
| `how_an_invoice_is_paid` | `payment_method` | A sentence in the name's slot leaves the real values unnamed |
| `retry_mode_flag` | `retry_limit` | Names the mechanism instead of the value |
| `why`, `who_asked` | `reason`, `requester_id` | Question words are not names |
| `did_the_user_confirm` | `is_confirmed` | Predicate, not a sentence |

Four failure modes are worth checking explicitly before you commit a name: naming the mechanism
rather than the value; putting a question word or a sentence where a noun belongs; blending a name
and its explanation into one string; and writing prose where a factual term belongs.

**If you cannot define a column in one sentence, it is not one column.** A definition that needs
"sometimes it holds X, but if Y then Z" describes two columns.

## Tables

Table plurality, casing, and join-table form are the owning framework's convention — Laravel pairs a
singular `Invoice` model with a plural `invoices` table; another stack chooses differently. Follow
the stack in use rather than exporting one ecosystem's rule as universal.

| Don't | Do | Failure prevented |
|---|---|---|
| `tbl_invoice`, `invoice_tb` | The framework's established form | A storage prefix adds no domain meaning |
| `data`, `records`, `misc_settings` | The actual concept | Generic nouns make unrelated tables indistinguishable |

## Columns

Common form is `snake_case`, singular, naming the value. Compose the name from the concept, its
property, and a representation term so that `table.column` reads as one precise fact. Omit the
concept when the table already supplies it unambiguously — `invoices.name`, not
`invoices.invoice_name` — but qualify a generic representation when the property would otherwise
disappear: `publication_status`, `sale_amount`.

Bare `status`, `type`, or `value` may be precise inside a tightly bounded model, and may also
collide with framework behavior — Rails reads a bare `type` as single-table inheritance. Inspect the
owning model and framework before using one.

| Don't | Do | Why |
|---|---|---|
| `reason_text`, `name_str`, `count_int` | `reason`, `name`, `count` | The type is in the schema and it changes |
| `col1`, `field_2`, `misc` | The meaning | Unnameable means undesigned |
| `qty_rcv`, `dt_crt` | `quantity_received`, `created_at` | Non-domain abbreviations cost comprehension forever |
| `notes2`, `extra_field` | Name the second meaning, or do not add it | A numeric suffix is a missing concept |
| `data`, `info`, `details`, `meta` | The specific thing | These say "I have not decided what this is" |

Cardinality belongs in the name at the point of use: `invoice_id` holds one, `invoice_ids` holds
many. A plural that holds one value falsely promises a collection; a singular that holds many hides
it. Table plurality is a separate, ecosystem-level convention and is not evidence about a column.

## Booleans

Use the schema's predicate convention; `is_` / `has_` / `can_`, stated positively, is a good
default.

| Don't | Do |
|---|---|
| `active` | `is_active` |
| `is_not_active`, `disabled`, `no_email` | `is_active`, `email_enabled` |
| `deleted`, when the time matters | `deleted_at`, null while live |
| `flag`, or `status` holding true/false | Name the condition, or make it an enum |
| `is_active` + `is_archived` + `is_draft` | One `status` enum |

`WHERE NOT is_not_active` is a double negative at every call site. Prefer the positive form when it
states the same fact — but preserve a genuinely negative domain fact or a fixed external contract,
and map it at the boundary rather than silently inverting its meaning.

**Do not let mutually exclusive booleans grow into a state machine.** Three booleans express eight
combinations, of which perhaps three are legal. Use an enum when the states cannot coexist; keep
separate columns when the facts are orthogonal and may coexist, and document the legal combinations.

## Timestamps and dates

Name the event, then apply the schema's established suffix. `{event}_at` for an instant and
`{event}_on` for a civil date are useful where that convention is already in force; they are not
universal rules.

| Don't | Do |
|---|---|
| `date_created`, `when_archived`, `crt_dt` | `created_at`, `archived_at` |
| `timestamp`, `date`, `time` | The event it records |
| `effective_date_date` | `effective_on` |

Name the **business event**, not the row operation. `received_at` — when the document arrived — and
`created_at` — when we inserted the row — are different facts, and conflating them destroys
reconciliation. Document the temporal model: UTC is a strong default for instants, while domain-local
dates and civil times stay local when that locality is the fact being stored.

## Foreign keys

`{singular_entity}_id` is the common form. A role-bearing name may defeat framework inference and
then needs the relationship configured explicitly — do that rather than reusing one name for two
roles.

| Don't | Do |
|---|---|
| `invoice`, `invoiceid`, `fk_invoice` | `invoice_id` |
| `parent_id` with no indication of parent type | `parent_invoice_id` |
| `user_id` and `user_id_2` for two roles | `created_by_id`, `approved_by_id` |

## Enums and stored states

Stored values are `snake_case` or `SCREAMING_SNAKE`, chosen once per codebase. Values are **states**:
adjectives or past participles.

| Don't | Do |
|---|---|
| `archive`, `suspend` | `archived`, `suspended` |
| `1`, `2`, `3` stored in the column | Named values |
| `Active`, `PENDING_REVIEW`, `paused` mixed | One case convention |
| `status` holding `'Archived (see notes)'` | The enum value only |

**Do not store presentation text as a machine state.** Storing `Pending review` as the value makes a
copy change into a data migration. **Do not render machine values directly** either: keep an
authoritative stored-to-display mapping in the presentation layer, because a view that merely
replaces underscores turns `awaiting_invoice_confirm` into poor interface text.

## Units, money, and precision

| Don't | Do |
|---|---|
| `price` as a float | `price_cents` as an integer |
| `amount` with no currency | `amount_cents` plus `currency_code` |
| `timeout`, `distance`, `size` | `timeout_seconds`, `distance_meters`, `size_bytes` |
| `0.15` in one column and `15` in another | One named convention: `rate_bps` or `rate_percent` |

Include the unit whenever the number could be misread. A unit that lives only in a doc comment gets
misread eventually, and most expensively when it is money.

## Absence has to mean one thing

Decide and document what an absent value means, because ambiguity here causes bugs no naming fixes.

| State | Meaning |
|---|---|
| `NULL` | One documented absence meaning for this field |
| An explicit state | "Unknown" and "not applicable" are different domain facts |
| `0` | Known to be zero — never rendered as absent |
| `''` | Known to be empty |
| A sentinel such as `-1` or `9999` | Do not use one |

Never let `''` and `NULL` both occur in one column; pick one and add a constraint.

## Keep one canonical term across layers

The same concept should trace back to one term in the column, the API field, the form field, the
label, and the export header. When the database says `cust_flg`, the model says `isCustomer`, the API
says `customer_type`, and the screen says `Client`, there are four vocabularies for one idea and
everyone pays a translation tax forever.

Parity means shared meaning, not identical spelling. A published contract, a vendor schema, a
localization, or a privacy boundary may require a different surface form — record that mapping
deliberately. What is not acceptable is accidental drift, or a display-layer alias quietly hiding an
owned name nobody fixed.

Take the words from the domain's own vocabulary. If the business says "buyer", a schema that says
`party` or `entity` has invented a superordinate nobody speaks.

## Renaming stored data is a migration, not an edit

Private, unpublished names can be changed atomically. Anything stored or published is a contract.
Before renaming, enumerate the full fanout: column, index, constraint, API field, validation rule,
form field, label, export header, saved filters and reports, and the lexicon entry. Then stage it —
expand, migrate, deprecate, contract — rather than renaming in place.

Prefer getting the name right for new work, and assess migrations, reports, integrations, and
rollback before renaming stable owned data.
