# Composables

## Make inputs stay reactive

Accept `MaybeRefOrGetter<T>` when callers may pass a value, ref, or getter. Normalize with `toValue`
inside the tracking scope. Reading once before the effect loses future changes.

```ts
// Bad: one snapshot; a ref or getter is not tracked.
const url = toValue(source)
watchEffect(() => fetch(url))

// Good: the read becomes an effect dependency.
watchEffect(() => fetch(toValue(source)))
```

When returning several values, return a plain object of refs. A consumer may safely destructure it;
destructuring properties from a returned `reactive()` object disconnects them.

## Own every effect

A composable that registers a listener, timer, observer, socket, or request also registers its
cleanup. Put DOM-specific setup in `onMounted` for SSR safety and teardown in `onUnmounted`; cancel
stale watcher work with `onWatcherCleanup` (Vue 3.5+) or positional `onCleanup`.

Bryce Andy's watcher case study shows the practical symptoms: growing heap, lingering network work,
and stacked handlers after repeated changes. The replacement is not merely “remember cleanup”; make
cleanup part of the composable's contract.

## Call with an active Vue scope

Call composables synchronously in `setup()` or `<script setup>` so Vue can associate hooks and
watchers with the component. A lifecycle hook is also valid when the composable specifically needs
that phase. `<script setup>` is the documented exception after `await` because the compiler restores
the active instance. If creation must be deferred, create the watcher now and make its body
conditional, or manually stop what you create.

## Keep one concern

Use `useX` for reactive stateful logic, `createX` for a factory, and an ordinary function for pure
transformation. Anthony Fu's VueUse guidance is to keep composables small, composable, and
self-cleaning. A function with no reactivity or lifecycle does not gain anything from a `use` name.

Do not hoist user-specific refs to module scope in an SSR application. The module is reused between
requests; create app-scoped state with `createX` plus provide/inject, or use Pinia.
