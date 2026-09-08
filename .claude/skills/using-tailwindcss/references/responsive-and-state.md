# Responsive, State, and Dark Mode

## Write the Small Screen as the Base Case

Breakpoints are min-width. An unprefixed utility applies everywhere; `sm:` does not mean "on small
screens".

Incorrect: `class="p-8 sm:p-4"` intending eight on desktop.

Correct: `class="p-4 md:p-8"` — four everywhere, eight from `md` up.

## Use a Container Query for a Component That Moves

A component in both a sidebar and a main column needs its own width, not the viewport's. The
ancestor must declare a container type, which applies containment.

Incorrect: `class="grid grid-cols-1 md:grid-cols-2"` inside a 320px sidebar — the viewport is wide,
so it wrongly goes to two columns.

Correct: `class="@container"` on the wrapper, `class="grid grid-cols-1 @md:grid-cols-2"` on the grid.

## Place a Breakpoint Where the Layout Fails

Incorrect: `--breakpoint-phone: 390px;` named after a device.

Correct: a breakpoint at the width where the content actually starts to wrap badly.

## Read Stacked Variants Left to Right

Stacked variants apply in written order, so `dark:md:hover:*` and `hover:md:dark:*` are not
interchangeable. Older examples online use the opposite order and change meaning when copied.

## Style From the Attribute That Already Holds the State

Toggling a class duplicates state the DOM already carries, and assistive technology reads the
attribute, not the class.

Incorrect: `<button class="btn is-disabled">` plus `disabled`

Correct: `<button class="btn disabled:opacity-50" disabled>`

Same for `aria-expanded:*`, `data-state-*`, `group-*` for an ancestor's state, `peer-*` for an
earlier sibling's, and `has-*` for a parent by its descendants.

## Style `focus-visible`, Never Remove Focus

`outline-hidden` (v4's replacement for `outline-none`) with nothing in its place makes keyboard
operation invisible.

Incorrect: `class="focus:outline-hidden"`

Correct: `class="focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-brand-500"`

## Theme Dark Mode With Variables, Not Paired Utilities

A `dark:` twin on every element makes every future change a two-place edit, and any element missing
its twin inherits the wrong colour.

Incorrect: `class="bg-white text-slate-900 dark:bg-slate-900 dark:text-white"` repeated everywhere.

Correct:
```css
@theme { --color-surface: white; --color-ink: oklch(0.2 0 0); }
@variant dark { --color-surface: oklch(0.2 0 0); --color-ink: white; }
```
```html
<div class="bg-surface text-ink">
```

Check contrast in both themes. A pair that passes on white often fails on a dark surface.

## Guard Motion With `motion-safe`

Incorrect: `class="animate-bounce"`

Correct: `class="motion-safe:animate-bounce"`
