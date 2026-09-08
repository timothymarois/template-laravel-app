# Designing APIs Validation

This is the current validation record for `designing-apis`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for HTTP and RESTful interface design — resources and their names, method
and status-code semantics, payload and error formats, collections and pagination, idempotency and
concurrency control, rate limits, authorization boundaries, versioning and deprecation, and the
machine-readable contract. It should not activate for implementing a framework's routing, for
consuming somebody else's API inside application code, or for GraphQL, gRPC, or event-stream schema
design.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| API-T01 | Design the endpoints for a new invoicing service | Load |
| API-T02 | "Clients keep getting duplicate charges when the network drops" | Load |
| API-T03 | Design a GraphQL schema for the same data | Do not load; outside the boundary |
| API-T04 | Write the controller that serves an already-designed endpoint | Do not load; that is framework implementation |
| API-T05 | Call a third-party API from a service and handle its errors | Do not load; that is consuming, not designing |
| API-T06 | Add a field to a published response without breaking clients | Load |
| API-T07 | A Laravel application exposing a new public API | Compose with `using-laravel`; this package owns the interface, that one the framework |
| API-T08 | Endpoint design whose resources come from a new data model | Compose with `designing-databases`; the model is not the contract |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| API-W01 | `GET /orders/42/cancel` proposed | Reject it against the safe-method definition and explain that automatic agents can fire it; move to a `POST` on a modelled resource |
| API-W02 | Failures returned as `200` with an error body | Replace with the matching status code, and name what caching, retries, and monitoring lose |
| API-W03 | `POST /createInvoice` and `GET /getInvoiceById/4` | Apply the naming rules: the method supplies the verb, the path names the resource |
| API-W04 | A field renamed in a published payload | Treat it as breaking; stage replacement, deprecation signal, window, removal — never repurpose the old name |
| API-W05 | Retried payment creating duplicate charges | Introduce an idempotency key, scoped per caller, storing the response, with a stated conflict rule |
| API-W06 | "The API is RESTful and documented, so we're done" | Reject fluent assurance; require the description validated in CI and responses asserted against it, including the failure paths |
| API-W07 | It cannot be determined whether the API is already published or who calls it | Establish the consumers and the compatibility policy, or stop and name the unknown; do not assume a change is safe |
| API-W08 | `GET /invoices` returning every row | Bound it before the table grows, and prefer keyset pagination with a stated reason if offsets are chosen |
| API-W09 | An endpoint returning objects to any authenticated caller | Identify broken object-level authorization; derive scope from the token rather than checking after the fact |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. This package was added after the sampled run performed for the ten
  technology packages, so no case here has been executed.
- Codex: not run.

No case below is marked passed.

## Limits

This package was written from specifications rather than migrated from an existing catalog package,
so it has no inherited field history. API-T03, API-T04, and API-T05 are the exclusion cases most
likely to misfire and should be exercised first. No case runs against a live API.
