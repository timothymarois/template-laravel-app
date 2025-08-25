# PageHeader

Page-level header with optional breadcrumbs and tabs.

## Example
```ts
import { PageHeader } from '@atlas/ui';
```

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

## Props
- `breadcrumbs: Breadcrumb[]` – items for the breadcrumb trail. Default `[]`.
- `tabs: Tab[]` – optional tab navigation. Default `[]`.
- `title: string` – page title text. Default `''`.
- `linkComponent: string | object` – component used for links. Default `'a'`.
- `widthClass: string` – max width container class. Default `'max-w-screen-2xl'`.
- `hideTitle: boolean` – hide the title section. Default `false`.

## Slots
- `title` – replace the entire title area.
- `badge` – optional badge next to the title.
- `action` – action area in the header.

## Events
- None
