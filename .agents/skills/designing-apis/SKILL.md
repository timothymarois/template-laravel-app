---
name: designing-apis
description: Use when designing, reviewing, documenting, or evolving an HTTP or RESTful API, including resource and URL modelling, method and status-code choice, request and response payloads, error formats, collections and pagination, filtering, idempotency and concurrency control, rate limits, authentication and authorization boundaries, versioning and deprecation, and the OpenAPI contract. Do not use for implementing a specific framework's routing, for consuming somebody else's API inside application code, or for GraphQL, gRPC, or event-stream schema design.
---

# Design APIs

An API is a contract you cannot take back. Once a client depends on a field, a status code, or an
ordering, changing it is a breaking change regardless of how the server is implemented. Design for
the client you cannot see.

This package owns the interface: what the resources are, what the methods and status codes mean,
what crosses the wire, and how it changes over time. It does not own the framework that serves it or
the data model behind it.

## Work in this order

1. Name the consumers and what they need to accomplish. An endpoint with no named caller is
   speculative surface.
2. Model the resources and their identity before choosing any URL, and name them deliberately —
   a published name is the hardest thing in an API to take back.
3. Map each operation to the method whose documented semantics already match it.
4. Define the representation, then the error representation, then the collection behavior.
5. Decide the compatibility and deprecation rules before the first client exists.
6. Write the contract down where it can be tested, and prove the API against it.

## Let HTTP mean what it already means

The strongest default is to use the semantics the specification already defines rather than invent
parallel ones. Two properties drive most method choices:

- **Safe** methods are read-only: `GET`, `HEAD`, `OPTIONS`, `TRACE`. A safe method that changes
  state breaks caches, prefetchers, and crawlers that are entitled to assume otherwise.
- **Idempotent** methods can be repeated with the same effect as one call: the safe methods plus
  `PUT` and `DELETE`. `POST` and `PATCH` are not idempotent.

```http
# Bad: a state change behind a safe method. A prefetch or a crawler can fire it.
GET /orders/42/cancel

# Good: the effect is a state change, so the method says so.
POST /orders/42/cancellation
```

Do not signal failure with `200` and an error body. A client that must parse the body to learn
whether the call worked cannot use any of HTTP's generic machinery, and every intermediary between
you and it is now misinformed.

## Prefer failure-preventing replacements

| Avoid | Prefer | Failure prevented |
|---|---|---|
| Verbs in the path (`/getUser`, `/createOrder`) | A resource plus the method | Two vocabularies for one operation |
| `200` with `{"error": ...}` | The status code that matches the outcome | Clients and intermediaries cannot tell success from failure |
| A bare string or array as the top-level body | A JSON object with named members | No room to add a field without breaking parsers |
| Returning the whole record because it is convenient | An explicit representation | Fields leak, then become contract |
| Accepting the whole record on write | An explicit accepted set | Mass assignment of fields the caller may not set |
| Offset pagination on a growing collection | Keyset pagination over a stable sort | Rows shifting between pages during iteration |
| Unbounded list responses | A default and maximum page size | One caller exhausting server resources |
| Retrying a non-idempotent write blindly | An idempotency key | Duplicate charges and duplicate records |
| A new field added as required | Optional with a default | Existing clients break on a minor change |
| Documenting the API after building it | A contract the tests run against | Documentation that drifts from behavior |

## Read only the needed depth

- Naming paths, fields, enum values, error codes, and events, and renaming safely once published:
  [naming.md](references/naming.md)
- Resource shape, URLs, method semantics, and status-code choice:
  [resources-and-methods.md](references/resources-and-methods.md)
- Request and response bodies, validation errors, and the problem-details format:
  [payloads-and-errors.md](references/payloads-and-errors.md)
- Pagination, filtering, sorting, and partial responses:
  [collections.md](references/collections.md)
- Idempotency keys, optimistic concurrency, retries, rate limits, and long-running operations:
  [reliability.md](references/reliability.md)
- Authentication and authorization boundaries and the documented API risks:
  [security.md](references/security.md)
- Versioning, backward compatibility, and deprecation:
  [evolution.md](references/evolution.md)
- OpenAPI, examples, and proving the implementation matches the contract:
  [contracts-and-docs.md](references/contracts-and-docs.md)
- Claim-to-source audit map: [sources.md](references/sources.md)

## Follow the repository before following this package

Where an API already exists, its established conventions win. A second pagination style, a second
error shape, or a second identifier format is worse for clients than an imperfect but consistent
one. Deviate only where the existing convention causes a correctness, security, compatibility, or
data-integrity defect, and say that is why.

## Report findings as evidence

```text
[HIGH] State change behind a safe method
Location: routes/orders.php:31 — GET /orders/{id}/cancel
Why: GET is defined as safe, so caches, prefetchers, and link crawlers may issue it without user
     intent. A cancellation can fire without anyone clicking.
Fix: POST /orders/{id}/cancellation, returning 200 with the cancellation or 409 if already cancelled.
Check: request the old path and assert it no longer mutates; exercise the new path for both outcomes.
```

Separate specification violations and security defects from house-style preferences, and label
which is which.
