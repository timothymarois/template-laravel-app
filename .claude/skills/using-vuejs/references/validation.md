# Using Vue Validation

This is the current validation record for `using-vuejs`; the repository-wide method is in
[Validating Skills](../../../docs/guides/validation.md).

## Boundary under test

The skill should activate for Vue 3 and Nuxt semantics — component contracts, reactivity and
watchers, composables, Pinia, Vue Router, SSR and hydration, rendering performance, and
Vue-focused tests. It should not activate for Vue 2, for React, for an Inertia protocol question
that never reaches Vue behavior, or for a non-Vue project that merely shares a build tool.

## Trigger and exclusion cases

| ID | Request shape | Expected behavior |
|---|---|---|
| VUE-T01 | Refactor a Vue 3 component whose store values stop updating | Load |
| VUE-T02 | "The cart total on screen never changes when I add an item" (Vue never named) | Load |
| VUE-T03 | A React component re-renders too often | Do not load |
| VUE-T04 | A Vite-built Svelte or vanilla-TypeScript project | Do not load |
| VUE-T05 | Upgrade a Vue 2 options-API application | Do not load; state that the scope is Vue 3 |
| VUE-T06 | Nuxt page fetches twice during hydration | Load |
| VUE-T07 | Vue page rendered through an Inertia response | Compose with `using-inertia`; Inertia owns the payload, this package owns component behavior |

## Workflow and authority cases

| ID | Request shape | Expected behavior |
|---|---|---|
| VUE-W01 | `const { total } = useCartStore()` renders a stale number | Replace with `storeToRefs()` and explain that destructuring reads a value rather than retaining the reactive connection |
| VUE-W02 | A `computed` sends an analytics event | Move the effect to a handler or watcher; explain that cached, lazy evaluation fires effects unpredictably |
| VUE-W03 | `watch(obj.count, ...)` never fires | Use a getter source; explain the number was read once instead of tracked |
| VUE-W04 | A composable is asked to use `defineModel`, `useId`, or `useTemplateRef` | Read `package.json` and the lockfile first; gate on Vue 3.4 or 3.5 rather than assuming |
| VUE-W05 | Module-scope user state on an SSR route | Create per-request state; explain the cross-request leak |
| VUE-W06 | "The refactor is done and it looks right" | Reject fluent assurance; require rendered output, emitted events, cleanup, and the original reproduction |
| VUE-W07 | Installed Vue version cannot be determined | Inspect or stop and name the unknown; do not recommend a version-gated API on assumption |
| VUE-W08 | A component test asserts internal `setup` state | Move assertions to DOM and emitted events, and label the change a preference rather than a defect where correctness is unaffected |

## Provider evidence

Last verification: not yet run against a live provider matrix.

- Claude Code: pending. Cases marked for the sampled run are VUE-T01, VUE-T03, VUE-W06, and VUE-T07.
- Codex: not run.

No case below is marked passed. Record client versions, model identifiers, isolation constraints,
and per-case results here before claiming provider compatibility.

## Limits

VUE-T07 requires `using-inertia` in the same workspace. It tests that ownership stays separate, not
that either package depends on the other. No case exercises a live Nuxt server; SSR cases are
graded on the decision and the proof demanded, not on a running process.
