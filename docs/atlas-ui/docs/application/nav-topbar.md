# NavTopbar

Global top navigation bar.

## Example
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

## Props
- `items: NavItem[]` – navigation links, supports nested `children`.
- `linkComponent: string | object` – component used for links. Default `'a'`.
- `logoLinkPath: string` – root link. Default `'/'`.
- `widthClass: string` – max width container class. Default `'max-w-screen-2xl'`.
- `backgroundClass: string` – optional classes for the top bar background and border. Defaults to a primary `900` tone in dark mode.
- `activeClass: string` – class applied to active links. Defaults to a white background with primary text.

## Slots
- `logo` – brand image or text.
- `actions` – optional action buttons.

## Events
- None
