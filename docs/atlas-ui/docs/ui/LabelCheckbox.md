# LabelCheckbox

Checkbox with an integrated label.

```ts
import { LabelCheckbox } from '@atlas/ui';
```

## Usage

```vue
<LabelCheckbox v-model="checked" label="Accept terms" inputId="accept" binary />
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `label` | `string` | `''` | Text label displayed next to the checkbox. |
| `pt` | `LabelCheckboxPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots

None.

### Events

None.

Refer to the [PrimeVue Checkbox API](https://primevue.org/checkbox/#api).

