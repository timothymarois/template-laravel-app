# PageFooter

Sticky footer with optional action slot.

## Example
```ts
import { PageFooter } from '@atlas/ui';
```

```vue
<PageFooter :leftOffset="64">
  <span>&copy; 2024</span>
  <template #action>
    <Button label="Save" />
  </template>
</PageFooter>
```

## Props
- `leftOffset: number` – offset to account for sidebars. Default `0`.
- `widthClass: string` – max width container class. Default `'max-w-screen-2xl'`.

## Slots
- `default` – footer content.
- `action` – action area beside footer content.

## Events
- None
