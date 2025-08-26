# InputOtp

InputOtp is an input component for entering one-time passwords.

```ts
import { InputOtp } from '@atlas/ui';
```

## Usage

```vue
<InputOtp v-model="code" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `string` | `''` | Bound OTP value. |
| `length` | `number` | `6` | Number of input boxes. |
| `integerOnly` | `boolean` | `false` | Restricts input to digits when true. |
| `disabled` | `boolean` | `false` | Disables the inputs when true. |
| `pt` | `InputOtpPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots
- `default` – Custom template for each input box.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `string` | Emitted when the value changes. |
| `complete` | `{ originalEvent: Event }` | Fired when the last box is filled. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when any box receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when any box loses focus. |

Refer to the [PrimeVue InputOtp API](https://primevue.org/inputotp/#api).

