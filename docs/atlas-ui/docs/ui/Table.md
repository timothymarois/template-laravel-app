# Table

Table is a wrapper around DataTable with column configuration and selection features.

```ts
import { Table } from '@atlas/ui';
```

## Usage

```vue
<Table
  :items="rows"
  :columns="columns"
  :activeColumnList="activeColumns"
  @sort="onSort"
/>
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| items | `any[]` | — | Table data rows. |
| itemTotal | `number` | `0` | Total number of items. |
| selected | `any[]` | `undefined` | Selected rows. |
| selectAll | `boolean` | `false` | Select all rows across pages. |
| columns | `any[]` | — | Column definitions. |
| activeColumnList | `any[]` | — | Ordered list of visible column keys. |
| defaultColumnList | `any[]` | `[]` | Default column keys. |
| size | `string` | `'small'` | Table size. |
| tableStyle | `string` | `'min-width: 50rem'` | Inline style for the table element. |
| dataKey | `string` | `'id'` | Unique field for row keys. |
| emptyLabel | `string` | `'No results'` | Message when no data is available. |
| hasSelectAll | `boolean` | `true` | Show select-all menu. |
| hasSelection | `boolean` | `false` | Enable row selection. |
| hasCustomizeColumns | `boolean` | `false` | Enable column customization UI. |
| scrollOffset | `number` | `0` | Additional offset for the scroll frame. |
| scrollable | `boolean` | `false` | Enable DataTable scrolling. |

### Slots

| Name | Description |
| ---- | ----------- |
| columnKey | Named slots for custom cell templates using the column key. |
| empty | Content shown when no rows exist. |

### Events

| Name | Payload | Description |
| ---- | ------- | ----------- |
| sort | `{ field: string; order: number }` | Emitted when sorting changes. |
| update:selected | `any[]` | Emitted with selected rows. |
| update:selectAll | `boolean` | Emitted when the select-all state changes. |
| update:activeColumnList | `string[]` | Emitted with new active column keys. |

Built on PrimeVue's DataTable. Refer to the [PrimeVue DataTable API](https://primevue.org/datatable/#api).
