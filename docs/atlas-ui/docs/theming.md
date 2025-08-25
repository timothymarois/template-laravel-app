# Theming

Atlas UI exposes design tokens as CSS variables so you can match the component library to your brand.

## Table of Contents
- [Primary Colors](#primary-colors)
- [Surface Colors](#surface-colors)
- [Rounding](#rounding)
- [Dark Mode](#dark-mode)
- [Setting the Default Theme](#setting-the-default-theme)

## Primary Colors
Use the `--p-primary-*` tokens to define your brand palette. Each token corresponds to a shade from `50` to `950`.

The playground includes ready-made palettes you can apply by adding a class to your root element:

```html
<html class="theme-primary-indigo">
```

Available palettes: `blue`, `purple`, `indigo`, `teal`, `pink`, `gray`, `green`, and `orange`.

You can also provide your own palette by declaring the variables:

```css
@layer base {
  :root {
    --p-primary-50: #dbeafe;
    /* ... */
    --p-primary-950: #0f172a;
  }
}
```

## Surface Colors
`--p-surface-*` tokens control neutral backgrounds and text. Choose a built-in palette such as `theme-surface-slate` or define custom values with the same `50`–`950` scale.

Available palettes: `blue`, `gray`, `purple`, `pink`, `slate`, and `zinc`.

## Rounding
Atlas provides border radius tokens for consistent rounding:

| Token | Value |
| ----- | ----- |
| `--p-rounded-0` | `0rem` |
| `--p-rounded-1` | `0.25rem` |
| `--p-rounded-2` | `0.5rem` |
| `--p-rounded-3` | `0.75rem` |
| `--p-rounded-4` | `1rem` |

Set the default radius with a `theme-rounded-{n}` class or use the tokens directly with Tailwind's arbitrary values:

```html
<div class="rounded-[var(--p-rounded-2)]"></div>
```

## Dark Mode
Atlas updates its tokens for dark mode using `@media (prefers-color-scheme: dark)`. To force dark mode or use Tailwind's `class` strategy, add a `dark` class to the root element:

```html
<html class="dark">
```

Override dark values by redefining the tokens inside a `@media (prefers-color-scheme: dark)` block if needed.

## Setting the Default Theme
Combine the theme classes on your root element to establish defaults for your project:

```html
<!-- index.html -->
<html class="theme-primary-teal theme-surface-slate theme-rounded-2">
```

Include `dark` to enable dark mode by default:

```html
<html class="theme-primary-teal theme-surface-slate theme-rounded-2 dark">
```

These classes configure the CSS variables that Atlas components consume throughout your application.
