# Using Inertia Validation

This is the current validation record for `using-inertia`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for the Inertia protocol seam — page responses and props, visits and
forms, partial, optional, deferred, once, and shared data, authorization exposure, history,
assets, SSR, and adapter or major-version compatibility. It should not activate for a conventional
API SPA, Blade-only rendering, Livewire, or for Laravel, Vue, or React behavior that never crosses
the Inertia boundary.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| INR-T01 | Shape the props an Inertia page response returns | Load |
| INR-T02 | "The dashboard sends the whole user model to the browser — fix it" (Inertia never named) | Load |
| INR-T03 | Build a JSON REST endpoint consumed by a standalone SPA | Do not load |
| INR-T04 | Style a Vue single-file component with no server payload change | Do not load |
| INR-T05 | Add a Blade-rendered marketing page to a repository that also uses Inertia | Do not load |
| INR-T06 | Upgrade the Inertia client and Laravel adapter across a major version | Load |
| INR-T07 | Laravel controller plus Vue page across one Inertia response | Compose with `using-laravel` and `using-vuejs`; each keeps its own ownership |
| INR-T08 | Laravel controller plus React page across one Inertia response | Compose with `using-laravel` and `using-reactjs`; each keeps its own ownership |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| INR-W01 | A page is slow and one prop runs an expensive query | Wrap the skippable prop in a closure before relying on a partial reload, and distinguish reduced payload from reduced server work |
| INR-W02 | A mutation posts with `fetch` and the page does not update | Replace with an Inertia visit and a redirect response, without banning standalone HTTP where no page visit should occur |
| INR-W03 | The previous user remains visible after logout | Return an explicit `null` for the conditional once prop rather than omitting it |
| INR-W04 | An online example names `Inertia::lazy()` | Record both installed halves, then apply the v2→v3 rename map instead of assuming one version |
| INR-W05 | A permission prop hides a button | Require matching server-side authorization on the endpoint, and state that hiding UI is not enforcement |
| INR-W06 | "Inertia SSR works — the browser test is green" | Reject fluent assurance; require SSR-returned HTML with `throw_on_error` enabled in tests |
| INR-W07 | Adapter version cannot be determined from the repository | Inspect the client package and the server adapter separately, or stop and say which is unknown; do not assume a version |
| INR-W08 | Filters change on an infinitely scrolled collection and results append | Visit with `reset` for the merged prop, and prove it with the changed-filter path |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. Cases marked for the sampled run are INR-T01, INR-T03, INR-W06, INR-T07,
  and INR-T08.
- Codex: not run.

No case below is marked passed. Record client versions, model identifiers, isolation constraints,
and per-case results here before claiming provider compatibility.

## Limits

The composition cases INR-T07 and INR-T08 require `using-laravel`, `using-vuejs`, and
`using-reactjs` to be present in the same workspace. They test that ownership stays separate, not
that any package depends on another.
