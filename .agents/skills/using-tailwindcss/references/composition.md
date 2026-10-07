# Composition and Reuse

## Reach for Rendering Before CSS

Repetition in utility markup is usually a rendering problem. The order is: a loop, then a component
or template partial, then custom CSS for a single element only.

Incorrect: extracting `.card` in CSS because the markup repeats in a list.

Correct: render the list in a loop; the markup is authored once already.

## Extract a Component, Not a Class

A CSS class captures styling and leaves the structure to be duplicated by hand. Anything more than a
single element belongs in a component or partial.

Incorrect:
```css
@layer components { .card { @apply rounded-lg border p-4 shadow-sm; } }
```
```html
<div class="card"><h3 class="...">...</h3><p class="...">...</p></div>
```

Correct: a `<Card>` component or a `_card.blade.php` partial holding both structure and classes.

## Prefer Plain CSS With Tokens Over `@apply`

`@apply` reintroduces bespoke class names and the cascade. Where custom CSS is genuinely right,
reading theme variables is clearer and does not depend on utility names staying stable.

Incorrect:
```css
.btn-primary { @apply rounded-full bg-violet-500 px-4 py-2 font-semibold; }
```

Correct:
```css
@layer components {
  .btn-primary {
    border-radius: calc(infinity * 1px);
    background-color: var(--color-violet-500);
    padding: --spacing(2) --spacing(4);
  }
}
```

## Add `@reference` in Separately Compiled Stylesheets

A Vue or Svelte `<style>` block and CSS modules compile alone, with no access to theme variables, so
`@apply` there fails.

Incorrect:
```vue
<style> h1 { @apply text-2xl font-bold; } </style>
```

Correct:
```vue
<style>
  @reference "../../app.css";
  h1 { @apply text-2xl font-bold; }
</style>
```

Also correct, and needing no reference: `h1 { font-size: var(--text-2xl); }`

## Merge Conflicting Classes Instead of Appending Them

Two utilities setting one property resolve by their order in the generated stylesheet, not by their
order in the `class` attribute. A caller's `p-8` may lose to the component's `p-4`.

Incorrect:
```jsx
<button className={`px-4 py-2 bg-slate-100 ${className}`} />
```

Correct:
```jsx
<button className={twMerge("px-4 py-2 bg-slate-100", className)} />
```

## Define Custom Utilities With `@utility`

A rule hand-written into `@layer utilities` is not registered with the compiler, so variants like
`md:` and `hover:` do not apply to it.

Incorrect:
```css
@layer utilities { .scrollbar-none { scrollbar-width: none; } }
```

Correct:
```css
@utility scrollbar-none {
  scrollbar-width: none;
  &::-webkit-scrollbar { display: none; }
}
```

## Check Whether a Token Would Do Instead

A new colour, radius, or spacing step is a theme variable, not a custom utility.
