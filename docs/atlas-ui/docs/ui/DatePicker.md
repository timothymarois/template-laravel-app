# DatePicker

DatePicker is an input component for selecting dates.

```ts
import { DatePicker } from '@atlas/ui';
```

## Usage

```vue
<DatePicker v-model="date" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `Date \| Date[]` | — | Selected date or range. |
| `selectionMode` | `'single' \| 'multiple' \| 'range'` | `'single'` | Number of dates that can be selected. |
| `showIcon` | `boolean` | `false` | Displays a button with calendar icon. |
| `showTime` | `boolean` | `false` | Enables time selection. |
| `pt` | `DatePickerPassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
- `header` – Custom content above the calendar.
- `footer` – Custom content below the calendar.
- `date` – Template for a day cell.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `show` | `{ originalEvent: Event }` | Fired when the overlay is opened. |
| `hide` | `{ originalEvent: Event }` | Fired when the overlay is closed. |
| `month-change` | `{ month: number, year: number }` | Fired when the month changes. |
| `year-change` | `{ month: number, year: number }` | Fired when the year changes. |

Refer to the [PrimeVue DatePicker API](https://primevue.org/datepicker/#api).

