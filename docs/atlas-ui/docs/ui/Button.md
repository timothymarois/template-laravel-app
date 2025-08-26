# Button

Basic action trigger element.

```ts
import { Button } from '@atlas/ui';
```

## Usage

```vue
<Button>Click me</Button>
```

## API

### Props
| Prop | Type | Description |
| ---- | ---- | ----------- |
| `label` | `string` | Text of the button. |
| `icon` | `string` | Name of the icon. |
| `iconPos` | `'left' \| 'right' \| 'top' \| 'bottom'` | Position of the icon. Defaults to `'left'`. |
| `iconClass` | `string \| object` | Style class of the icon. |
| `badge` | `string` | Value of the badge. |
| `badgeClass` | `string \| object` | Class for the badge. |
| `badgeSeverity` | `'secondary' \| 'info' \| 'success' \| 'warn' \| 'danger' \| 'contrast'` | Severity style of the badge. |
| `loading` | `boolean` | Displays a loading indicator. |
| `loadingIcon` | `string` | Icon for the loading state. |
| `as` | `string \| Component` | Tag name of the root element. Defaults to `'BUTTON'`. |
| `asChild` | `boolean` | Use the child element as the root. |
| `link` | `boolean` | Renders with link styling. |
| `severity` | `'secondary' \| 'success' \| 'info' \| 'warn' \| 'help' \| 'danger' \| 'contrast'` | Visual severity. |
| `raised` | `boolean` | Adds a box shadow. |
| `rounded` | `boolean` | Applies rounded border radius. |
| `text` | `boolean` | Text button without background. |
| `outlined` | `boolean` | Bordered button without background. |
| `size` | `'small' \| 'large'` | Button size. |
| `variant` | `'outlined' \| 'text' \| 'link'` | Variant shorthand. |
| `plain` | `boolean` | Plain textual button (deprecated, use `severity="contrast"`). |
| `fluid` | `boolean` | Stretches to full width. |
| `dt` | `DesignToken` | Design token for scoped CSS variables. |
| `pt` | `ButtonPassThroughOptions` | Pass-through styling options. |
| `ptOptions` | `PassThroughOptions` | Configuration for pass-through. |
| `unstyled` | `boolean` | Removes component styles. |

### Slots
- `default` – Custom button content, overrides `label`, `icon`, and `badge`.
- `icon` – Replaces the icon element.
- `loadingicon` – Custom loading icon.

### Events
- None.

Refer to the [PrimeVue Button API](https://primevue.org/button/#api) for full details.
