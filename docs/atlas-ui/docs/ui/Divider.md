# Divider

Visual separator for content sections.

```ts
import { Divider } from '@atlas/ui';
```

## Usage

```vue
<Divider />
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `align` | `'left' \| 'center' \| 'right' \| 'top' \| 'bottom'` | `undefined` | Alignment of the content. |
| `layout` | `'horizontal' \| 'vertical'` | `'horizontal'` | Orientation of the divider. |
| `type` | `'solid' \| 'dashed' \| 'dotted'` | `'solid'` | Border style type. |
| `dt` | `DesignToken` | `undefined` | Design tokens for CSS variables. |
| `pt` | `DividerPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configures pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |

### Slots
- `default` – Custom content inside the divider.

### Events
None.

Refer to the [PrimeVue Divider API](https://primevue.org/divider/#api).
