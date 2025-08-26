# Password

Password is an input component for password values with a strength indicator and theming.

```ts
import { Password } from '@atlas/ui';
```

## Usage

```vue
<Password v-model="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `string` | — | Bound password value. |
| `feedback` | `boolean` | `true` | Displays a strength indicator panel. |
| `toggleMask` | `boolean` | `false` | Shows an icon to reveal the password. |
| `pt` | `PasswordPassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
- `header` – Content displayed above the feedback panel.
- `footer` – Content displayed below the feedback panel.
- `content` – Custom content inside the feedback panel.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `string` | Emitted when the value changes. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the input receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the input loses focus. |

Refer to the [PrimeVue Password API](https://primevue.org/password/#api).

