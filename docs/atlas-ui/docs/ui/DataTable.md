# DataTable

DataTable displays tabular data with optional pagination and sorting.

```ts
import { DataTable } from '@atlas/ui';
import Column from 'primevue/column';
```

## Usage

```vue
<DataTable :value="rows">
  <Column field="name" header="Name" />
  <Column field="email" header="Email" />
</DataTable>
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| value | `any[]` | — | Table data rows. |
| dataKey | `string` | — | Unique identifier field for row keys. |
| paginator | `boolean` | `false` | Enables pagination controls. |
| rows | `number` | `0` | Number of rows to display. |
| first | `number` | `0` | Zero-based index of the first row. |
| totalRecords | `number` | `0` | Total number of records. Required for lazy loading. |
| sortField | `string` | — | Field name to sort by. |
| sortOrder | `1 \| -1` | `1` | Sort order: `1` for asc, `-1` for desc. |
| multiSortMeta | `SortMeta[]` | `null` | Sort metadata for multiple column sorting. |
| filters | `Record<string, any>` | `{}` | Filter constraints per column. |
| selection | `any \| any[]` | `null` | Selected row or rows. |
| selectionMode | `'single' \| 'multiple'` | `null` | Selection mode. |
| rowHover | `boolean` | `false` | Highlights rows on hover. |
| resizableColumns | `boolean` | `false` | Enables column resizing. |
| reorderableColumns | `boolean` | `false` | Allows column reordering. |
| reorderableRows | `boolean` | `false` | Allows row drag-and-drop. |
| showGridlines | `boolean` | `false` | Displays cell borders. |
| stripedRows | `boolean` | `false` | Applies zebra striping. |
| scrollable | `boolean` | `false` | Enables scrolling for long tables. |
| scrollHeight | `string` | `null` | Height of the scroll viewport. |
| loading | `boolean` | `false` | Displays a loading overlay. |
| virtualScrollerOptions | `VirtualScrollerProps` | `null` | Configuration for virtual scrolling. |

### Slots
| Name | Description |
| ---- | ----------- |
| header | Custom content above the table. |
| footer | Custom content below the table. |
| empty | Content when no data is present. |
| loading | Custom loading template. |
| paginatorstart | Content at the start of the paginator. |
| paginatorend | Content at the end of the paginator. |
| expansion | Template for row expansion. |
| groupheader | Template for group headers. |
| groupfooter | Template for group footers. |

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| row-click | `{ originalEvent: Event; data: any }` | Emitted when a row is clicked. |
| row-dblclick | `{ originalEvent: Event; data: any }` | Emitted when a row is double-clicked. |
| sort | `{ field: string; order: number }` | Emitted on sorting. |
| page | `{ first: number; rows: number }` | Emitted on page change. |
| filter | `any` | Emitted on filtering. |
| row-select | `{ originalEvent: Event; data: any }` | Emitted when a row is selected. |
| row-unselect | `{ originalEvent: Event; data: any }` | Emitted when a row is unselected. |
| row-expand | `{ originalEvent: Event; data: any }` | Emitted when a row is expanded. |
| row-collapse | `{ originalEvent: Event; data: any }` | Emitted when a row is collapsed. |

Refer to the [PrimeVue DataTable API](https://primevue.org/datatable/#api). For column definitions, pagination, and CSV export, see the [Table guide](../table.md).
