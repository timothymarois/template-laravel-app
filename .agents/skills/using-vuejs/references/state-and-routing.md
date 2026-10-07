# State and routing

## Escalate state only when ownership widens

| Owner or lifetime | Use |
|---|---|
| One component | Local `ref` |
| Parent and children | Props down, events up |
| Reusable stateful behavior | Composable; each call normally owns an instance |
| One subtree | Typed provide/inject |
| Shared across routes, needs devtools or SSR integration | Pinia |
| Bookmarkable filters, sort, page, or selected tab | Route params or query |
| Server data needing cache, invalidation, and refetch | Nuxt data composables or a query layer |

## Pinia traps

```ts
// Bad: values are disconnected by destructuring.
const { items, total } = useCartStore()

// Good: state/getters become refs; actions can be read from the store directly.
const cart = useCartStore()
const { items, total } = storeToRefs(cart)
const { add } = cart
```

- Outside a component, call `useStore()` only after Pinia is installed, or pass the Pinia instance
  explicitly. SSR code must pass the request's instance to prevent cross-request sharing.
- Setup stores must implement their own `$reset`; Pinia supplies automatic `$reset` only for option
  stores.
- Setup stores may call another store at the top of the store function. The actual circularity trap is
  two stores synchronously reading each other's state during setup. Move mutual reads into computed
  values or actions. In async actions, call other stores before the first `await` in SSR.

Pinia 4 is ESM-only and requires `@vue/devtools-api` alongside Pinia. Treat that as installation
compatibility, not a reason to rewrite store APIs.

## Router traps

Vue Router reuses a component when only params change, so mount hooks do not run again:

```ts
const route = useRoute()
watch(() => route.params.id, loadOrder, { immediate: true })
```

Watch only the route property that drives the effect, not the whole reactive route object. A normal
param is a string; optional params may be empty and repeatable params can be arrays, so validate and
coerce at the boundary instead of asserting every param is a string.

Use `props: true` when a route view can accept params as component inputs; this removes direct router
coupling from that component. Lazy-load route components with dynamic imports.

Prefer guards that return a location or `false`; the legacy `next` form remains supported but is easy
to call twice. Client guards improve navigation, not authorization—the server still authorizes.
Fetching before or after navigation are both documented choices; choose based on the intended loading
experience instead of declaring all data fetching in guards wrong.

Vue Router 5 merged typed routing from `unplugin-vue-router` without breaking the core v4 API. Check
the installed major before giving migration advice.
