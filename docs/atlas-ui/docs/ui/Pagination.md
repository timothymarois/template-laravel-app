# Pagination

Simple pagination component rendering link elements.

```ts
import { Pagination } from '@atlas/ui';
```

## Usage

```vue
<Pagination :links="links" />
```

## API

### Props

| Name | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `links` | `PaginationLink[]` | — | Array of pagination link objects. |
| `linkComponent` | `string \| object` | `'a'` | Component used for each link. |
| `pt` | `PaginationPassThroughOptions` | — | Pass-through styling options. |

### Slots

None.

### Events

None.

No PrimeVue equivalent.
