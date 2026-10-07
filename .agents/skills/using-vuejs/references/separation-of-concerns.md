# Separation of concerns

## Put logic at its narrowest useful scope

| Logic | Default home | Escalate when |
|---|---|---|
| Markup and local UI state | Component | Another component needs the logic or state |
| Reusable reactive logic and cleanup | Composable | State must be shared across routes or app instances |
| Pure formatting, transformation, validation | Plain function | It actually needs reactive inputs or lifecycle |
| Cross-component application state | Pinia | It is server data needing cache/refetch semantics |
| Request/response transport | API function | A framework data layer already owns it |

Vue explicitly supports extracting composables for organization as components grow. Anthony Fu's
practitioner rule is “one thing at a time”: compose small connections instead of building one helper
that fetches, routes, toasts, and mutates shared state.

```ts
// Bad: presentation, transport, cancellation, and transformation are inseparable.
watch(query, async q => {
  users.value = (await (await fetch(`/api/users?q=${q}`)).json()).data
    .filter(user => user.active)
})

// Good: the component wires state to a focused, self-cleaning composable.
const { results, loading, error } = useUserSearch(query)
```

The split is earned when it exposes a reusable boundary, isolates a failure, or makes logic testable
without mounting. Do not create an API layer, store, or feature hierarchy for a small one-use
component merely to satisfy a folder pattern.

## Preserve determinism at the boundaries

- Render and computed paths derive output; they do not mutate state or start effects.
- Use stable list identity and one-way component communication.
- Declare watcher sources and teardown.
- Cancel or reject stale async results.
- Keep browser-only values and user-local time out of server render paths.
- Scope SSR state per request.

These are correctness constraints. File layout, component naming beyond the essential style rules,
and container/presentational splits are team conventions unless a demonstrated dependency problem
makes them necessary.
