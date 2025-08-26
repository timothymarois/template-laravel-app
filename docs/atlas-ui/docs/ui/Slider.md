# Slider

Slider is an input component for selecting a numeric value on a range.

```ts
import { Slider } from '@atlas/ui';
```

## Usage

```vue
<Slider v-model="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `number \| number[]` | `0` | Current value of the slider. |
| `min` | `number` | `0` | Minimum boundary of the range. |
| `max` | `number` | `100` | Maximum boundary of the range. |
| `step` | `number` | `1` | Incremental step for the value. |
| `orientation` | `'horizontal' \| 'vertical'` | `'horizontal'` | Orientation of the slider. |
| `range` | `boolean` | `false` | Enables two handles to define a range. |
| `pt` | `SliderPassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
None.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `change` | `{ originalEvent: Event, value: number \| number[] }` | Emitted when the value changes. |
| `slideend` | `{ originalEvent: Event, value: number \| number[] }` | Emitted after sliding ends. |

Refer to the [PrimeVue Slider API](https://primevue.org/slider/#api).

