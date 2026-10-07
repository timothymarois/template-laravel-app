# Class Detection

Tailwind scans source files as plain text. A class exists only if its complete name appears
literally. It never executes your code.

## Never Build a Class Name by Interpolation

The assembled string does not exist in the source, so the CSS is never generated. The element
renders unstyled — usually only in production, only on the branch nobody exercised.

Incorrect:
```jsx
<span className={`bg-${color}-100 text-${color}-800`} />
```

Correct:
```jsx
const tones = {
  red: "bg-red-100 text-red-800",
  green: "bg-green-100 text-green-800",
};
<span className={tones[tone]} />
```

## Switch on Whole Class Names, Not on Fragments

Incorrect: `class="text-{{ error ? 'red' : 'green' }}-600"`

Correct: `class="{{ error ? 'text-red-600' : 'text-green-600' }}"`

## Never Style From a Runtime String

A class name from an API, a database column, or a CMS field is as invisible as an interpolated one.
Map the value to a literal in source.

Incorrect: `<div :class="page.theme_class">`

Correct: `<div :class="THEMES[page.theme] ?? THEMES.default">`

## Register Sources Outside the Default Scan

`.gitignore`d paths, `node_modules`, binary files, CSS files, and lock files are not scanned. A
component library's classes never compile until you add it.

```css
@import "tailwindcss";
@source "../node_modules/@acmecorp/ui-lib";
@source not "../src/components/legacy";
```

## Use `source(none)` Only in a Monorepo With a Wrong Base

It disables automatic detection, so every path becomes a list someone must remember to update.

```css
@import "tailwindcss" source(none);
@source "../admin";
@source "../shared";
```

## Verify in the Emitted CSS, Not the Markup

A class that failed to compile is identical in source to one that did. A dev build can also mask it
when another file happens to contain the same class.

Incorrect: reading the template and concluding the class is applied.

Correct: build for production, grep the emitted CSS for the class, and exercise the conditional
branch in the browser.

## Do Not Expect `safelist`

v4 removed it. A name that genuinely cannot appear in source must be written as a literal somewhere
the scanner reads, kept beside the code that needs it.
