# InputText

InputText is an extension to standard input element with theming.

```ts
import { InputText } from '@atlas/ui';
```

## Usage

```vue
<InputText v-model="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `string` | `''` | Bound value of the input. |
| `type` | `string` | `'text'` | Native input type. |
| `placeholder` | `string` | `''` | Placeholder text to display when empty. |
| `disabled` | `boolean` | `false` | Disables the input when true. |
| `clearable` | `boolean` | `false` | Displays a button to clear the value. |
| `pt` | `InputTextPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `string` | Emitted when the value changes. |
| `input` | `Event` | Browser input event. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the input receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the input loses focus. |

Refer to the [PrimeVue InputText API](https://primevue.org/inputtext/#api).

