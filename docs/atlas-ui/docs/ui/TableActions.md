# TableActions

Toolbar for batch table actions.

```ts
import { TableActions } from '@atlas/ui';
```

## Usage

```vue
<TableActions :selectedCount="selected.length" :menuItems="actions" @action="onAction" />
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| selectedCount | `number \| null` | `null` | Number of selected rows displayed next to actions. |
| menuItems | `MenuItem[]` | `[]` | Defines available actions. Each item supports `label`, `action`, optional `tooltip`, `disabled`, and nested `children` arrays for submenus. |

### Slots

None.

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| action | `string` | Emitted when an action is chosen. `'clear'` is emitted when the clear button is pressed. |

Refer to the [PrimeVue Menu API](https://primevue.org/menu/#api) for menu item options.
