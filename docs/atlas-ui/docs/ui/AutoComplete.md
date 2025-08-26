# AutoComplete

AutoComplete is an input with dynamic suggestions.

```ts
import { AutoComplete } from '@atlas/ui';
```

## Usage

```vue
<AutoComplete v-model="value" :suggestions="items" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `any` | — | Bound value of the input. |
| `suggestions` | `any[]` | `[]` | Options available to display. |
| `dropdown` | `boolean` | `false` | Displays a button to show all options. |
| `multiple` | `boolean` | `false` | Allows selecting multiple values. |
| `pt` | `AutoCompletePassThroughOptions` | — | Pass-through options to customize internal elements. |

### Slots
- `option` – Template for a suggestion item.
- `header` – Custom content above the list.
- `footer` – Custom content below the list.
- `empty` – Content shown when there are no results.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `complete` | `{ originalEvent: Event, query: string }` | Callback to load suggestions. |
| `item-select` | `{ originalEvent: Event, value: any }` | Fired when an item is selected. |
| `item-unselect` | `{ originalEvent: Event, value: any }` | Fired when an item is unselected. |
| `clear` | `{ originalEvent: Event }` | Fired when the input is cleared. |

Refer to the [PrimeVue AutoComplete API](https://primevue.org/autocomplete/#api).

