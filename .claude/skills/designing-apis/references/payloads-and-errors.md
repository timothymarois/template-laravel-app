# Payloads and errors

## Give every payload room to grow

Make the top level of every JSON body an object, never a bare array or scalar. An array at the top
level has nowhere to put pagination, warnings, or a new field, so the first thing you need to add
becomes a breaking change.

```json
// Bad: nothing can ever be added alongside the list.
[{"id": 1}, {"id": 2}]

// Good: the envelope has room.
{"data": [{"id": 1}, {"id": 2}], "page": {"next": "..."}}
```

Be explicit in both directions:

- **Responses** return a stated representation, not whatever the record happens to hold. A field
  that leaks once is a field clients now depend on.
- **Requests** accept a stated set of writable fields. Binding the whole body to a record is how a
  caller sets `role` or `is_admin` on an endpoint that never meant to offer it.

Distinguish an absent field from a null one and document what each means, especially for `PATCH`
where "not mentioned" and "set to null" are different instructions.

## Use one error format everywhere

Pick one shape and use it for every failure the API can produce, including the ones the framework
generates. Problem Details is the specified default: a JSON object with `type`, `title`, `status`,
`detail`, and `instance`, served as `application/problem+json`, extended with your own members.

```json
{
  "type": "https://example.com/probs/validation",
  "title": "The invoice could not be created",
  "status": 422,
  "code": "INVOICE_NUMBER_TAKEN",
  "errors": [
    {"field": "number", "code": "TAKEN", "detail": "Invoice number 2026-001 is already in use."}
  ]
}
```

What makes this usable is that the machine-readable parts and the human-readable parts are separate.
`status` and `code` are what a client branches on; `title` and `detail` are what a person reads. A
client should never have to pattern-match on a sentence, because a sentence is exactly what you will
want to reword later.

Field-level validation errors belong in a list with a stable identifier per entry, so a form can
attach each message to the input that caused it.

`type` is a URI that identifies the problem kind. It does not have to resolve, but if it does, point
it at documentation rather than at a placeholder.

## Do not let the framework leak

An unhandled database or framework error reaching the client is both a poor contract and a
disclosure. Map exceptions to domain errors at the boundary. `SQLSTATE[23000]: Integrity constraint
violation` tells an attacker about your schema and tells a legitimate client nothing it can act on.

Include a correlation identifier on unexpected errors so a user's report maps to a log line, and
keep stack traces and internal messages out of the response entirely.

Error code naming — the part clients branch on and can never be changed — is in
[naming.md](naming.md).
