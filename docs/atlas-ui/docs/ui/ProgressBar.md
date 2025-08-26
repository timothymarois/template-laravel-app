# ProgressBar

Wrapper around PrimeVue ProgressBar to display task progress.

```ts
import { ProgressBar } from '@atlas/ui';
```

## Usage

```vue
<ProgressBar :value="50" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `value` | `number` | `0` | Current progress value. |
| `mode` | `'determinate' \| 'indeterminate'` | `'determinate'` | Progress display mode. |
| `showValue` | `boolean` | `true` | Whether to show the value label. |
| `pt` | `ProgressBarPassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
- `default` – Custom content displayed inside the progress bar.

### Events
- None.

Refer to the [PrimeVue ProgressBar API](https://primevue.org/progressbar/#api).
