# The UI component kit

49 component groups under `resources/js/components/ui/`, exported from one barrel and demonstrated by a
live showcase. It exists so a feature is assembled from parts that already handle their own states, not
rebuilt per page. **Search it before writing a component** — the usual failure here is not a bad component,
it is a second one.

## How it works

Three tiers, and dependencies point **inward only**:

| Tier | Directory | May know about | Import style |
|---|---|---|---|
| Kit | `components/ui/` | Nothing app-specific — no Inertia, auth, or routes | Barrel: `import { Button } from '@/components/ui'` |
| App | `components/app/` | Inertia, auth, routes; the kit | Direct file path |
| Site | `components/site/` | Public marketing surfaces; the kit | Direct file path |

- A shadcn/reka primitive is suffixed `*Base` (`ButtonBase`); the kit's own wrapper adds the project's
  props, defaults and states on top. Reach for the wrapper; drop to `*Base` only when the wrapper cannot
  express what you need.
- Kit components are `lang="ts"` with typed props. `app/` and `site/` are plain JS on purpose, which is why
  `noImplicitAny` is off — see [the troubleshooting guide](../guides/troubleshooting.md).
- Barrel imports are kit-only. Importing a name that the barrel does not export yields `undefined` at
  runtime rather than a build error, and the page renders nothing.

## Find one before building one

```bash
ls resources/js/components/ui/                       # 49 groups
grep -n "export" resources/js/components/ui/index.ts # what the barrel actually exposes
```

The showcase is the index: `routes/components.php` serves it, and the pages live in
`resources/js/pages/admin/components/`, grouped **actions · charts · data · display · forms**. Each page
renders every variant and state of its components, so it answers "does this already exist, and what can it
do" faster than reading source.

| Looking for | Group |
|---|---|
| Buttons, menus, dialogs, sheets, command palette | `actions/` |
| Area, bar, line, pie | `charts/` |
| Tables, pagination, row actions | `data/` |
| Cards, alerts, badges, tabs, tooltips, toasts, carousels | `display/` |
| Inputs, selects, comboboxes, dates, upload, editor | `forms/` |

## Configure — add or extend a component

1. **Search first.** The showcase group above, then the barrel. Extending an existing component beats a
   near-duplicate.
2. **Pick the tier by what it needs to know.** Needs a route, `usePage`, or the authenticated user? It is
   `app/`, not `ui/`. This is the rule most often broken, and it is what makes a kit unreusable.
3. **Build it in `ui/`** with `lang="ts"`, typed props, and `class?: HTMLAttributes['class']` — never
   `class?: string`, which rejects the object and array forms callers actually pass.
4. **Export it from the group's `index.ts` and the kit barrel**, in the same change.
5. **Add a showcase page** under the matching group and a route in `routes/components.php`, so the next
   person can find it without reading source.
6. **Cover behaviour in Vitest** — rendering plus interaction. Unmount anything that binds a `window` or
   `document` listener.

## How it fails

| Symptom | Cause |
|---|---|
| A page renders nothing, no build error | A name imported from the barrel that it does not export is `undefined`. Check `components/ui/index.ts`. |
| The same component exists twice with different props | The showcase was not searched first. Consolidate rather than adding a third. |
| A kit component imports `usePage`, `route()`, or a model | Tier violation — it belongs in `app/`. The kit stops being portable the moment it knows about the app. |
| A prop passed to a kit component is silently ignored | An explicit `:prop` before `v-bind="forwarded"` is overwritten by the forwarded set. Omit that key. |
| Passing `:class="{ active: x }"` is a type error | That component still declares `class?: string`. Widen it to `HTMLAttributes['class']`. |

## Verify

```bash
pnpm exec vue-tsc --noEmit                            # 0 — props and slots actually check
pnpm lint                                             # covers .js, .ts and .vue
php artisan route:list | grep components              # the showcase route for a new page exists
```
