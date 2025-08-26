# NavTopbar

Global top navigation bar.

```ts
import { NavTopbar } from '@atlas/ui';

const navItems = [
  { label: 'Dashboard', href: '/' },
  {
    label: 'Users',
    href: '/users',
    children: [
      { label: 'List', href: '/users' },
      { label: 'Create', href: '/users/create' }
    ]
  }
];
```

## Usage

```vue
<NavTopbar :items="navItems" :linkComponent="Link" logoLinkPath="/">
  <template #logo>
    <img src="/logo.svg" alt="Logo" />
  </template>
  <template #actions>
    <Button label="Logout" />
  </template>
</NavTopbar>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `items` | `NavItem[]` | `[]` | Navigation links, supports nested `children`. |
| `linkComponent` | `string \| object` | `'a'` | Component used for links. |
| `logoLinkPath` | `string` | `'/'` | Root link. |
| `widthClass` | `string` | `'max-w-screen-2xl'` | Max width container class. |
| `backgroundClass` | `string` | `''` | Optional classes for the top bar background and border. Defaults to a primary `900` tone in dark mode. |
| `activeClass` | `string` | `''` | Class applied to active links. Defaults to a white background with primary text. |

### Slots
- `logo` – brand image or text.
- `actions` – optional action buttons.

### Events
- None.
