# MultiSelect

MultiSelect is a dropdown for selecting multiple options.

```ts
import { MultiSelect } from '@atlas/ui';
```

## Usage

```vue
<MultiSelect v-model="selected" :options="options" optionLabel="name" optionValue="value" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `modelValue` | `any[]` | `[]` | Selected option values. |
| `options` | `any[]` | `[]` | List of available options. |
| `optionLabel` | `string` | `undefined` | Property name or getter for the label. |
| `optionValue` | `string` | `undefined` | Property name or getter for the value. |
| `placeholder` | `string` | `''` | Placeholder text when no selection exists. |
| `filter` | `boolean` | `false` | Enables client-side filtering. |
| `display` | `'comma' \| 'chip'` | `'comma'` | Display mode of the selected items. |
| `pt` | `MultiSelectPassThroughOptions` | `undefined` | Pass-through options to customize internal elements. |

### Slots
- `value` – Template for the selected value display.
- `option` – Template for an option in the panel.
- `header` – Custom content above the options.
- `footer` – Custom content below the options.
- `empty` – Content shown when no options are available.

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `change` | `{ originalEvent: Event, value: any[] }` | Emitted when the selection changes. |
| `focus` | `{ originalEvent: FocusEvent }` | Fired when the input receives focus. |
| `blur` | `{ originalEvent: FocusEvent }` | Fired when the input loses focus. |

Refer to the [PrimeVue MultiSelect API](https://primevue.org/multiselect/#api).

