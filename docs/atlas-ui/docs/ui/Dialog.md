# Dialog

Modal overlay for user interactions.

```ts
import { Dialog, Button } from '@atlas/ui';
```

## Usage

```vue
<Dialog v-model:visible="visible">
  Content
  <template #footer>
    <Button label="Close" @click="visible = false" />
  </template>
</Dialog>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `header` | `string` | `undefined` | Title content of the dialog. |
| `footer` | `string` | `undefined` | Footer content of the dialog. |
| `visible` | `boolean` | `false` | Controls visibility. |
| `modal` | `boolean` | `false` | Blocks background when shown. |
| `contentStyle` | `any` | `undefined` | Inline style of the content section. |
| `contentClass` | `any` | `undefined` | Style class of the content section. |
| `contentProps` | `HTMLAttributes` | `undefined` | Props for the overlay content element. |
| `closable` | `boolean` | `true` | Adds a close icon to the header. |
| `dismissableMask` | `boolean` | `false` | Hide on outside click of modal background. |
| `closeOnEscape` | `boolean` | `true` | Hide when escape key is pressed. |
| `showHeader` | `boolean` | `true` | Display the header. |
| `blockScroll` | `boolean` | `false` | Prevent background scrolling. |
| `baseZIndex` | `number` | `0` | Base z-index for layering. |
| `autoZIndex` | `boolean` | `true` | Automatically manage layering. |
| `position` | `'center' \| 'top' \| 'bottom' \| 'left' \| 'right' \| 'topleft' \| 'topright' \| 'bottomleft' \| 'bottomright'` | `'center'` | Initial position of the dialog. |
| `maximizable` | `boolean` | `false` | Allow full-screen mode. |
| `breakpoints` | `DialogBreakpoints` | `undefined` | Responsive width options. |
| `draggable` | `boolean` | `true` | Enable moving by dragging the header. |
| `keepInViewport` | `boolean` | `true` | Restrict dragging within viewport. |
| `minX` | `number` | `0` | Minimum left coordinate when dragging. |
| `minY` | `number` | `0` | Minimum top coordinate when dragging. |
| `appendTo` | `'body' \| 'self' \| HTMLElement` | `'body'` | DOM element to attach the dialog. |
| `style` | `any` | `undefined` | Style of the dialog container. |
| `closeIcon` | `string` | `undefined` | Icon for the close button. |
| `maximizeIcon` | `string` | `undefined` | Icon when dialog is not maximized. |
| `minimizeIcon` | `string` | `undefined` | Icon when dialog is maximized. |
| `closeButtonProps` | `object` | `{ severity: 'secondary', rounded: true, text: true }` | Props for the close button. |
| `maximizeButtonProps` | `object` | `{ severity: 'secondary', rounded: true, text: true }` | Props for the maximize button. |
| `dt` | `DesignToken` | `undefined` | Design tokens for CSS variables. |
| `pt` | `DialogPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configures pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |

### Slots
- `default` – Dialog content.
- `header` – Custom header content.
- `footer` – Footer section.
- `closebutton` – Replaces the close button.
- `maximizebutton` – Replaces the maximize button.

### Events
None.

Refer to the [PrimeVue Dialog API](https://primevue.org/dialog/#api).
