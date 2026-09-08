# Inertia core

Read [`data-loading.md`](data-loading.md) when the failure is payload or query cost.

## Make the prop contract public on purpose

Every prop is serialized for the browser. Limit the shape at the server boundary; do not rely on a
component to ignore sensitive or unused fields.
Apply the shaped-prop default and sourced pair in `SKILL.md`. Use a resource or DTO when the shape is
reused or conditional. Use `withViewData()` for data needed by the root server template but not the
JavaScript page. Keep payloads bounded because Inertia stores page responses in browser history
state.

Permission props only control presentation:

```php
'can' => ['update' => $request->user()->can('update', $post)]
```

The update endpoint must still authorize the request. A hidden button cannot prevent a crafted
request.

## Match the request to its intended response

Use `<Form>`, `useForm`, or `router` for a request that should produce an Inertia page visit. After a
successful mutation, redirect; after validation failure, redirect back with errors. Inertia reads
the shared `errors` prop and calls the error callback—it does not consume a `422` JSON response.

```js
// Good: redirect and validation behavior remain in the Inertia visit flow.
form.post('/users')

// Bad when a page transition is expected: this bypasses Inertia visit handling.
fetch('/users', { method: 'POST', body: JSON.stringify(data) })
```

Do not turn that second example into a universal ban. In v3, `useHttp` and plain XHR or `fetch` are
supported for standalone requests that should not trigger a page visit.

Let Inertia convert requests containing files to `FormData`. With Laravel, send multipart updates as
`POST` plus `_method: 'put'` or `'patch'`; PHP does not natively parse multipart bodies for those
verbs. Give checkboxes an explicit value because the HTML default is `"on"`, which some boolean
validators reject. Exclude remembered password fields with `dontRemember('password')` when a keyed
form would otherwise write them to history.

Use named error bags when multiple forms can return the same field names. On partial reloads that
must preserve client-set errors, pass `preserveErrors: true`; Laravel shares server errors as an
`always` prop, so an empty bag otherwise replaces them.

For one-time notifications, use Inertia flash data. Unlike shared props, flash data is not persisted
in history and therefore does not reappear on back navigation.

## Choose layout lifetime deliberately

A layout wrapped inside each page is destroyed and recreated between visits. Stateful widgets can
reset, third-party setup can run again, and a component-local `ref` cannot guard work for the session
because the guard is recreated too.

```vue
<!-- Bad when the layout must survive navigation: each page owns its instance. -->
<Layout><PageContent /></Layout>

<!-- Good: assign Layout through Inertia's persistent-layout API. -->
<script>
import Layout from './Layout.vue'
export default { layout: Layout }
</script>
```

Use a persistent layout for UI that must outlive page visits. Otherwise make mount and unmount work
idempotent and verify repeated navigation, including focus and retained state.

## Protect history and deployed assets

For privileged page data, enable history encryption and rotate the key when clearing sensitive
history:

```php
Inertia::encryptHistory();
Inertia::clearHistory(); // for example, during logout
```

Encryption depends on `window.crypto.subtle`, so test it through HTTPS; it is unavailable in an
insecure context.

Keep asset versioning enabled. A version mismatch turns the next user-initiated Inertia visit into a
full-page load so new assets are fetched. Laravel Vite supplies a version automatically. Since
v3.6.0, a mismatch detected by polling or `router.reload()` waits for the next user-initiated visit
to avoid destroying unsaved state.

## Make SSR failures visible

Move browser-only work (`window`, `document`, `localStorage`, and libraries that touch them at import
time) behind client lifecycle boundaries. This prevents the community-reported `document is not
defined` failure during SSR.

Inertia falls back to client rendering when SSR fails. Enable `ssr.throw_on_error` in tests so a
green browser test cannot hide missing server-rendered HTML; leave fallback enabled in production.
Run the production SSR process under supervision and restart it after deploying a new server bundle.

## Test the seam

Assert the component, required prop shape, and forbidden fields. For Laravel, use `missing()` for
fields that must never ship, `reloadOnly()` or `reloadExcept()` for partial reloads, and
`loadDeferredProps()` for deferred data. For SSR, verify returned HTML rather than only client
hydration.
