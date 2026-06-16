# Migrating a fork to template v5.1.1

v5.1.1 replaces the unmaintained **`vuedraggable@4`** with **`vue-draggable-plus`**. Patch — no schema, API, or Docker changes. It fixes an Inertia **SSR crash** on any page that mounts a `<draggable>` and removes the `vite.config.js` SSR special-casing that worked around it.

- **Every fork:** swap the dependency + clean `vite.config.js` (~3 min, mechanical).
- **Forks that use `<draggable>`** (ship `CustomizeColumns.vue`, or any other draggable component): also migrate those components to the `vue-draggable-plus` API (~10 min).

> **Why this matters:** `vuedraggable@4` ships UMD-only and does `require('vue')`. When the SSR build externalizes it, Rollup emits a default import of Vue's ESM (which has no default export) → the SSR server throws `The requested module 'vue' does not provide an export named 'default'` and returns a 500 on every page with a draggable. `vue-draggable-plus` is the maintained Vue 3 successor on the same Sortable.js engine, ships proper ESM/CJS, and externalizes cleanly under SSR.

## Prerequisites

- Fork is on template **v5.1.0** (apply earlier migrations first). Check: `jq -r .version template-manifest.json` → expect `5.1.0`.
- Baseline `pnpm check` is green.

---

## Part A — Swap the dependency (every fork)

```sh
pnpm remove vuedraggable
pnpm add vue-draggable-plus
```

---

## Part B — Clean `vite.config.js` (every fork)

Remove the vuedraggable-specific SSR special-casing. Delete the `ssr.external` entry for `vuedraggable` and drop `vuedraggable` from `optimizeDeps.include`:

```diff
     optimizeDeps: {
-        include: ['vuedraggable'],
         exclude: ['vue'],
     },
-    ssr: {
-        external: ['vuedraggable'],
-    },
     test: {
```

**Keep** `resolve.dedupe: ['vue']` and `optimizeDeps.exclude: ['vue']`.

> If your fork had instead band-aided this with `ssr: { noExternal: true }` (bundling every dep into the SSR build), remove that too — with `vuedraggable` gone, normal externalization works again, so the heavy bundling and any matching `NODE_OPTIONS=--max-old-space-size` build-time heap bump are no longer required. Re-verify the SSR build after reverting; if some *other* externalized UMD dep surfaces the same error, fix it targeted (add just that package to `ssr.external`'s replacement or `optimizeDeps`), not with a global `noExternal`.

---

## Part C — Migrate draggable components (only forks that use `<draggable>`)

Find them: `grep -rl "vuedraggable\|<draggable" resources/js`. The template ships exactly one — `resources/js/components/ui/data-table/CustomizeColumns.vue`. Apply this mapping to each:

| vuedraggable | vue-draggable-plus |
|---|---|
| `import draggable from 'vuedraggable'` | `import { VueDraggable } from 'vue-draggable-plus'` |
| `<draggable>` … `</draggable>` | `<VueDraggable>` … `</VueDraggable>` |
| `<template #item="{ element }">…</template>` (scoped slot) | default slot with a `v-for` over the same v-model list inside the root |
| `item-key="id"` | `:key="element.id"` on the `v-for` element |
| `:move="fn"` (validation callback) | `@move="fn"` — the `onMove` prop; its `false` return still rejects the move |
| props `handle`, `group`, `disabled`, `ghost-class`, `chosen-class`, `drag-class`, `:animation`, `:filter`, `v-bind="dragOptions"` | unchanged (both wrap Sortable.js) |
| events `@start`, `@end`, `@choose` | unchanged |

**Slot API:** vuedraggable rendered items through the `#item` scoped slot; vue-draggable-plus uses the **default slot with a `v-for`** — the direct children of `<VueDraggable>` become the sortable items. Move the per-item markup out of `#item` into a `v-for` keyed by id, and **keep v-model as the single source of truth for order** (its internal `onUpdate` mutates the list before your `@end` fires).

**Move-callback gotcha:** vuedraggable's `:move` callback received `{ draggedContext, relatedContext }` (data wrappers). vue-draggable-plus's `onMove` (`@move`) receives the **raw Sortable.js `MoveEvent`** (`evt.dragged`, `evt.related`, `evt.to`, `evt.from` — DOM nodes). Resolve your data from the DOM node (e.g. a `data-*` attribute on the item). In `CustomizeColumns.vue` the lock guard becomes:

```ts
// add :data-column-key="column.key" to the draggable item div, then:
const checkLocked = (evt: any) => {
    const draggedKey = (evt?.dragged as HTMLElement | undefined)?.dataset?.columnKey;
    const relatedKey = (evt?.related as HTMLElement | undefined)?.dataset?.columnKey;
    const dragged = selectedColumns.value.find(column => column.key === draggedKey);
    const related = selectedColumns.value.find(column => column.key === relatedKey);
    return !dragged?.locked && !related?.locked;
};
```

**Tests:** any test stubbing the draggable as a named `draggable` slot must update the stub key to `VueDraggable` and render the **default** slot (vue-draggable-plus has no `#item` slot).

---

## Verify

```sh
pnpm check                       # ESLint, Stylelint, tsc, Vitest, builds — all green
pnpm build-ssr && node bootstrap/ssr/ssr.js   # expect "Inertia SSR server started", no crash; Ctrl-C
grep -rn 'from "vue"' bootstrap/ssr/ | grep -i 'require\$\$0'   # expect ZERO
```

Then **manually drag-and-drop every surface** that uses a draggable — reorder must work AND persist, and locked/grouped/disabled states must behave exactly as before.

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.1.1"` and copy this changelog entry so the fork records the version it's on.
