# TooltipInfo

TooltipInfo shows a clickable info icon that reveals a popover.

```ts
import { TooltipInfo } from '@atlas/ui';
```

## Usage

```vue
<TooltipInfo>
  Details here
</TooltipInfo>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| pt | `object` | `undefined` | Pass-through options to customize internal elements. |

### Slots

| Name | Description |
| ---- | ----------- |
| default | Content displayed inside the popover. |

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| toggle | `boolean` | Emitted with visibility state when the popover is toggled. |

Refer to the [PrimeVue Popover API](https://primevue.org/popover/#api).
