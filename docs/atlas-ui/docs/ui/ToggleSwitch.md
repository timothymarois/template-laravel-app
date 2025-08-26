# ToggleSwitch

ToggleSwitch is a switch component for boolean values.

```ts
import { ToggleSwitch } from '@atlas/ui';
```

## Usage

```vue
<ToggleSwitch v-model="checked" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `any` | `false` | Bound value of the switch. |
| `trueValue` | `any` | `true` | Value to represent the checked state. |
| `falseValue` | `any` | `false` | Value to represent the unchecked state. |
| `disabled` | `boolean` | `false` | Disables the switch when true. |
| `pt` | `ToggleSwitchPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots
None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `any` | Emitted when the value changes. |
| `change` | `{ originalEvent: Event, value: any }` | Triggered when the value changes. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the switch receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the switch loses focus. |

Refer to the [PrimeVue ToggleSwitch API](https://primevue.org/toggleswitch/#api).
