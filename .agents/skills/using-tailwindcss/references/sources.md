# Tailwind and CSS source basis

Tailwind's own documentation establishes the compiler's behaviour and the v4 contract; MDN and the
W3C establish the CSS underneath. Every link was opened and checked on 23 August 2026.

## Version scope

- The [npm registry entry](https://registry.npmjs.org/tailwindcss/latest) reported `4.3.3` on
  23 August 2026. Query it at review time rather than trusting that number later.
- The [v4 announcement](https://tailwindcss.com/blog/tailwindcss-v4) and
  [compatibility page](https://tailwindcss.com/docs/compatibility) establish the browser floor of
  Safari 16.4, Chrome 111, and Firefox 128, its dependence on `@property` and `color-mix()`, the
  recommendation to stay on v3.4 for older browsers, and that v4 is not designed for Sass, Less, or
  Stylus.

## Class detection

- [Detecting classes in source files](https://tailwindcss.com/docs/detecting-classes-in-source-files)
  is the source of every rule in `class-detection.md`. It states that Tailwind scans source files as
  plain text and "has no way of understanding string concatenation or interpolation", supplies both
  the interpolation and the props examples and their corrected forms, lists what is excluded from
  the scan — `.gitignore`d files, `node_modules`, binary files, CSS files, lock files — and
  documents `@source`, `@source not`, `source()`, and `source(none)`.
- The [upgrade guide](https://tailwindcss.com/docs/upgrade-guide) establishes that `safelist` is not
  supported in v4.

## Theme

- [Theme variables](https://tailwindcss.com/docs/theme) establishes that `@theme` both defines
  custom properties and generates utilities, the distinction from `:root`, the namespace table,
  overriding a single default, `--*: initial` and per-namespace resets, `@theme inline` and why a
  variable referencing another needs it, `@theme static`, keyframes inside `@theme`, and sharing a
  theme by import.
- [Colors](https://tailwindcss.com/docs/colors) documents the default palette and its OKLCH basis.

## Composition

- [Styling with utility classes](https://tailwindcss.com/docs/styling-with-utility-classes) is the
  source of the reuse hierarchy in `composition.md`: loops for rendered repetition, then a component
  or template partial, and custom CSS only where a partial "feels heavy-handed" for a single
  element. It states that for anything more complicated than one element, template partials are
  "highly recommended" so structure and styles stay in one place, and its own example writes plain
  CSS against theme variables rather than `@apply`.
- [Adding custom styles](https://tailwindcss.com/docs/adding-custom-styles) and
  [Functions and directives](https://tailwindcss.com/docs/functions-and-directives) establish
  `@utility`, `@variant`, `@apply`, `@reference`, and that custom utilities registered with
  `@utility` participate in variants.
- The [upgrade guide](https://tailwindcss.com/docs/upgrade-guide) establishes that separately
  bundled stylesheets — CSS modules, Vue and Svelte `<style>` blocks — have no access to theme
  variables and need `@reference`, and that using the variable directly is the alternative.
- [tailwind-merge](https://github.com/dcastil/tailwind-merge) and
  [class-variance-authority](https://cva.style/docs) are the conflict-resolution and variant helpers
  named in `composition.md`. They are community libraries, not part of Tailwind, and are cited as
  the established solution to the class-order problem rather than as a requirement.
- [prettier-plugin-tailwindcss](https://github.com/tailwindlabs/prettier-plugin-tailwindcss) is the
  official class sorter; sorting is cosmetic and does not affect which utility wins.
- Adam Wathan,
  [CSS Utility Classes and "Separation of Concerns"](https://adamwathan.me/css-utility-classes-and-separation-of-concerns/),
  is the design rationale behind preferring composition in markup over extracting CSS classes. It is
  an opinion piece by the framework's author, cited as rationale rather than as evidence.

## Responsive, state, and dark mode

- [Responsive design](https://tailwindcss.com/docs/responsive-design) establishes min-width,
  mobile-first breakpoints, that an unprefixed utility applies at every size, container queries and
  the required container type, and that breakpoints come from `--breakpoint-*`.
- [Dark mode](https://tailwindcss.com/docs/dark-mode) establishes the default
  `prefers-color-scheme` behaviour and configuring a manual class or data-attribute toggle.
- The [upgrade guide](https://tailwindcss.com/docs/upgrade-guide) establishes that variant stacking
  order changed to left-to-right in v4, that `outline-none` became `outline-hidden`, and the
  `space-y-*` and `divide-*` selector change.
- [Preflight](https://tailwindcss.com/docs/preflight) establishes the base reset, including
  `box-sizing: border-box` and removed default margins.

## Setup and v4 contract

- The [upgrade guide](https://tailwindcss.com/docs/upgrade-guide) is retained because several v4
  facts used elsewhere are stated there: `safelist` is unsupported, separately bundled stylesheets
  need `@reference`, variant stacking order is left-to-right, `outline-none` became
  `outline-hidden`, `space-y-*` and `divide-*` changed selector, `@utility` replaces
  `@layer utilities`, arbitrary variables use `bg-(--var)`, and `theme()` gives way to CSS
  variables. This package targets v4 only and does not carry a migration workflow.
- [Installing with Vite](https://tailwindcss.com/docs/installation/using-vite) documents the
  `@tailwindcss/vite` plugin; PostCSS uses `@tailwindcss/postcss` and the CLI is
  `@tailwindcss/cli`.

## CSS

- MDN on the [cascade](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascade/Cascade) and
  [specificity](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascade/Specificity) establishes
  the resolution order used in `css.md` — origin and importance, then layer, then specificity, then
  source order — and that `:where()` contributes zero specificity while `:is()` takes its most
  specific argument.
- MDN on [`@layer`](https://developer.mozilla.org/en-US/docs/Web/CSS/@layer) establishes that a
  later layer wins over an earlier one regardless of specificity, and that unlayered styles win over
  layered ones.
- MDN on [custom properties](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascading_variables/Using_CSS_custom_properties)
  establishes inheritance and fallbacks;
  [`@property`](https://developer.mozilla.org/en-US/docs/Web/CSS/@property) establishes that
  registration is what makes a custom property animatable.
- MDN on the [box model](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_box_model/Introduction_to_the_CSS_box_model),
  [mastering margin collapsing](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_box_model/Mastering_margin_collapsing),
  [flexbox](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_flexible_box_layout/Basic_concepts_of_flexbox),
  and [grid](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_grid_layout/Basic_concepts_of_grid_layout)
  establish `box-sizing`, margin collapsing and what prevents it, the `min-width: auto` behaviour of
  flex items, and `fr` sizing.
- MDN on [stacking context](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_positioned_layout/Stacking_context)
  lists what creates one — including `transform`, `filter`, `opacity` below 1, `will-change`,
  `contain`, and `isolation` — and that `z-index` orders only within a context.
- MDN on [logical properties](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_logical_properties_and_values),
  [container queries](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_containment/Container_queries),
  [`:has()`](https://developer.mozilla.org/en-US/docs/Web/CSS/:has),
  [`oklch()`](https://developer.mozilla.org/en-US/docs/Web/CSS/color_value/oklch), and
  [`prefers-reduced-motion`](https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-reduced-motion)
  establish the remaining mechanisms in `css.md`.
- [WCAG 2.2, Understanding Focus Appearance](https://www.w3.org/WAI/WCAG22/Understanding/focus-appearance)
  establishes that a visible focus indicator is a conformance requirement, not a preference.

## Catalog conclusions

- The order of the reuse hierarchy is Tailwind's; treating `@apply` as a last resort *behind* plain
  CSS with theme variables is this package's reading of its own documented example.
- "Promote a repeated arbitrary value into a token" is a local rule; the documentation permits
  arbitrary values without a threshold.
- The defaults table in `SKILL.md` and the diagnosis-before-classes framing are operational
  conclusions.

Omitted on purpose: undated utility-versus-semantic-CSS opinion posts, v3 tutorials presented as
current, and class-ordering advice that implies attribute order affects the cascade.
