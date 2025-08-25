# Table

Atlas DataTable component extends PrimeVue's DataTable with Atlas styling and utilities.

## Table of Contents
- [Basic Usage](#basic-usage)
- [Columns](#columns)
- [Pagination and Sorting](#pagination-and-sorting)
- [CSV Export](#csv-export)
- [Application Table](#application-table)
- [Table Actions](#table-actions)
- [Customize Columns](#customize-columns)

## Basic Usage
```ts
import { DataTable } from '@atlas/ui';
import Column from 'primevue/column';
```

```vue
<DataTable :value="rows">
  <Column field="name" header="Name" />
  <Column field="email" header="Email" />
</DataTable>
```

## Columns
Define columns using PrimeVue's `<Column>` components. Slot templates may be used for custom cell content.

## Pagination and Sorting
Enable pagination and sorting via PrimeVue props such as `paginator`, `rows`, `sortField`, and `sortOrder`.

```vue
<DataTable :value="rows" paginator :rows="10" sortField="name" :sortOrder="1" />
```

## CSV Export
Call the `exportCSV` method on the component reference to download data.

```vue
<DataTable ref="dt" :value="rows" />
<Button @click="dt.exportCSV()" label="Export" />
```

Refer to the [PrimeVue DataTable API](https://primevue.org/datatable/#api) for all available options.

## Application Table
The `Table` component builds on `DataTable` to add selection and column management utilities.

```ts
import { Table } from '@atlas/ui';
```

```vue
<Table
  :items="rows"
  :columns="columns"
  :activeColumnList="active"
  @sort="onSort"
/> 
```

### Props
- `items: any[]` – table rows.
- `itemTotal: number` – total item count.
- `selected: any[]` – selected rows.
- `selectAll: boolean` – select all rows across pages.
- `columns: any[]` – column definitions.
- `activeColumnList: any[]` – ordered list of visible column keys.
- `defaultColumnList: any[]` – default column keys.
- `size: string` – table size; default `'small'`.
- `tableStyle: string` – inline style for table width.
- `dataKey: string` – unique key field; default `'id'`.
- `emptyLabel: string` – message when no data; default `'No results'`.
- `hasSelectAll: boolean` – show select-all menu.
- `hasSelection: boolean` – enable row selection.
- `hasCustomizeColumns: boolean` – enable column customization UI.
- `scrollOffset: number` – additional offset for the scroll frame.
- `scrollable: boolean` – enable DataTable scrolling.

### Events
- `sort` – emitted with `{ field, order }` when sorting changes.
- `update:selected` – emitted with selected rows.
- `update:selectAll` – emitted when the select-all state changes.
- `update:activeColumnList` – emitted with new active column keys.

### Slots
- `columnKey` – named slots for custom cell templates using the column key.
- `empty` – content shown when no results.

Built on PrimeVue's DataTable. See the [DataTable API](https://primevue.org/datatable/#api) for additional options.

## Table Actions
`TableActions` displays the number of selected rows and contextual actions.

```ts
import { TableActions } from '@atlas/ui';
```

```vue
<TableActions :selectedCount="selected.length" :menuItems="actions" @action="onAction" />
```

### Props
- `selectedCount: number` – number of selected rows.
- `menuItems: any[]` – action definitions.

### Events
- `action` – emitted with the action identifier. `'clear'` is emitted when the clear button is pressed.

## Customize Columns
`TableCustomizeColumns` renders a popover to toggle and reorder columns.

```ts
import { TableCustomizeColumns } from '@atlas/ui';
```

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

### Props
- `columns: any[]` – full column definitions.
- `activeColumnList: any[]` – visible column keys.
- `defaultColumnList: any[]` – default column keys.

### Events
- `update` – emitted with new array of active column keys.

### Slots
- `trigger` – element that toggles the popover.
