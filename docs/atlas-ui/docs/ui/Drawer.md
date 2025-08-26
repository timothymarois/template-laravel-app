# Drawer

Panel that slides in from the edge of the screen.

```ts
import { Drawer } from '@atlas/ui';
```

## Usage

```vue
<Drawer v-model:visible="visible">
  Content
</Drawer>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `visible` | `boolean` | `false` | Controls visibility. |
| `position` | `'left' \| 'right' \| 'top' \| 'bottom' \| 'full'` | `'left'` | Drawer attachment edge. |
| `header` | `string` | `undefined` | Title content. |
| `baseZIndex` | `number` | `0` | Base z-index for layering. |
| `autoZIndex` | `boolean` | `true` | Automatically manage layering. |
| `dismissable` | `boolean` | `true` | Hide when background is clicked. |
| `showCloseIcon` | `boolean` | `true` | Display close icon. |
| `closeButtonProps` | `object` | `{ severity: 'secondary', text: true, rounded: true }` | Props for the close button. |
| `closeIcon` | `string` | `undefined` | Icon for the close button. |
| `modal` | `boolean` | `true` | Add modal overlay. |
| `blockScroll` | `boolean` | `false` | Prevent background scrolling. |
| `dt` | `DesignToken` | `undefined` | Design tokens for CSS variables. |
| `pt` | `DrawerPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configures pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |

### Slots
- `default` – Drawer content.
- `header` – Custom header content.
- `footer` – Footer section.
- `closebutton` – Replaces the close button.

### Events
None.

Refer to the [PrimeVue Drawer API](https://primevue.org/drawer/#api).
