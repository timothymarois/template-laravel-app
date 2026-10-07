# Components

## Keep ownership visible

Declare typed or runtime-validated props and emits. Props are read-only bindings; nested objects are
still mutable JavaScript references, so Vue cannot stop a child from changing parent-owned data.

```vue
<!-- Bad: the owner cannot see where its object changed. -->
<input v-model="todo.text">

<!-- Good: the child proposes a replacement; the parent owns the write. -->
<input :value="todo.text"
       @input="$emit('update:todo', { ...todo, text: $event.target.value })">
```

For an initial value, copy once into a local `ref`. For a transformed value that must follow the prop,
use `computed`. Do not copy a prop at setup and expect later parent updates to appear.

Declare emitted events: undeclared listeners can fall through to the root element. Name events for
what happened (`selected`), not what the parent must do (`closeModal`).

## Avoid the `defineModel` default trap

`defineModel()` (Vue 3.4+) replaces the `modelValue` prop and `update:modelValue` emit. Its documented
trap is a child default when the parent passes an undefined ref: the child starts with the default
while the parent remains undefined. Initialize the parent or avoid the child default.

## Preserve identity in lists

Without keys, Vue uses an in-place patch strategy. That can attach local component or DOM state to the
wrong item after a reorder. Use a primitive, stable identity—not the position when items can move.

```vue
<!-- Bad: position changes when the list is sorted. -->
<TodoItem v-for="(todo, index) in todos" :key="index" :todo="todo" />

<!-- Good: identity follows the item. -->
<TodoItem v-for="todo in todos" :key="todo.id" :todo="todo" />
```

Do not put `v-if` and `v-for` on the same element. `v-if` runs first, so its expression cannot use the
loop variable. Filter with a computed, or move `v-if` to a wrapper when hiding the whole list.

## Compose instead of coupling

- Use slots when the child owns data but the parent must own markup. Supply fallback content for the
  common case.
- Use a symbol `InjectionKey<T>` for typed subtree context. Provide readonly state plus explicit
  mutators; keep mutations with the provider when practical.
- Use template refs only for DOM or deliberately exposed component APIs. They are null before mount
  and may become null again.
- Prefer props and events over `$parent` or undocumented child internals.

## Apply style-guide severity accurately

- Multi-word component names prevent collisions with HTML elements; root `App` is the documented
  exception.
- In applications, global styles may live in top-level `App` and layout components. Scope other
  component styles with `scoped`, CSS Modules, or a class convention. Component libraries should
  prefer a class strategy so consumers can override styles.
- In `scoped` CSS, prefer classes over large numbers of element selectors; Vue documents the latter
  as slower after attribute rewriting.
