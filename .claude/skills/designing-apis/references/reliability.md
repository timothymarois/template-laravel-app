# Reliability

## Make unsafe writes safely repeatable

A client that times out does not know whether the request arrived. It will retry. If the operation is
not idempotent, the retry charges the card twice.

`PUT` and `DELETE` are idempotent by definition. For `POST` and `PATCH`, accept an idempotency key:

```http
POST /payments
Idempotency-Key: 9f2c1e8a-5b1a-4a3d-9d3b-77d2f0f4b1c2
```

The server records the key with the outcome. A repeat of the same key returns the original result
rather than performing the work again. Three details decide whether this actually works:

- Scope the key to the caller so two clients cannot collide.
- Store the response, not just a "seen" marker, so the retry gets the same body and status.
- Decide what a repeat with the *same* key and a *different* body means. Returning the original
  silently hides a client bug; rejecting it is usually the better contract.

Give the record a documented lifetime, and say what happens after it expires.

## Protect against lost updates

Two clients reading, editing, and writing the same resource will overwrite each other unless the API
gives them a way not to. Use conditional requests:

```http
# The client received ETag "v7" when it read the invoice.
PUT /invoices/4
If-Match: "v7"
```

If the resource has changed, the server answers `412` and the client re-reads instead of clobbering.
Without this, the second writer always wins and the first writer's change vanishes with no error
anywhere.

`If-None-Match` on reads is the same mechanism used for caching: a `304` saves the body when the
client's copy is current.

Make the validator meaningful — a version column or a content hash. An ETag derived from a
last-modified timestamp with one-second resolution cannot distinguish two writes in the same second,
which is exactly when it matters.

## Say what a client should do when you say no

Rate limiting is a resource-consumption control, not an error condition. `429` should tell the
client when to come back, using `Retry-After`, and the same header belongs on a `503` during
maintenance or overload.

Publish the limit and the window. A client that cannot see its own budget can only discover it by
tripping it, and will usually respond by retrying immediately — which is the traffic pattern the
limit exists to prevent.

Rate-limit responses use the same error format as everything else, so a client's existing error
handling reads them.

## Model long work as a resource

When an operation cannot complete inside a request, return `202` and give the client something to
observe:

```http
HTTP/1.1 202 Accepted
Location: /exports/8f1c
```

The status resource reports state, progress if it is meaningful, and either the result or the
failure. This is better than holding the connection open, and far better than a `200` that promises
completion that has not happened.

Say explicitly whether the operation is idempotent, whether submitting twice creates two jobs, and
how long the status resource lives.
