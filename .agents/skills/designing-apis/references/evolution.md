# Evolving an API

Decide the compatibility rules before the first client exists — the moment there are two, changing
them is negotiation.

## Know what is actually breaking

Additive and safe for existing clients:

- A new optional request field with a default.
- A new field in a response, provided clients tolerate unknown members.
- A new endpoint, a new optional parameter, a new enum value *in a field the client only reads*.

Breaking, whatever the implementation looks like:

- Removing or renaming a field, endpoint, parameter, or error code.
- Changing a type, a format, or the meaning of an existing value.
- Making an optional request field required, or narrowing what is accepted.
- Changing a status code for an existing outcome, or a default value.
- Changing default sort order or pagination behavior.
- Adding a new enum value to a field the client must *switch* on — old clients have no branch for it.

That last pair is what catches teams out. "We only added something" is not a safety argument on its
own; whether it breaks depends on how clients consume it. Publish, from the start, what clients must
tolerate: unknown fields, new enum values, and unrecognized error codes. A stated tolerance contract
is what makes additive change safe.

Never repurpose an existing name to mean something new. It is the one change no client can detect
until it is already producing wrong results.

## Version at the boundary that actually changes

Version only when a change is genuinely breaking and cannot be made additively. Every version is
another implementation to keep correct.

- **URL path** (`/v2/invoices`) is the most visible and easiest to route, cache, and debug. It
  versions the whole surface, which is coarse.
- **Media type** (`Accept: application/vnd.example.v2+json`) versions the representation and can be
  finer-grained, at the cost of being invisible in a URL and easy to get wrong in a client.

Either is defensible. Pick one, apply it everywhere, and do not run both. Whichever you choose,
version the contract, not the deployment — a client should not be able to tell that you shipped.

Use semantic versioning language honestly if you use it at all: a breaking change is a major, and
calling one a minor because it felt small does not make it compatible.

## Deprecate on a published schedule, not by surprise

1. Ship the replacement alongside the old surface.
2. Mark the old one deprecated in the contract, and signal it in responses — the `Deprecation` and
   `Sunset` headers exist for this — so a client learns from traffic, not from a blog post.
3. Give a window long enough for the actual consumers, and tell them the date.
4. Find out who is still calling it. Usage telemetry per client is the difference between a
   deprecation and an outage.
5. Remove it after the window, and answer removed endpoints with an error that says what replaced it.

An internal API with known callers can move faster than a public one. What does not change is the
order: replacement first, signal second, removal last.

Renaming rules for published names are in [naming.md](naming.md).
