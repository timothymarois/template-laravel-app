# CSS Underneath the Utilities

Most "Tailwind bugs" are CSS behaving as specified. Identify the mechanism before changing classes.

## Never Let Class-Attribute Order Decide a Conflict

Equal-specificity utilities resolve by their order in the *generated stylesheet*, not the order you
wrote them. The cascade order is: origin and importance, then layer, then specificity, then source
order.

Incorrect: `class="px-4 px-8"` expecting `px-8` to win because it is last.

Correct: remove the loser, or merge with a helper that knows they conflict.

## Fix the Cause Instead of Reaching for `!important`

`!important` means something is winning that should not. The fix is usually a layer, a removed rule,
or a better selector.

Incorrect: `.card { padding: 2rem !important; }`

Correct: move the base rule into an earlier `@layer`, so any later declaration wins without it.

## Use `@layer` Instead of Escalating Specificity

A later layer beats an earlier one regardless of specificity, and unlayered styles beat all layers.

Incorrect: `html body .content article h2 { … }` to beat a reset.

Correct:
```css
@layer reset, components, utilities;
@layer components { .prose h2 { … } }
```

## Remember Custom Properties Inherit

They are live, inherited values, not compile-time constants — which is why theming works by
redefining a variable on a scope. An unexpected value usually means a nearer ancestor redefined it.

```css
:root { --surface: white; }
[data-theme="dark"] { --surface: #111; }
.card { background: var(--surface); }
```

To animate one, register it — an unregistered custom property cannot transition.

Incorrect: `transition: --brand 200ms;` with `--brand` never registered.

Correct: `@property --brand { syntax: "<color>"; inherits: false; initial-value: #000; }`

## Add `min-w-0` When a Flex Item Overflows

Flex items default to `min-width: auto` and will not shrink below their content. This is the most
common flex overflow cause.

Incorrect: `<div class="flex"><div class="truncate">very long text…</div></div>`

Correct: `<div class="flex"><div class="min-w-0 truncate">very long text…</div></div>`

The grid equivalent is `minmax(0, 1fr)` rather than `1fr`.

## Use `gap` Instead of Margins Between Items

Margins collapse between block siblings, attach to the wrong element when order changes, and leave
trailing space at the ends. Gap does none of that and does not collapse.

Incorrect: `class="space-y-4"` on a list that can reorder.

Correct: `class="flex flex-col gap-4"`

## Give a Percentage Height a Resolved Parent

`h-full` computes against `auto` and does nothing unless the parent has a resolved height.

Incorrect: `<div><div class="h-full">…</div></div>`

Correct: `<div class="h-64"><div class="h-full">…</div></div>` — or use flex/grid sizing.

## Choose Grid for Two Axes, Flex for One

Grid when the container decides the tracks — columns aligning across rows, a page skeleton. Flex when
items distribute along one line — a toolbar, a row of chips.

## Find the Stacking Context Before Raising `z-index`

`transform`, `filter`, `opacity` below 1, `will-change`, `contain`, and `isolation: isolate` all
create one. A child can never escape its ancestor's context, so a bigger number does nothing.

Incorrect: `z-50` on a dropdown inside a `transform`ed card.

Correct: raise the ancestor, or portal the overlay to the document root.

## Use Logical Properties for Anything Localizable

Physical `left`/`right` are wrong in a right-to-left locale.

Incorrect: `class="ml-4 text-left"`

Correct: `class="ms-4 text-start"`

Keep physical values only where the direction is genuinely physical, such as a shadow offset.

## Build Colour Scales in `oklch()`

Perceptually uniform, so changing lightness keeps hue and chroma stable and the scale stays even.

Incorrect: an `hsl()` ramp where the mid tones look muddy and uneven.

Correct: `oklch(0.72 0.11 178)` varying only lightness across the scale.

Colour is never the only carrier of meaning, and contrast is checked against the rendered
background, including translucency.

## Select the Parent With `:has()` Instead of a Toggled Class

Incorrect: JavaScript adding `.is-checked` to a label when its input changes.

Correct: `label:has(input:checked) { … }`, or the `has-[:checked]:*` variant.

## Group Selectors With `:where()` When Specificity Should Stay Zero

`:is()` takes the specificity of its most specific argument; `:where()` contributes none, which is
how a reset stays trivially overridable.
