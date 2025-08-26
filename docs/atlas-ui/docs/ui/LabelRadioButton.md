# LabelRadioButton

Radio button with an integrated label.

```ts
import { LabelRadioButton } from '@atlas/ui';
```

## Usage

```vue
<LabelRadioButton
  v-model="selected"
  :value="'a'"
  label="Option A"
  name="options"
  inputId="option-a"
/>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `label` | `string` | `''` | Text label displayed next to the radio button. |
| `pt` | `LabelRadioButtonPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events

None.

Refer to the [PrimeVue RadioButton API](https://primevue.org/radiobutton/#api).

