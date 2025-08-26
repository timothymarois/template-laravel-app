# TooltipIcon

TooltipIcon displays an informational icon with a tooltip.

```ts
import { TooltipIcon } from '@atlas/ui';
```

## Usage

```vue
<TooltipIcon text="More info" />
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| text | `string \| null` | `null` | Tooltip text displayed on hover. |
| iconClass | `string` | `'size-5'` | Additional CSS classes applied to the icon. |
| pt | `object` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events

None.

Refer to the [PrimeVue Tooltip API](https://primevue.org/tooltip/#api).
