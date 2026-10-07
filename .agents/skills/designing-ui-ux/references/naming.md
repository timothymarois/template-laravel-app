# Naming what the user reads

Interface text is not decoration applied at the end — a control the user cannot name is usually a
control whose behavior is not decided.

Every string below occupies a **slot**, and each slot has one job. Most bad interface copy is a
string doing two jobs at once: a label trying to also explain, or a button trying to also reassure.

The defaults here suit operator-style software. Regulated wording, localization, platform
conventions, and an established design system take precedence — record the exception rather than
deciding it per screen.

## Name the value, not the mechanism or the question

| Slot | Don't | Do |
|---|---|---|
| Column header | `How retries are handled` | `Retry limit` |
| Field label | `Why is this archived?` | `Reason` |
| Column header | `Who asked` | `Requester` |
| Column header | `Has the user logged in recently` | `Last sign-in` |

Question words, sentences, and narration of the flow all belong outside the name. If a column or
field genuinely needs explaining, add help text or a tooltip; do not turn the header into a
sentence.

## Shared mechanics

**Capitalization.** Sentence case is the default for English interfaces: `Add line item`, not
`Add Line Item`. Apply one convention across labels, headers, buttons, titles, tabs, and options,
and never introduce title case beside an established sentence-case interface.

**Terminal punctuation** follows the slot:

| Slot | Punctuation |
|---|---|
| Labels, headers, buttons, titles, tabs, menu items | None |
| Tooltips, help text, descriptions | Period, even for one sentence |
| Errors, empty states, confirmations, toasts | Period |
| Placeholders | None |

**Numbers and units.** State the unit beside the value or in the label — `Timeout (seconds)`,
`Size (MB)`. Show a currency code where more than one is possible. Show the timezone whenever
readers could interpret an instant differently. Give a rate its basis.

**Interpolation.** Build whole strings with placeholders — `{count} invoices selected` — never
concatenated fragments. Use real plural rules: never `item(s)`, never `1 items`. Do not build a
sentence whose word order assumes English if the product will ever be translated.

**Accessible names.** Every interactive element needs one, it contains the visible label where one
exists, and it still distinguishes the control. An icon-only trash button is `Delete invoice`, not
`Trash` and not `Button`. A placeholder is never an accessible name.

**Truncation.** Truncate at the end unless the tail is the distinguishing part, show the full value
on hover and in the accessible name, and never truncate a label — shorten the label instead.

## Buttons and actions

A button names the literal thing that happens: verb plus object, in the product's own vocabulary.

| Don't | Do | Why |
|---|---|---|
| `Submit` | `Save invoice` | Does not name the object |
| `Update record` | `Archive document` | A vague verb hides the actual state change |
| `Create new` | `Add line item` | Does not name the object |
| `Confirm` | `Send invoice` | Names the interaction, not the act |
| `Yes` | `Delete project` | A confirmation button must repeat the real verb and object |

Keep the same name through the control, the confirmation, the loading state, and the result. A
button that says `Delete project` must not open a dialog whose confirm button says `OK`.

A button that opens a form still names the destination act: `Add invoice`, not `Open invoice form`.

`Cancel` abandons an edit, `Close` dismisses a read-only view, `Back` steps within a flow. Do not
invent alternatives. A bulk action carries the live count: `Delete 4 invoices`. A menu uses verbs
for items and nouns for section headers, and never mixes them in one list.

When a control is disabled and someone is likely to expect it to work, say why:
`Cannot archive an invoice with active line items.`

## Values, states, and metrics

Enum display values are product vocabulary: `Active`, `Paused`, `Archived` — adjectives or past
participles, never verbs, and never `Currently active` or `Turned off`.

**Never render a raw stored value.** `PENDING_REVIEW` and `line_item` are machine states; the
interface maps them to display terms. A view that only replaces underscores turns
`awaiting_invoice_confirm` into text nobody wrote.

Render an empty value with the product's chosen null marker, and distinguish "none", "not
applicable", "unavailable", and "redacted" when they mean different things and revealing the
difference is safe. **Zero renders as `0`, never as the null marker** — they are different facts.

A metric label is a noun phrase with its basis in parentheses: `Revenue (last 30 days)`,
`Delivery rate (last 7 days)`. This is the slot where the urge to explain in the label is strongest;
resist it. Any metric derived from other metrics needs a tooltip stating the formula in plain terms.

A chart title names what is plotted and the period; an axis names the measure and its unit; a legend
entry is the product's term for the series. Never label a chart with its own chart type.

Counts read as data: `1 invoice`, `12 invoices`, `1–50 of 213`. Not `Showing 12 of 12 results`, and
not `You have 12 invoices`.

## Feedback, empty states, and errors

**Two empty states, always.** Zero rows because nothing exists and zero rows because a filter
excluded everything are different states with different remedies. Showing the never-had-data message
while a filter is active is a defect, not a wording problem — it makes people believe their data was
deleted. Build both into the shared list component so a screen cannot forget one.

> `No invoices yet.` — never had data
> `No invoices match the current filters.` **Clear filters** — filtered to zero

State the fact first. No sympathy, no metaphor, no personality: not `It's quiet in here!`, not
`Time to get paid!`.

**Validation errors** name the constraint in the same words as the field, beside the field:
`Retry limit must be between 1 and 10.`, not `Invalid value`. Two recurring defects are worth
checking for explicitly — a framework default leaking the column name
(`The received_at field must be a valid date`), and validating input the interface itself produced,
where the real fix is the format contract between the control and the validator, not the wording.

**Conflict errors** name the entity and the condition: `An invoice with that number already exists.`
Never substitute a pronoun for something that has a name.

**System errors** state what failed, whether data was saved, what to do, and a reference:
`Could not save the invoice. No changes were made. Try again, or contact support with reference 4f21c8.`
`Oops` and `Something went wrong` replace all four with none of them.

**Partial failure** states the split and how to see the rest: `Imported 37 of 40 rows. 3 rows failed.`
Never report it as a success, and never as a total failure.

**Success** is a past-tense verb plus object: `Invoice saved.` Not `Success!`, not `All set!`, and
not an exclamation mark. Suppress the confirmation entirely when the result is already visible on
screen — a row appearing in a table confirms itself.

**Loading** is a skeleton, or `Loading {noun}`. Progress is `{verb-ing} {n} of {total} {noun}`.
Never `Hang tight`, never a rotating set of jokes.

## Words to keep out of functional text

| Don't | Why |
|---|---|
| Marketing adjectives: powerful, seamless, robust, smart | They say nothing to someone doing the task |
| Minimizers: simply, just, easily, only | They add no recovery information and understate difficulty |
| Hedging: it looks like, it seems, apparently | Erodes trust in every other message |
| Unchosen first person: `we couldn't find`, `our records` | One screen invents a different speaker |
| Vague pronouns for named entities | The system knows what it is; say it |
| Unchosen exclamation marks and emoji | Accidental tone changes in operational text |
| Non-standard abbreviations: `cust`, `qty`, `mgr` | Saves keystrokes once, costs comprehension forever |

## Keep one term across the interface and the system

The label, the form field, the column header, the export header, the API field, and the database
column should all trace back to one concept. When the database says `cust_flg`, the model says
`isCustomer`, the API says `customer_type`, and the screen says `Client`, everyone — every engineer,
every support agent, every user — pays a translation tax forever.

Parity means shared meaning, not identical spelling: localization, a published contract, a privacy
boundary, or an audience-specific label can require a different surface form, and that mapping is
recorded deliberately. Accidental drift is not.

An export header is an interface slot that happens to live in a file. Someone reconciling a
spreadsheet against a screen should not have to translate.

Take the words from the domain. If practitioners say "endorsement", the term is `endorsement`, not
`policy change`. Plain language means avoiding invented abstraction, not avoiding the reader's own
vocabulary.

## Test a string before you ship it

1. **Slot.** What job does this string have? If it is explaining, it belongs in help text, not in a
   name.
2. **Meaning.** What fact, value, or outcome does it establish? Do not invent a state, a metric, or
   a capability the product does not have.
3. **Canonical term.** Does this concept already have a word in this product? Reuse it.
4. **Fortieth time.** Read it as the operator who sees it for the fortieth time. Does it help them
   scan, or make them read?
