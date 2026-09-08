# Inertia symptom map

Follow the linked owner instead of treating the likely cause as proven.

| Symptom | Inspect | Likely trap | Preferred direction |
|---|---|---|---|
| Unexpected or sensitive fields appear in the payload | Serialized page props | Whole model or collection passed through | Define a minimal resource, DTO, or `only(...)` shape in [`core.md`](core.md#make-the-prop-contract-public-on-purpose) |
| Filtering still runs unrelated queries | Server query log during an `only` reload | Expensive prop evaluated before Inertia can omit it | Wrap skippable work in closures; see [`data-loading.md`](data-loading.md#choose-prop-evaluation-deliberately) |
| Every navigation has a large repeated payload | Shared-data middleware | Page-specific or unshaped data shared globally | Move it to the owning page or shape a small global prop; see [`data-loading.md`](data-loading.md#keep-shared-data-small) |
| The previous user remains after logout | Once-prop response after auth changes | Conditional prop omitted rather than overwritten | Return explicit `null`; see [`data-loading.md`](data-loading.md#overwrite-conditional-once-props) |
| A toast reappears with Back | History state | One-time notification implemented as shared data | Use flash data; see [`core.md`](core.md#match-the-request-to-its-intended-response) |
| A mutation shows plain JSON or no page update | Request and response headers | Standalone HTTP used where an Inertia visit was intended, or the visit returned JSON | Use an Inertia form/router visit and redirect; see [`core.md`](core.md#match-the-request-to-its-intended-response) |
| Client-set errors disappear after reload | Partial reload options | Empty server `errors` prop replaced them | Use `preserveErrors` when that persistence is intended; see [`data-loading.md`](data-loading.md#make-partial-reload-assumptions-explicit) |
| New filtered results append to old results | Visit options for a merged prop | Collection was merged without reset | Add the prop to `reset`; see [`data-loading.md`](data-loading.md#bound-repeated-requests-and-merged-state) |
| Logout history reveals an earlier privileged page | History-encryption and logout path | History remained readable | Encrypt page history and clear it on logout; see [`core.md`](core.md#protect-history-and-deployed-assets) |
| Only some sessions run stale assets | Page version and full-visit behavior | Asset version missing, fixed manually, or only checked by background requests | Restore cache-busted versioning and test a user visit; see [`core.md`](core.md#protect-history-and-deployed-assets) |
| SSR tests pass but HTML is client-only | SSR logs and returned HTML | Silent client-render fallback hid an SSR exception | Throw on SSR errors in tests; see [`core.md`](core.md#make-ssr-failures-visible) |
| `window` or `document` is undefined in SSR | Import-time and setup code | Browser API evaluated on the server | Move it behind a client lifecycle boundary; see [`core.md`](core.md#make-ssr-failures-visible) |
| An example names a missing method or event | Both installed package versions | v2 advice applied to v3, or client/server versions conflated | Use the migration map and both changelogs in [`migration.md`](migration.md) |
