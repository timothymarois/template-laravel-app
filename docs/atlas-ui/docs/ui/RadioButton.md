# RadioButton

RadioButton is an extension to standard radio button element with theming.

```ts
import { RadioButton } from '@atlas/ui';
```

## Usage

```vue
<RadioButton v-model="selected" :value="'a'" name="group" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `any` | `undefined` | Currently selected value. |
| `value` | `any` | `undefined` | Value of the radio button option. |
| `name` | `string` | `''` | Name of the radio group. |
| `disabled` | `boolean` | `false` | Disables the radio button when true. |
| `pt` | `RadioButtonPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `any` | Emitted when the value changes. |
| `change` | `{ originalEvent: Event, value: any }` | Triggered when the option is selected. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the radio button receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the radio button loses focus. |

Refer to the [PrimeVue RadioButton API](https://primevue.org/radiobutton/#api).

