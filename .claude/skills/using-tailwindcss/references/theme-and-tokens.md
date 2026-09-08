# Theme and Tokens

In v4 the theme is CSS. `@theme` defines custom properties *and* generates matching utilities.

## Use `@theme` for Design Tokens, `:root` for Plain Variables

`@theme` generates utilities; `:root` does not. Using `:root` for a token leaves it reachable only
through arbitrary values.

Incorrect:
```css
:root { --color-brand-500: oklch(0.72 0.11 178); }
/* no bg-brand-500 exists; markup falls back to bg-[var(--color-brand-500)] */
```

Correct:
```css
@theme { --color-brand-500: oklch(0.72 0.11 178); }
/* bg-brand-500, text-brand-500, border-brand-500 all exist */
```

## Put the Variable in a Recognized Namespace

A variable in no namespace generates nothing. This is the usual reason a "theme value" has no class.

| Namespace | Generates |
|---|---|
| `--color-*` | `bg-*`, `text-*`, `border-*`, `fill-*` |
| `--spacing-*` | `p-*`, `m-*`, `w-*`, `h-*`, `gap-*` |
| `--text-*` | `text-xs` … `text-9xl` |
| `--font-*` | `font-sans`, custom families |
| `--breakpoint-*` | `sm:`, `md:` variants |
| `--radius-*` | `rounded-*` |
| `--shadow-*` | `shadow-*` |

Incorrect: `@theme { --brand-primary: #3f3cbb; }`

Correct: `@theme { --color-brand-primary: #3f3cbb; }`

## Override One Value; Reset a Namespace Deliberately

Redefining a name overrides that value. `--*: initial` clears everything and removes utilities other
code may already use — a migration, not a setting.

Override one: `@theme { --breakpoint-sm: 30rem; }`

Reset a namespace:
```css
@theme {
  --color-*: initial;
  --color-white: #fff;
  --color-brand: oklch(0.72 0.11 178);
}
```

## Use `@theme inline` When a Variable References Another

Without `inline`, the utility holds a reference that resolves through the cascade and can pick up a
value redefined nearer the element.

Incorrect: `@theme { --font-sans: var(--font-inter); }`

Correct: `@theme inline { --font-sans: var(--font-inter); }`

## Keep Keyframes Beside the Animation Variable

```css
@theme {
  --animate-fade-in: fade-in 0.3s ease-out;
  @keyframes fade-in { from { opacity: 0 } to { opacity: 1 } }
}
```

## Promote a Repeated Arbitrary Value Into a Token

One arbitrary value is a one-off. The second is a duplicated constant nobody can change in one
place.

Incorrect: `mt-[13px]` in four files.

Correct: `@theme { --spacing-gutter: 13px; }` then `mt-gutter`.

## Share a Theme as a CSS Import

```css
@import "tailwindcss";
@import "../brand/theme.css";
```
