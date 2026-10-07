# Performance

## Profile the failing dimension

Use PageSpeed Insights or WebPageTest for load behavior. Use the browser performance panel with
`app.config.performance = true` and Vue DevTools for component updates. Do not apply `v-memo`, flatten
components, or change reactivity from a guess.

## Keep child props stable

Vue's performance guide gives this exact trap: passing `activeId` makes every row receive a changed
prop; passing the derived boolean updates only rows whose active state changed.

```vue
<!-- Bad -->
<ListItem v-for="item in items" :key="item.id" :id="item.id" :active-id="activeId" />

<!-- Good -->
<ListItem v-for="item in items" :key="item.id" :id="item.id" :active="item.id === activeId" />
```

Do not generalize this into premature prop reshaping. Confirm the child is updating in the profiler;
reorder the list and verify row-local state or focus stays with the same `item.id`.

## Remove work at the right boundary

- Lazy-load route components with dynamic imports; Vue Router strongly recommends this split.
- Lazy-load other heavy, non-initial component trees with `defineAsyncComponent`.
- Virtualize lists large enough that DOM-node count is the measured bottleneck.
- For large immutable nested data, `shallowRef` avoids deep proxy work, but nested mutation no longer
  triggers updates; replace the root.
- Use `v-once` only for content that never changes. Use `v-memo` only after profiling and include every
  dependency; an incomplete list creates stale UI.

Do not remove a handful of component abstractions as a generic optimization. Vue says that reduction
is normally unnoticeable; it matters when multiplied across large lists.

For load-sensitive content, do not assume a client-only SPA is the right delivery model. Vue's guide
recommends SSR, SSG, or server-rendered HTML when time-to-content dominates.
