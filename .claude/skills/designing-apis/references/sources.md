# API design source basis

Use this mapping to verify a rule before changing it. IETF specifications establish HTTP semantics
and the wire formats; the industry guidelines are consulted where they agree and are labelled as
conventions rather than requirements where they differ. Every link below was opened and checked on
23 August 2026.

## HTTP semantics

- [RFC 9110, HTTP Semantics](https://www.rfc-editor.org/rfc/rfc9110.html) is the current
  specification and obsoletes RFC 7231. Its
  [safe methods](https://www.rfc-editor.org/rfc/rfc9110.html#section-9.2.1) and
  [idempotent methods](https://www.rfc-editor.org/rfc/rfc9110.html#section-9.2.2) sections establish
  the method-properties table in `resources-and-methods.md`: `GET`, `HEAD`, `OPTIONS`, and `TRACE`
  are safe; those plus `PUT` and `DELETE` are idempotent. The safe-method definition is the direct
  source of the rule that a state change must not sit behind `GET`, because automatic agents may
  issue safe requests without user intent.
- The same document defines the status codes used in that file's table, including `201` with
  `Location`, `202`, `204`, the `400`/`422` distinction between unparseable and semantically
  invalid, `401` versus `403`, `409`, `412`, and `415`.
- [RFC 9110 §13, Conditional Requests](https://www.rfc-editor.org/rfc/rfc9110.html#section-13)
  establishes `ETag`, `If-Match`, `If-None-Match`, and the `412` response. It supports the lost-update
  protection in `reliability.md`, including that a weak validator cannot distinguish two changes it
  cannot resolve.
- [RFC 5789](https://www.rfc-editor.org/rfc/rfc5789.html) defines `PATCH` and states it is neither
  safe nor idempotent, which is why `reliability.md` sends clients to idempotency keys rather than
  claiming the method provides repeatability.
- [RFC 6585](https://www.rfc-editor.org/rfc/rfc6585.html) defines `428 Precondition Required` and
  `429 Too Many Requests`, and recommends `Retry-After` with `429`.
- [RFC 9111, HTTP Caching](https://www.rfc-editor.org/rfc/rfc9111.html) establishes what
  intermediaries may do with a response, which is the concrete cost of signalling failure with `200`.

## Formats

- [RFC 9457, Problem Details for HTTP APIs](https://www.rfc-editor.org/rfc/rfc9457.html) defines the
  `application/problem+json` media type and the `type`, `title`, `status`, `detail`, and `instance`
  members, and permits extension members. It is the source of the error object in
  `payloads-and-errors.md`. It obsoletes [RFC 7807](https://www.rfc-editor.org/rfc/rfc7807.html),
  which is retained here only so older references to 7807 can be recognized as superseded.
- [RFC 8259](https://www.rfc-editor.org/rfc/rfc8259.html) defines JSON and permits any value at the
  top level. The top-level-object rule in `payloads-and-errors.md` is therefore an extensibility
  convention of this package, not a format requirement.
- [RFC 6902 JSON Patch](https://www.rfc-editor.org/rfc/rfc6902.html) and
  [RFC 7396 JSON Merge Patch](https://www.rfc-editor.org/rfc/rfc7396.html) define the two standard
  patch documents. Merge Patch's use of `null` to remove a member is the documented basis for
  requiring an API to say what absent and null mean on `PATCH`.
- [RFC 8288, Web Linking](https://www.rfc-editor.org/rfc/rfc8288.html) defines the `Link` header and
  relations such as `next` and `prev`, one standard way to convey pagination links.
- [RFC 9745](https://datatracker.ietf.org/doc/html/rfc9745) defines the `Deprecation` header field
  for signalling that a resource is deprecated, used in `evolution.md`.
- The [Idempotency-Key header field draft](https://datatracker.ietf.org/doc/html/draft-ietf-httpapi-idempotency-key-header)
  describes the mechanism in `reliability.md`. It is an Internet-Draft, not a published standard —
  the header is widely deployed by convention, and this package presents it as a convention.

## Contracts and documentation

- The [OpenAPI Specification](https://spec.openapis.org/oas/latest.html) is the machine-readable
  description format referenced in `contracts-and-docs.md`.
- [Semantic Versioning](https://semver.org/) supplies the major/minor/patch meaning used in
  `evolution.md`. Applying it to an HTTP API surface is a convention; SemVer specifies versioning for
  software packages.

## Security

- The [OWASP API Security Top 10 (2023)](https://owasp.org/API-Security/editions/2023/en/0x11-t10/)
  is the source of `security.md`'s structure. API1 Broken Object Level Authorization supports the
  per-object check; API3 Broken Object Property Level Authorization supports explicit representations
  and writable sets, covering both excessive data exposure and mass assignment; API4 Unrestricted
  Resource Consumption supports the bounding rules; API5 Broken Function Level Authorization supports
  checking permissions on administrative endpoints; API6 Unrestricted Access to Sensitive Business
  Flows supports treating volume limits as design; API7 Server Side Request Forgery supports the
  caller-supplied-URL rules; API9 Improper Inventory Management supports the inventory requirement;
  and API10 Unsafe Consumption of APIs supports treating third-party responses as untrusted.

## Industry conventions

These are widely used house styles, not standards. They are cited where this package follows a
convention rather than a specification, and they do not always agree with each other.

- [Google API Improvement/Design Guide](https://cloud.google.com/apis/design) and the
  [Microsoft Azure REST API Guidelines](https://github.com/microsoft/api-guidelines/blob/vNext/azure/Guidelines.md)
  both support resource-oriented design, consistent collection parameters, and long-running
  operations modelled as their own resource.
- The [Zalando RESTful API Guidelines](https://opensource.zalando.com/restful-api-guidelines/)
  supply the tolerance contract — clients ignore unknown fields — and treat adding an enum value to a
  field clients switch on as a compatibility hazard. That distinction is the source of the nuanced
  "additive is not automatically safe" rule in `evolution.md`.
- [JSON:API](https://jsonapi.org/format/) is one fully specified envelope and pagination convention.
  This package does not require it; it is cited as evidence that a documented envelope is a
  recognized solution to the top-level-array problem.
- [MDN's HTTP status reference](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status)
  and [conditional requests guide](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Conditional_requests)
  are used as accessible restatements of the RFC behavior, not as the authority for it.

## Catalog conclusions

These are this package's judgments, not claims made by the sources above:

- The six-step working order in `SKILL.md` is a local method.
- Requiring a top-level JSON object is an extensibility convention; JSON permits otherwise, and
  JSON:API is cited as one standardized instance of the same conclusion.
- Preferring keyset pagination by default, and treating offsets as the deliberate exception, is a
  local ranking of two legitimate techniques.
- Treating the endpoint inventory as a security control as much as a documentation one follows from
  OWASP API9 but is stated more strongly here.
- "Follow the repository's existing convention unless it causes a defect" is this catalog's rule,
  applied to APIs because inconsistency is more expensive for clients than imperfection.

Omitted on purpose: undated blog posts restating REST maturity models, guidance that presents one
vendor's house style as a standard, and advice about GraphQL, gRPC, or event-stream schema design,
which are outside this package's boundary.

## Attribution

This package was written from the specifications above rather than migrated from an existing
package, with one exception: the rules in `naming.md` are adapted from the naming and grammar
guidance in the Rundesk skills catalog at <https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, path `skills/naming-grammar-conventions/`, published by
Rundesk AI under the MIT License.

Its REST path, API field, error code, and published-event sections are carried into this package,
rewritten for an API-design audience, so that an API can be named correctly without a second package
installed. The naming skill's wider scope — code identifiers, log keys, interface copy, and the
product lexicon — is deliberately not reproduced here; it belongs to that package.
