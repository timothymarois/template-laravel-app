# NavSidebar

Vertical navigation sidebar.

```ts
import { NavSidebar } from '@atlas/ui';

const navItems = [
  {
    children: [
      { label: 'Home', href: '/' },
      { label: 'Users', href: '/users' }
    ]
  },
  {
    children: [
      { label: 'Settings', href: '/settings' }
    ]
  }
];
```

## Usage

```vue
<NavSidebar :items="navItems" :linkComponent="Link" logoLinkPath="/">
  <template #logo>
    <img src="/logo.svg" alt="Logo" />
  </template>
  <template #actions>
    <Button label="Settings" />
  </template>
</NavSidebar>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `items` | `NavItem[]` | `[]` | Sections and links displayed in the sidebar. |
| `logoLinkPath` | `string` | `'/'` | Root link. |
| `linkComponent` | `string \| object` | `'a'` | Component used for links. |
| `backgroundClass` | `string` | `''` | Optional classes for the sidebar background and border. Defaults to a primary `900` tone in dark mode. |
| `activeClass` | `string` | `''` | Class applied to active links. Defaults to a white background with primary text/icons. |

### Slots
- `logo` – brand image or text.
- `actions` – footer actions.

### Events
- None.
