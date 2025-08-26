# ToggleButton

ToggleButton is a two-state button component.

```ts
import { ToggleButton } from '@atlas/ui';
```

## Usage

```vue
<ToggleButton v-model="checked" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `boolean` | `false` | Bound value of the button. |
| `onLabel` | `string` | `''` | Label when in checked state. |
| `offLabel` | `string` | `''` | Label when in unchecked state. |
| `onIcon` | `string` | `''` | Icon class for the checked state. |
| `offIcon` | `string` | `''` | Icon class for the unchecked state. |
| `iconPos` | `'left' \| 'right'` | `'left'` | Position of the icon. |
| `disabled` | `boolean` | `false` | Disables the button when true. |
| `pt` | `ToggleButtonPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots
None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `boolean` | Emitted when the value changes. |
| `change` | `{ originalEvent: Event, value: boolean }` | Triggered when the value changes. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the button receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the button loses focus. |

Refer to the [PrimeVue ToggleButton API](https://primevue.org/togglebutton/#api).

