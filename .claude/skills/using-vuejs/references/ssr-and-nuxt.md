# SSR and Nuxt

## Diagnose hydration instead of suppressing it

| Symptom source | Cause | Preferred replacement |
|---|---|---|
| Invalid nesting | The browser repairs server HTML before hydration | Fix the markup |
| Random values | Server and client render different values | Serialize a seed, render client-only, or use `useId` for ids |
| User-local date/time | Server and browser timezones differ | Render the local form after mount |
| Browser globals in setup | They do not exist on the server | Read them in `onMounted` or a client-only abstraction |
| Media query swaps the initial root or wrapper | The server fallback and first client render differ | Keep the initial tree stable; use CSS or enhance after mount |

Vue recovers from many mismatches by discarding and mounting nodes, which costs work and may hide the
wrong output. Vue 3.5's `data-allow-mismatch` is for mismatches that are truly inevitable, not a first
response. Nuxt's `<ClientOnly>` is likewise a boundary for a genuinely client-only widget.

A stable outer root limits the blast radius but does not make a mismatched child tree correct.
`data-allow-mismatch` is not a substitute for making client-only responsive structure predictable.

## Create request-local state

```ts
// Bad: this singleton survives across server requests.
export const currentUser = ref<User | null>(null)

// Good: create the app, router, and state for each request.
export function createApp() {
  const app = createSSRApp(App)
  const store = createStore()
  app.provide(StoreKey, store)
  return { app, store }
}
```

Vue documents module-scope SSR state as a cross-request data leak. Pinia is designed for request
scoping, but its SSR integration still must pass the correct Pinia instance.

Do not start cleanup-dependent effects in `setup`, `created`, or module scope during SSR. Mount hooks
do not run on the server, and unmount hooks will never clear that server-side timer or subscription.
Move browser effects to `onMounted`.

## Avoid Nuxt's double-fetch trap

```ts
// Bad in component setup: server request repeats during hydration.
const users = await $fetch('/api/users')

// Good: Nuxt transfers the server result in its payload.
const { data: users } = await useFetch('/api/users')
```

Use `useAsyncData(key, handler)` for custom async logic. In component setup, use `useFetch` or
`useAsyncData` whenever the server result must transfer to hydration; bare `$fetch` remains suitable
when that transfer is unnecessary, such as an event handler. When calls intentionally share a key,
keep `handler`, `deep`, `transform`, `pick`, `getCachedData`, and `default` consistent; Nuxt warns when
they differ. Same URLs at different `useFetch` call sites have different generated keys, so pass the
same explicit key when the data should be shared.

Private runtime-config keys remain server-only; `runtimeConfig.public` is the application namespace
for values intentionally exposed to the client. Consume credentials from server routes; never move a
secret under `public` to make browser code see it.
