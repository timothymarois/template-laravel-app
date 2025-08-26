# PageFooter

Sticky footer with optional action slot.

```ts
import { PageFooter } from '@atlas/ui';
```

## Usage

```vue
<PageFooter :leftOffset="64">
  <span>&copy; 2024</span>
  <template #action>
    <Button label="Save" />
  </template>
</PageFooter>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `leftOffset` | `number` | `0` | Offset to account for sidebars. |
| `widthClass` | `string` | `'max-w-screen-2xl'` | Max width container class. |

### Slots
- `default` – footer content.
- `action` – action area beside footer content.

### Events
- None.
