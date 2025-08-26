# Checkbox

Checkbox is an extension to standard checkbox element with theming.

```ts
import { Checkbox } from '@atlas/ui';
```

## Usage

```vue
<Checkbox v-model="checked" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `any` | `false` | Checked value. |
| `binary` | `boolean` | `false` | Uses true/false values instead of multiple selection. |
| `trueValue` | `any` | `true` | Value to represent the checked state in binary mode. |
| `falseValue` | `any` | `false` | Value to represent the unchecked state in binary mode. |
| `disabled` | `boolean` | `false` | Disables the checkbox when true. |
| `pt` | `CheckboxPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `any` | Emitted when the value changes. |
| `change` | `{ originalEvent: Event, checked: boolean, value: any }` | Triggered on value change. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the checkbox receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the checkbox loses focus. |

Refer to the [PrimeVue Checkbox API](https://primevue.org/checkbox/#api).

