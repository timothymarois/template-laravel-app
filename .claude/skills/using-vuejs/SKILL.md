---
name: using-vuejs
description: Use when building, reviewing, debugging, or refactoring a Vue 3 or Nuxt application, including components and props, reactivity and watchers, composables, Pinia state, Vue Router, SSR and hydration, rendering performance, and Vue-focused tests. It supplies source-backed defaults and community-solved traps that keep state ownership, reactive dependencies, and effect lifetimes predictable. Do not use for Vue 2, for React work, or for a non-Vue project merely because it uses Vite.
---

# Use Vue and Nuxt

Make state ownership, reactive dependencies, and effect lifetimes explicit.

## Check the installed versions

Read `package.json` and the lockfile before using version-gated APIs. Confirm the resolved versions
when dependencies are installed:

```sh
npm ls vue pinia vue-router nuxt 2>/dev/null
```

Do not recommend a prerelease feature as a production default. Record any version assumption in the
answer. In particular, `defineModel` became stable in Vue 3.4; `useId`, `useTemplateRef`, numeric
watcher depth, `onWatcherCleanup`, and `data-allow-mismatch` require Vue 3.5.

## Triage before refactoring

1. Read the template and script together.
2. Reproduce the symptom; distinguish stale state, an extra effect, a render mismatch, and slowness.
3. Repair lost reactivity or unsafe effects before reorganizing files.
4. Preserve ownership: props down, events up; local state stays local; shared state gets an explicit
   app or store scope.
5. Verify through rendered output, emitted events, cleanup, and the original reproduction.

## Defaults and their failure modes

| Avoid | Prefer | Failure prevented |
|---|---|---|
| Destructuring a reactive object or Pinia store | `toRefs()` or `storeToRefs()` | Values disconnect at setup |
| Prop or nested prop mutation | Emit the proposed value; let the owner update | Hidden two-way state flow |
| Side effects in `computed` | Event handlers or watchers | Cached/lazy evaluation fires effects unpredictably |
| `watch(obj.count, ...)` | `watch(() => obj.count, ...)` | A number is read once instead of tracked |
| Watchers created in async callbacks | Create synchronously; make the body conditional | The watcher is not auto-stopped on unmount |
| Uncancelled requests or listeners | Register teardown with watcher or component cleanup | Stale writes and retained effects |
| Module-scope user state during SSR | Create state per request; use Pinia or app `provide` | One request leaks state into another |
| Bare `$fetch` in Nuxt setup | `useFetch` or `useAsyncData` | Server fetch repeats during hydration |
| Internal-state assertions | Assert DOM and emitted events | Tests break on harmless refactors |

## Read only the needed reference

- Component contracts, list identity, models, slots, provide/inject, and styling:
  [components.md](references/components.md)
- Lost updates, watchers, cleanup, and flush timing: [reactivity.md](references/reactivity.md)
- Reusable stateful logic and call-site rules: [composables.md](references/composables.md)
- Placement and dependency boundaries: [separation-of-concerns.md](references/separation-of-concerns.md)
- Pinia and Router traps: [state-and-routing.md](references/state-and-routing.md)
- Profile-led rendering and load work: [performance.md](references/performance.md)
- Hydration, request isolation, and Nuxt fetching: [ssr-and-nuxt.md](references/ssr-and-nuxt.md)
- Public-behavior tests and teardown: [testing.md](references/testing.md)
- Claim-to-source audit map: [sources.md](references/sources.md)

## Report findings as evidence

```text
[HIGH] Store state disconnected at setup
Location: src/components/CartSummary.vue:14
Evidence: const { total } = useCartStore()
Why: destructuring reads the current property value; it does not retain Pinia's reactive connection.
Fix: const { total } = storeToRefs(useCartStore())
Check: mutate the store and assert the rendered total changes.
```

Call an error-prevention rule a defect. Label style-guide preferences and local structure choices as
preferences, not correctness failures.
