# PageHeader

Page-level header with optional breadcrumbs and tabs.

```ts
import { PageHeader } from '@atlas/ui';
```

## Usage

```vue
<PageHeader
  :breadcrumbs="[{ href: '/', title: 'Home' }]"
  :tabs="[{ href: '/users', title: 'Users' }]"
  title="Dashboard"
>
  <template #badge>
    <Badge label="New" />
  </template>
  <template #action>
    <Button label="Edit" />
  </template>
</PageHeader>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `breadcrumbs` | `Breadcrumb[]` | `[]` | Items for the breadcrumb trail. |
| `tabs` | `Tab[]` | `[]` | Optional tab navigation. |
| `title` | `string` | `''` | Page title text. |
| `linkComponent` | `string \| object` | `'a'` | Component used for links. |
| `widthClass` | `string` | `'max-w-screen-2xl'` | Max width container class. |
| `hideTitle` | `boolean` | `false` | Hide the title section. |

### Slots
- `title` – replace the entire title area.
- `badge` – optional badge next to the title.
- `action` – action area in the header.

### Events
- None.
