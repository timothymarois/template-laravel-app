# ScrollFrame

Provides a scrollable container sized to the viewport. When `page` is set it locks body scrolling and uses `scroll-key` to preserve position. Scrolling can be disabled with the `scrollable` prop to avoid setting a fixed height.

```ts
import { ScrollFrame } from '@atlas/ui';
```

## Usage

```vue
<ScrollFrame
  page
  scroll-key="settings"
  :add-offset="16"
  class="p-4"
>
  <!-- scrollable content -->
</ScrollFrame>

<!-- Disable fixed height/scroll behavior -->
<ScrollFrame :scrollable="false" class="p-4">
  <!-- content grows naturally without constrained height -->
  ...
  </ScrollFrame>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `scrollKey` | `string \| null` | `null` | Identifier used to persist scroll position. |
| `page` | `boolean` | `false` | Lock body scroll and treat the frame as a full page. |
| `allowBodyScroll` | `boolean` | `false` | Allow body scroll even when `page` is `true`. |
| `offset` | `number \| null` | `null` | Explicit pixel offset for height calculation. |
| `addOffset` | `number` | `0` | Extra pixels subtracted from height. |
| `rootClass` | `string` | `overflow-y-auto` | Additional classes for the frame element. |
| `scrollable` | `boolean` | `true` | Enables height calculation and scroll behavior. When `false`, no height is set.

### Slots
- `default` – scrollable content.

### Events

None.
