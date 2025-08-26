# InputMask

InputMask is an input component with a mask to control the format of user input.

```ts
import { InputMask } from '@atlas/ui';
```

## Usage

```vue
<InputMask mask="99/99/9999" v-model="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `string` | `''` | Bound value of the input. |
| `mask` | `string` | `''` | Mask pattern defining the format. |
| `slotChar` | `string` | `'_'` | Placeholder character used in the mask. |
| `autoClear` | `boolean` | `true` | Clears the input if it does not complete the mask. |
| `unmask` | `boolean` | `false` | Defines whether to remove mask characters in the bound value. |
| `disabled` | `boolean` | `false` | Disables the input when true. |
| `pt` | `InputMaskPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `string` | Emitted when the value changes. |
| `complete` | `{ originalEvent: Event }` | Triggered when the mask is fully completed. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the input receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the input loses focus. |

Refer to the [PrimeVue InputMask API](https://primevue.org/inputmask/#api).

