# API security boundaries

This page covers the design decisions; it does not replace a security review.

## Authorize every object, on every request

The most common API vulnerability is an endpoint that checks *who* you are and not *what you may
reach*. `GET /invoices/4` that returns invoice 4 to any authenticated caller is broken even though
authentication worked perfectly.

```http
# Broken: the identifier comes from the caller and nothing checks ownership.
GET /invoices/4
Authorization: Bearer <valid token for a different account>
```

Every request that names an object must verify that this caller may reach that object. Deriving the
scope from the token — `WHERE account_id = :token_account` — is more reliable than checking it
afterwards, because it cannot be forgotten on a new endpoint.

Unguessable identifiers are not authorization. They raise the effort of discovery and do nothing once
an identifier leaks into a log, a referrer, or a support ticket.

The same rule applies at the property level. A caller permitted to read an object is not
automatically permitted to read every field of it, or to write any of them — which is why responses
state their representation and requests state their writable set, per
[payloads-and-errors.md](payloads-and-errors.md).

And it applies at the function level: an administrative endpoint is not protected by being
undocumented or absent from the UI. Check the permission on the endpoint.

## Bound what one caller can consume

An API request costs bandwidth, CPU, memory, and storage. Without limits, one caller can exhaust
them:

- Page size limits on every collection, and a maximum request body size.
- Rate limits per caller, with the `Retry-After` contract in [reliability.md](reliability.md).
- Bounds on anything whose cost is caller-controlled: filter and sort fields, expansion depth,
  batch sizes, export ranges, and file uploads.
- A timeout on work the request performs, so a slow dependency cannot pin a worker indefinitely.

Rate limiting is also the control on business flows that are individually legitimate but harmful in
volume — creating accounts, sending invitations, submitting codes. Design that limit as part of the
flow rather than discovering it in production.

## Treat every caller-supplied URL as hostile

If an endpoint fetches a URL the caller provides — a webhook target, an avatar import, a callback —
it can be pointed at your own network, including cloud metadata endpoints. Validate against an
allowlist of schemes and hosts, resolve the name and check the resulting address, and re-check after
redirects. Do not rely on a blocklist of private ranges alone.

## Keep the contract from disclosing more than it should

- Do not return an internal error, stack trace, or database message to a caller.
- Keep authentication failures uniform, so response differences do not enumerate valid accounts.
- Decide once whether a forbidden resource answers `403` or `404`, and apply it consistently;
  inconsistency is what reveals existence.
- Do not put secrets or identifiers in URLs, which are logged by everything in the path.
- Ship no endpoint you cannot name. An undocumented, forgotten, or duplicated-in-staging endpoint is
  an attack surface nobody is maintaining, which is why the inventory in
  [contracts-and-docs.md](contracts-and-docs.md) is a security control and not only a documentation
  one.

## Do not lower your standards for data you did not author

A response from a third-party API is untrusted input in exactly the way a user's request body is.
Validate it, bound it, and time it out. Data arriving from a partner over TLS is still data an
attacker may control.
