# InputNumber

InputNumber is an input field for numeric values with theming.

```ts
import { InputNumber } from '@atlas/ui';
```

## Usage

```vue
<InputNumber v-model="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `number` | `undefined` | Bound numeric value. |
| `min` | `number` | `undefined` | Minimum value. |
| `max` | `number` | `undefined` | Maximum value. |
| `step` | `number` | `1` | Incremental step when using buttons. |
| `mode` | `'decimal' \| 'currency'` | `'decimal'` | Number formatting mode. |
| `showButtons` | `boolean` | `false` | Shows increment and decrement buttons. |
| `prefix` | `string` | `''` | Text to prefix the value. |
| `suffix` | `string` | `''` | Text to append to the value. |
| `disabled` | `boolean` | `false` | Disables the input when true. |
| `pt` | `InputNumberPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots
- `incrementicon` – Custom icon for the increment button.
- `decrementicon` – Custom icon for the decrement button.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `number` | Emitted when the value changes. |
| `input` | `Event` | Browser input event. |
| `change` | `number` | Triggered when the input loses focus and value changes. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the input receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the input loses focus. |

Refer to the [PrimeVue InputNumber API](https://primevue.org/inputnumber/#api).

