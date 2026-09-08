# Blade & Views Best Practices

## Use `$attributes->merge()` in Component Templates

Hardcoding classes prevents consumers from adding their own. `merge()` combines class attributes cleanly.

```blade
<div {{ $attributes->merge(['class' => 'alert alert-'.$type]) }}>
    {{ $message }}
</div>
```

## Use `@pushOnce` for Per-Component Scripts

If a component renders inside a `@foreach`, `@push` inserts the script N times. `@pushOnce` guarantees it's included exactly once.

## Prefer Blade Components Over `@include`

`@include` shares all parent variables implicitly (hidden coupling). Components have explicit props, attribute bags, and slots.

## Use View Composers for Shared View Data

If every controller rendering a sidebar must pass `$categories`, that's duplicated code. A View Composer centralizes it.

## Use Blade Fragments for Partial Re-Renders (htmx/Turbo)

A single view can return either the full page or just a fragment, keeping routing clean.

```php
return view('dashboard', compact('users'))
    ->fragmentIf($request->hasHeader('HX-Request'), 'user-list');
```

## Use `@aware` for Deeply Nested Component Props

Avoids re-passing parent props through every level of nested components.

Use `@aware` only when the value is part of a real ancestor component contract. It should not become
an invisible channel for arbitrary page state.

## Escape or Sanitize at One Deliberate Boundary

Incorrect — raw output can execute user-controlled markup:

```blade
{!! $comment->body !!}
```

Correct for plain text:

```blade
{{ $comment->body }}
```

Use raw Blade output only for content that has passed a sanitizer appropriate to the delivery
context. Calling data "trusted" does not remove XSS risk.

Keep database queries and authorization out of templates. Controllers, view models, or composers
prepare data; Blade renders it. Use a view composer only when the data is genuinely shared across
the mapped views, not to hide one page-specific dependency.
