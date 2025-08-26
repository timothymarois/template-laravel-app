# PageSideNav

Secondary navigation within a page.

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

## Usage

```vue
<PageSideNav :items="sideItems" :linkComponent="Link" />
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `items` | `NavItem[]` | `[]` | Navigation links. |
| `linkComponent` | `string \| object` | `'a'` | Component used for links. |

### Slots
- None.

### Events
- None.
