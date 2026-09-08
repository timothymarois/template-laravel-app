# Resources, methods, and status codes

## Model the resource before the URL

A resource is a thing the client can name, fetch, and reason about — not a screen and not a database
table. Design the nouns first; the paths fall out of them.

- One concept per resource. If a response needs "and also" to describe it, it is two.
- A sub-resource expresses containment the client actually navigates: `/invoices/4/line-items`.
  Nesting beyond that becomes a URL the client has to construct from state it may not hold, so
  prefer a top-level resource with a filter once nesting reaches two levels.
- An operation that does not fit CRUD is often a resource of its own. A cancellation, a refund, or an
  export is a thing with an identity, a status, and a history — model it, and the awkward verb
  disappears.

Naming rules for paths, parameters, and fields are in [naming.md](naming.md).

## Choose the method by its documented properties

| Method | Safe | Idempotent | Use for |
|---|---|---|---|
| `GET` | yes | yes | Retrieval with no side effect |
| `HEAD` | yes | yes | Metadata for the same resource as `GET` |
| `OPTIONS` | yes | yes | Communicating capabilities |
| `PUT` | no | yes | Replacing a resource at a client-known identifier |
| `DELETE` | no | yes | Removal |
| `POST` | no | no | Creation under a server-assigned identifier, and everything else |
| `PATCH` | no | no | Partial modification |

Two consequences matter in review:

**A safe method must not change state.** Caches, prefetchers, link crawlers, and browser
speculation are all entitled to issue a `GET` without user intent. `GET /orders/42/cancel` is not a
style problem; it is an endpoint that fires by itself.

**`PUT` and `DELETE` must tolerate repetition.** A client that times out and retries a `PUT` must
land in the same state as a single call. `DELETE` on an already-deleted resource is a normal
outcome, not an error to be surprised by — decide whether it is `204` or `404` and document it.

`PATCH` is not idempotent in general, because a patch document can describe a relative change. If
your patches are absolute, say so; if a client needs safe retries, use an idempotency key rather
than claiming a property the method does not have. See [reliability.md](reliability.md).

## Pick the status code that matches the outcome

| Outcome | Code |
|---|---|
| Retrieved, or a synchronous action completed with a body | `200` |
| A new resource exists | `201`, with `Location` naming it |
| Accepted for later processing | `202`, with a way to observe the result |
| Succeeded and there is nothing to return | `204` |
| Malformed — the server cannot parse it | `400` |
| Not authenticated, or the credential is invalid | `401` |
| Authenticated but not permitted | `403` |
| No such resource, or one deliberately hidden from this caller | `404` |
| Conflicts with current state | `409` |
| A precondition on a conditional request failed | `412` |
| The body's media type is not supported | `415` |
| Understood, well-formed, and semantically invalid | `422` |
| A precondition is required before this may proceed | `428` |
| Rate limit exceeded | `429` |

The distinction that gets fumbled most often is `400` versus `422`: `400` is "I cannot parse this",
`422` is "I parsed it and it is not acceptable". A validation failure on a well-formed body is
`422`, and a client can act on that difference.

`401` versus `403` is the second: `401` means the request lacked valid authentication and retrying
with credentials may work; `403` means credentials were understood and are not enough. Returning
`403` for an unauthenticated request tells a client to give up when it should log in.

Where revealing existence is itself a disclosure, `404` in place of `403` is a deliberate choice —
make it deliberately, apply it consistently, and record it, because inconsistency is what leaks the
information you were protecting.

```http
# Bad: the caller cannot distinguish success from failure without parsing prose.
HTTP/1.1 200 OK
{"success": false, "message": "invoice not found"}

# Good: the status carries the outcome; the body explains it.
HTTP/1.1 404 Not Found
Content-Type: application/problem+json
{"type": "https://example.com/probs/not-found", "title": "Invoice not found", "status": 404}
```

The bad version defeats caching, client error handling, retry logic, monitoring, and every
intermediary between the server and the caller — all of which read the status line and nothing else.
