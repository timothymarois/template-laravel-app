# Reactivity

## Start with `ref`

Vue recommends `ref()` as the primary state API because `reactive()` only accepts objects, cannot be
replaced without losing the original connection, and loses primitive-property connections when
destructured or passed by value. Use `reactive()` deliberately for a stable object or collection.

```ts
// Bad: count is a number captured at setup.
const state = reactive({ count: 0 })
const { count } = state

// Good: the returned ref stays connected.
const { count } = toRefs(state)
```

The same trap applies to Pinia (`storeToRefs`) and prop snapshots (`computed(() => props.value)`).

## Derive with pure `computed`

Do not mutate state, navigate, fetch, or emit from a computed getter. `eslint-plugin-vue` treats
side effects there as an essential-rule violation because lazy caching makes their timing hard to
predict. Use `computed` for a value, a watcher for a reactive effect, and an event handler for a user
action. A writable computed setter remains appropriate for a two-way binding bridge.

## Watch the actual source

```ts
// Bad: obj.count is evaluated before watch receives it.
watch(obj.count, load)

// Good: Vue can track the getter.
watch(() => obj.count, load)
```

Prefer `watch` when the dependency should be explicit or the previous value matters. `watchEffect`
tracks reactive reads only during its synchronous run; reads after the first `await` are not tracked.
Watch a specific getter instead of a whole reactive object. Deep traversal can be expensive; Vue 3.5+
allows a numeric depth bound.

## Cancel invalidated work

An earlier request can finish after a later one and overwrite newer state. Register cancellation
before any `await`:

```ts
watch(id, async (nextId, _oldId, onCleanup) => {
  const controller = new AbortController()
  onCleanup(() => controller.abort())
  const response = await fetch(`/api/items/${nextId}`, { signal: controller.signal })
  item.value = await response.json()
})
```

`onWatcherCleanup` expresses the same pattern in Vue 3.5+, but must run synchronously. The positional
`onCleanup` callback is bound to the watcher and is not subject to that constraint.

Create watchers synchronously so Vue stops them at unmount. A watcher created in `setTimeout` or an
async callback is not owner-bound; keep its stop handle or create it synchronously with conditional
logic.

## Choose flush timing deliberately

- Default `pre`: effects that do not read the component's updated DOM.
- `post`: DOM measurement or focus after Vue patches.
- `sync`: only for a simple source that cannot be mutated repeatedly; it is unbatched and fires on
  every mutation.

Use `shallowRef` only for large immutable structures or external-state objects where root replacement
is the intended update. Mutating nested data will not trigger an update.
