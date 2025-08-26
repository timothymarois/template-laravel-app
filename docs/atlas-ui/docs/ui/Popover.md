# Popover

Transient overlay for supplemental content.

```ts
import { Popover, Button } from '@atlas/ui';
```

## Usage

```vue
<Popover ref="popover">
  <template #content>More info</template>
</Popover>
<Button label="Show" @click="popover.toggle($event)" />
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `dismissable` | `boolean` | `true` | Hide when outside is clicked. |
| `appendTo` | `'body' \| 'self' \| HTMLElement` | `'body'` | Where the overlay is attached. |
| `baseZIndex` | `number` | `0` | Base z-index for layering. |
| `autoZIndex` | `boolean` | `true` | Automatically manage layering. |
| `breakpoints` | `PopoverBreakpoints` | `undefined` | Responsive width options. |
| `dt` | `DesignToken` | `undefined` | CSS design tokens. |
| `pt` | `PopoverPassThroughOptions` | `undefined` | Pass-through attributes for DOM elements. |
| `ptOptions` | `PassThroughOptions` | `undefined` | Configure pass-through options. |
| `unstyled` | `boolean` | `false` | Removes component styles. |
| `closeOnEscape` | `boolean` | `true` | Hide when escape key is pressed. |

### Slots
- `default` – Target element that triggers the popover.
- `content` – Content displayed inside the popover.

### Events
None.

Refer to the [PrimeVue Popover API](https://primevue.org/popover/#api).
