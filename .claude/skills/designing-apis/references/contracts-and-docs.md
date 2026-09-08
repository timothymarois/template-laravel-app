# The contract and its documentation

## Write the contract where it can be executed

An API description that lives only in prose drifts, because nothing fails when it stops being true.
Write the contract in a machine-readable description — OpenAPI is the default for HTTP APIs — and
keep it in the repository next to the code it describes.

The description is worth writing first. Designing the endpoint, its parameters, its representation,
and its failures before implementing turns disagreements into edits rather than migrations. It also
makes the surface visible: an endpoint nobody can justify is easier to delete from a document than
from production.

Whether the description generates from the code or the code is checked against the description
matters less than which one is authoritative. Decide, and make the other one fail when they diverge.

## Document what a caller cannot guess

Cover, for every endpoint: the purpose, the authorization required, the parameters and their
constraints, the representation, **every** error it can return, and its pagination, rate-limit, and
idempotency behavior.

Errors are the half that is usually missing, and the half integrators need most. A client can guess
the success shape from one call; it cannot guess the eleven failure modes without hitting them in
production.

Include realistic examples for both success and failure. Generated examples full of `string` and `0`
teach a reader nothing about what a valid request looks like.

## Prove the implementation against the contract

Structural validity is not conformance — a description can be perfectly well-formed and describe an
API that does not exist.

1. Validate the description itself, in CI.
2. Assert responses against it, so a handler that returns an undocumented shape fails a test.
3. Cover the documented failures, not only the happy path.
4. Check compatibility between versions of the description, so a breaking change is caught before
   release rather than reported by a consumer.

Where consumers are known, contract tests on both sides catch the case where a producer's change is
technically compatible and practically breaks a real client.

## Keep an inventory of what is published

Know every environment an API is exposed in, every version still reachable, and who calls each one.
Forgotten endpoints, older versions left running after a migration, and staging deployments reachable
from outside are surface nobody is maintaining — the reason this is a security concern as much as a
documentation one, per [security.md](security.md).

Usage telemetry per client is also what makes the deprecation process in
[evolution.md](evolution.md) possible. Without it, removal is a guess.
