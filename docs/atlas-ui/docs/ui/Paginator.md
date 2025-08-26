# Paginator

Pagination controls for navigating between pages of data.

```ts
import { Paginator } from '@atlas/ui';
```

## Usage

```vue
<Paginator :totalRecords="100" :rows="10" />
```

## API

### Props
| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `totalRecords` | `number` | `0` | Number of total records. |
| `rows` | `number` | `0` | Data count per page. |
| `first` | `number` | `0` | Index of the first record to display. |
| `pageLinkSize` | `number` | `5` | Number of page links to display. |
| `rowsPerPageOptions` | `number[]` | — | Options for rows per page dropdown. |
| `currentPageReportTemplate` | `string` | `({currentPage} of {totalPages})` | Template for the current page report. |
| `pt` | `PaginatorPassThroughOptions` | — | Pass-through styling options. |

### Slots
| Name | Description |
| ---- | ----------- |
| `start` | Custom content before paginator controls. |
| `end` | Custom content after paginator controls. |

### Events
| Name | Payload | Description |
| ---- | ------- | ----------- |
| `page` | `{ first: number; rows: number; page: number; pageCount: number }` | Callback when the page changes. |
| `update:first` | `number` | Emitted when the `first` property changes. |
| `update:rows` | `number` | Emitted when the `rows` property changes. |

Refer to the [PrimeVue Paginator API](https://primevue.org/paginator/#api).
