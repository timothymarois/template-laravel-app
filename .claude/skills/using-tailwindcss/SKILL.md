---
name: using-tailwindcss
description: Use for CSS and styling work — layout, spacing, typography, colour, theming and design tokens, dark mode, responsive and container behaviour, transitions, focus and state styling, and the cascade, specificity, and custom properties underneath them. Covers Tailwind CSS v4 — utility composition, the theme, custom utilities and variants, and class detection in source files. Not for deciding what an interface should do, or for a non-web styling system.
---

# Use CSS and Tailwind

The subject is CSS; Tailwind is the toolchain. It compiles class names, so a class exists only if
its complete name appears literally in a source file, and the theme is CSS custom properties rather
than a JavaScript object.

Utilities do not replace understanding CSS. A cascade conflict, a stacking context, or a collapsing
layout is a CSS problem wearing utility classes.

This package owns styling. What the interface should do is a design decision made elsewhere;
component structure belongs to the framework in use.

## Check the version first

```sh
npm ls tailwindcss
```

This package targets v4, which needs Safari 16.4+, Chrome 111+, and Firefox 128+. A project below
that floor belongs on v3.4, where none of this applies. v4 does not work with Sass, Less, or Stylus.

v3 examples are common online and do not transfer: config, scales, defaults, and variant order all
changed.

## Defaults and the failure each prevents

| Avoid | Prefer | Failure prevented |
|---|---|---|
| `` `bg-${color}-600` `` | A map of complete class names | The class never compiles; the element renders unstyled |
| Repeated arbitrary values | A `@theme` token | A constant nobody can change in one place |
| `@apply` to build a component | A component or partial | CSS that re-implements the framework's composition |
| Appending a conflicting class | A merge helper | Stylesheet order decides the winner, not you |
| `outline-none` alone | `focus-visible` plus a visible ring | Keyboard operation becomes invisible |
| `space-y-*` on reorderable lists | `flex flex-col gap-*` | Margin lands on the wrong child |
| `!important` | A cascade layer | An override chain the next change must beat |
| Paired `dark:` utilities everywhere | Semantic tokens redefined under `dark` | Every colour change becomes a two-place edit |
| Raising `z-index` | Finding the stacking context | A child can never escape its ancestor's context |

## Load the depth the task needs

- [css.md](references/css.md) — cascade, layers, custom properties, box model, flex and grid,
  stacking contexts, logical properties, colour.
- [class-detection.md](references/class-detection.md) — why dynamic names fail, and `@source`.
- [theme-and-tokens.md](references/theme-and-tokens.md) — `@theme`, namespaces, resets, sharing.
- [composition.md](references/composition.md) — reuse, `@apply` and `@reference`, conflicts,
  `@utility`.
- [responsive-and-state.md](references/responsive-and-state.md) — breakpoints, container queries,
  variants, dark mode.

Read [the source map](references/sources.md) when auditing or changing a factual claim.

## Verify in the rendered output

A class that did not compile looks identical in source to one that did. Build for production, check
the emitted CSS, and exercise the conditional branch — do not read it.

```text
[HIGH] Interpolated class never compiles
Location: resources/js/components/Badge.vue:12
Evidence: :class="`bg-${tone}-100 text-${tone}-800`"
Why: the literal `bg-red-100` never appears in source, so it is never generated
Fix: map tone to complete class strings
Check: production build; assert the class exists in the emitted CSS
```
