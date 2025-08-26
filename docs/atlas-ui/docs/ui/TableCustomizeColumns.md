# TableCustomizeColumns

TableCustomizeColumns displays a popover to show and reorder table columns.

```ts
import { TableCustomizeColumns } from '@atlas/ui';
```

## Usage

```vue
<TableCustomizeColumns
  :columns="columns"
  :activeColumnList="active"
  :defaultColumnList="defaults"
  @update="val => active = val"
>
  <template #trigger="{ toggle }">
    <button @click="toggle">Columns</button>
  </template>
</TableCustomizeColumns>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| columns | `any[]` | — | Column definitions. |
| activeColumnList | `any[]` | — | Visible column keys. |
| defaultColumnList | `any[]` | — | Default visible column keys. |

### Slots

| Name | Description |
| ---- | ----------- |
| trigger | Custom element that toggles the popover. |

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| update | `string[]` | Emitted with new array of active column keys. |

Refer to the [PrimeVue Popover API](https://primevue.org/popover/#api).
