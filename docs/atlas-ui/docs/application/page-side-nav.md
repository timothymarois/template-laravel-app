# PageSideNav

Secondary navigation within a page.

## Example
```ts
import { PageSideNav } from '@atlas/ui';
const sideItems = [
  {
    label: 'Group',
    children: [
      { label: 'Overview', href: '/overview' },
      { label: 'Reports', href: '/reports' }
    ]
  },
  { label: 'Settings', href: '/settings' }
];
```

```vue
<PageSideNav :items="sideItems" :linkComponent="Link" />
```

## Props
- `items: NavItem[]` – navigation links. Default `[]`.
- `linkComponent: string | object` – component used for links. Default `'a'`.

## Slots
- None.

## Events
- None
