# Vue and Nuxt source map

Use this map to audit a lesson, not as another tutorial. Framework docs establish behavior; official
lint rules show automated failure prevention; maintainer and practitioner reports supply field-tested
traps and replacements. Verified 7 August 2026.

## Version boundary

The npm registry reported Vue 3.5.41, Pinia 4.0.2, Vue Router 5.2.0, Nuxt 4.5.2, Vite 8.2.1, and
Vue Test Utils 2.4.11 on the verification date. This is a snapshot; inspect the target lockfile and
re-check registry dist-tags before making current-version claims.

- [Vue releases](https://github.com/vuejs/core/releases) and
  [release policy](https://vuejs.org/about/releases): stable versus prerelease status.
- [Pinia 4.0.0](https://github.com/vuejs/pinia/releases/tag/v4.0.0): ESM-only package and separate
  `@vue/devtools-api` installation.
- [Vue Router 5.0.0](https://github.com/vuejs/router/releases/tag/v5.0.0): typed-router merge, core API
  compatibility with v4, and IIFE devtools exception.
- [Nuxt 4 upgrade](https://nuxt.com/docs/4.x/getting-started/upgrade): Nuxt 4 boundaries.

## Reactivity and watchers

- [Reactivity fundamentals](https://vuejs.org/guide/essentials/reactivity-fundamentals.html): `ref` as
  primary API; `reactive` value-type, replacement, and destructuring limits. This proves the
  disconnected-destructure good/bad pair and why a ref-returning composable's mock must preserve the
  mutable `.value` contract.
- [Watchers](https://vuejs.org/guide/essentials/watchers.html): valid sources, async dependency
  tracking, deep-watch cost, flush timing, stale-request cleanup, Vue 3.5 `onWatcherCleanup`, and the
  unowned async-watcher leak. This proves the getter, cancellation, and sync-creation pairs.
- [`vue/no-side-effects-in-computed-properties`](https://eslint.vuejs.org/rules/no-side-effects-in-computed-properties):
  side effects make computed behavior unpredictable; the rule is in Vue 3 essential configs.
- [Bryce Andy, “The Hidden Reason Your Vue Watchers Leak Memory”](https://www.bryceandy.com/posts/the-hidden-reason-your-vue-watchers-leak-memory-and-how-to-avoid-it):
  production symptoms and reproduced fixes for retained closures, uncancelled requests, and stacked
  listeners. Practitioner evidence for making cleanup part of each effect.

## Components

- [Props](https://vuejs.org/guide/components/props.html): one-way flow, nested object mutation caveat,
  and local-copy/computed replacements.
- [Events](https://vuejs.org/guide/components/events.html): declaration and listener fallthrough.
- [Component `v-model`](https://vuejs.org/guide/components/v-model.html): `defineModel` version and
  child-default de-synchronization warning.
- [List rendering](https://vuejs.org/guide/essentials/list.html#maintaining-state-with-key) and
  [Priority A style rules](https://vuejs.org/style-guide/rules-essential): in-place patching, stable
  keys, `v-if`/`v-for`, detailed props, component names, and scoped-style exceptions. These prove the
  retained list good/bad pairs.
- [Provide/inject](https://vuejs.org/guide/components/provide-inject.html): symbol keys, readonly
  values, and keeping mutations with the provider.
- [Priority D style rules](https://vuejs.org/style-guide/rules-use-with-caution): class selectors in
  scoped CSS and explicit parent-child communication.

## Composables and effect lifetimes

- [Vue composables](https://vuejs.org/guide/reusability/composables.html): reactive input tracking,
  plain object-of-refs returns, SSR-safe effects, cleanup, synchronous call sites, and the compiled
  post-`await` exception. This proves both composable good/bad pairs.
- [Anthony Fu, “Composable Vue”](https://antfu.me/posts/composable-vue-vueday-2021), VueDay 2021:
  lessons learned building VueUse—small concerns, flexible ref inputs, object-of-refs returns,
  self-cleaning effects, typed injection, and per-app shared state.
- [VueUse guidelines](https://vueuse.org/guidelines): maintained library conventions for refs,
  shallow refs over large data, configurable globals, and scope disposal.

## Pinia and Vue Router

- [Pinia outside components](https://pinia.vuejs.org/core-concepts/outside-component-usage.html): calls
  after installation, deferred calls in guards, and explicit request Pinia for SSR.
- [Pinia composing stores](https://pinia.vuejs.org/cookbook/composing-stores.html): supported top-level
  setup-store composition, the mutual setup-read loop, and pre-`await` store access in SSR actions.
- [Pinia state](https://pinia.vuejs.org/core-concepts/state.html) and
  [`storeToRefs`](https://pinia.vuejs.org/api/pinia/functions/storeToRefs.html): setup-store reset and
  safe state/getter destructuring.
- [Eduardo San Martin Morote, “Top 5 mistakes to avoid when using Pinia”](https://masteringpinia.com/blog/top-5-mistakes-to-avoid-when-using-pinia),
  2022: Pinia author's field guidance on app context, reactive replacement, URL state, and SSR.
- [Dynamic params](https://router.vuejs.org/guide/essentials/dynamic-matching.html) and
  [Router Composition API](https://router.vuejs.org/guide/advanced/composition-api.html): component
  reuse, targeted route watching, and repeatable-param arrays.
- [Route props](https://router.vuejs.org/guide/essentials/passing-props.html),
  [lazy loading](https://router.vuejs.org/guide/advanced/lazy-loading.html),
  [guards](https://router.vuejs.org/guide/advanced/navigation-guards.html), and
  [data fetching](https://router.vuejs.org/guide/advanced/data-fetching.html): decoupled views, route
  splits, return-based guards, and valid before/after-navigation fetching.

## SSR and Nuxt

- [Vue SSR](https://vuejs.org/guide/scaling-up/ssr.html): lifecycle behavior, browser globals,
  cross-request state pollution, hydration causes and recovery cost, and `data-allow-mismatch`.
- An anonymized production regression, reproduced and audited 7 August 2026, found that a pointer
  media query swapped a Vue component root after SSR and made every touch-mode card disappear. A
  stable root plus a compatible initial tree fixed the failure. This field evidence is scoped to
  client-only responsive structure; Vue's SSR docs define the public contract.
- [`useId`](https://vuejs.org/api/composition-api-helpers.html#useid): app-stable ids that avoid SSR
  hydration mismatches.
- [Nuxt data fetching](https://nuxt.com/docs/4.x/getting-started/data-fetching): bare `$fetch` double
  fetch, payload transfer, generated and shared keys, and option-consistency warnings. This proves the
  Nuxt good/bad pair.
- [Nuxt runtime config](https://nuxt.com/docs/4.x/guide/going-further/runtime-config): private server
  values and client-exposed `public` values.

## Performance

- [Vue performance](https://vuejs.org/guide/best-practices/performance.html): profiling tools, delivery
  architecture, route/component splitting, the exact prop-stability pair, virtualization, shallow
  reactivity trade-off, memo directives, and the warning against removing a few abstractions.

## Separation and determinism

- [Vue composables—organization](https://vuejs.org/guide/reusability/composables.html#extracting-composables-for-code-organization):
  extraction by logical concern when components become hard to reason about.
- Anthony Fu's “Composable Vue” above supplies the one-concern and composition judgment. The layer
  table is this catalog's scoped synthesis of those sources, not a Vue-mandated folder structure.
- Reactivity, component, watcher, and SSR sources above prove the deterministic boundary rules. File
  layout and container/presentational splits were deliberately omitted as universal requirements.

## Testing

- [Vue testing](https://vuejs.org/guide/scaling-up/testing.html): test public behavior, avoid private
  state/method assertions and snapshot-only suites, minimize component stubbing, and use Vitest with
  Vite. The component good/bad pair is a minimized form of this documented guidance.
- [Vue Test Utils async behavior](https://test-utils.vuejs.org/guide/advanced/async-suspense.html): await
  Vue updates before assertions.
- The same anonymized regression above found that a plain `false` mock replaced a composable's
  `Ref<boolean>`, making the touch branch impossible to exercise. Returning a mutable `ref`, flipping
  it per case, and asserting rendered output exposed the missing branch. Vue's reactivity source
  defines the return contract; this field reproduction supplies the failure and replacement.
- [Pinia testing](https://pinia.vuejs.org/cookbook/testing.html): unit and component-store setup.
- [`eslint-plugin-vue` rules](https://eslint.vuejs.org/rules/): automated essential-rule coverage.

## Deliberately omitted

- API inventories and general Vue/Nuxt setup documentation.
- Unmeasured performance rankings, arbitrary list-size thresholds, and prerelease performance claims.
- Folder structures or state-scope preferences presented as framework requirements.
- Advice whose source only proves that an API exists, without a failure or preferred replacement.

## Attribution

This package adapts `skills/vue-patterns/` from the Rundesk skills catalog at
<https://github.com/rundesk-ai/rundesk-skills>, commit
`680e3d720547dbb563e6e15808e15c8f5bdd4083`, published by Rundesk AI under the MIT License.
