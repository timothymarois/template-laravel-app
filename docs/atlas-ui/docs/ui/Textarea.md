# Textarea

Textarea is a multi-line text input with theming.

```ts
import { Textarea } from '@atlas/ui';
```

## Usage

```vue
<Textarea v-model="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `string` | `''` | Bound value of the textarea. |
| `autoResize` | `boolean` | `false` | Adjusts height based on content. |
| `rows` | `number` | `5` | Number of visible text lines. |
| `cols` | `number` | `20` | Visible character width. |
| `maxlength` | `number` | `undefined` | Maximum number of characters allowed. |
| `placeholder` | `string` | `''` | Placeholder text to display when empty. |
| `disabled` | `boolean` | `false` | Disables the textarea when true. |
| `pt` | `TextareaPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `update:modelValue` | `string` | Emitted when the value changes. |
| `input` | `Event` | Browser input event. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the textarea receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the textarea loses focus. |

Refer to the [PrimeVue Textarea API](https://primevue.org/textarea/#api).

