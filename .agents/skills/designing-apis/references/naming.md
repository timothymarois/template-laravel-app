# Naming an API

An API name is a published vocabulary: once a client depends on it, renaming it is a breaking
change no matter how the server is written. Names matter more here than almost anywhere else in a
system, because you cannot fix them later without a migration your consumers have to participate
in.

Naming is part of designing the interface, not a formatting pass at the end.

## Name the meaning, and let the method supply the verb

A resource name states what the thing *is*. The HTTP method already carries the action, so a verb in
the path is a second vocabulary for the same operation.

| Don't | Do |
|---|---|
| `POST /createInvoice` | `POST /invoices` |
| `GET /getInvoiceById/4` | `GET /invoices/4` |
| `POST /invoices/4/doArchive` | `POST /invoices/4/archive` |
| `/invoice`, `/Invoices`, `/line_items` | `/invoices`, `/line-items` — one convention, API-wide |

A verb in the path is legitimate for a genuine action that is not CRUD on a resource:
`/invoices/4/archive`, `/invoices/9/send`. It is not a workaround for a resource you have not
modelled. If you find yourself adding a third verb to one resource, the missing noun is usually the
real design problem.

Query parameters use one case convention API-wide and name the field they filter on:
`?status=active&invoice_id=4`. Pagination parameters are spelled the same way on every endpoint —
a second pagination vocabulary is one of the most expensive small inconsistencies an API can ship.

## Field names are a contract, not an implementation detail

| Don't | Do | Why |
|---|---|---|
| Mixing `camelCase` and `snake_case` in one payload | One convention, API-wide | Clients write two parsers for one API |
| Leaking an internal column name into a public contract | The canonical concept term, or a documented mapping | Internal renames become breaking changes |
| `reason_text`, `name_str`, `id_int` | `reason`, `name`, `id` | The type is in the schema and it changes |
| Returning the raw enum *and* a display string | Return the enum; the client maps it | A copy change becomes an API change |
| `id` meaning a different type on different endpoints | One identifier type and format | Callers cannot write one parser |
| `is_not_active`, `disable_notifications` | `is_active`, `notifications_enabled` | Double negatives at every call site |
| `data`, `info`, `details`, `meta`, `items` as the payload | The specific concept | Generic wrappers say nothing |
| `notes2`, `extra_field` | Name the second meaning | A numeric suffix is a missing concept |

Cardinality belongs in the name: `invoice_id` holds one, `invoice_ids` holds many. A plural that
carries a single value falsely promises a collection.

Preserve an immutable third-party field exactly at the adapter boundary and record the mapping — but
do not let a vendor spelling such as `VAT_ID` become precedent for new first-party fields.

## Error codes are the part clients branch on

Code form is `SCREAMING_SNAKE_CASE`, `{ENTITY}_{CONDITION}`, and stable forever.

| Don't | Do |
|---|---|
| `ERR_5`, or `BAD_REQUEST` for everything | `INVOICE_NUMBER_TAKEN`, `PAYMENT_WINDOW_EXPIRED` |
| Changing what an existing code means | Add a new code |
| A code derived from the HTTP status alone | The domain condition |

The status code says which class of failure it is; the code says which specific condition. A client
that can only see `400` has to parse prose to decide what to do.

Messages and correlation identifiers belong to the error format in
[payloads-and-errors.md](payloads-and-errors.md).

## Renaming a published name is a versioned change

Renaming a field, a code, or a path is breaking. Follow the deprecation sequence in
[evolution.md](evolution.md), which also covers why a name is never repurposed.
