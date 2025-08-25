# ProfileMenu

User avatar dropdown menu for account actions.

## Example
```ts
import { computed } from 'vue';
import { ProfileMenu } from '@atlas/ui';

const profileMenuItems = computed(() => [
  { separator: true },
  { label: 'Company Name', icon: 'pi pi-building-columns', href: '/' },
  { separator: true },
  { label: 'Billing & Plan', icon: 'pi pi-credit-card', href: '/' },
  { label: 'Manage Access', icon: 'pi pi-users', href: '/' },
  { label: 'Integrations', icon: 'pi pi-objects-column', href: '/' },
  { label: 'Settings', icon: 'pi pi-cog', href: '/' },
  { label: 'Status page', icon: 'pi pi-cog', href: 'https://google.com', external: true },
  { separator: true },
  { label: 'Logout', icon: 'pi pi-sign-out', href: '/logout' }
]);
```

```vue
<ProfileMenu
  :user="user"
  :items="profileMenuItems"
  :avatar-only="true"
  headerLink="/"
  :linkComponent="RouterLink"
/>
```

## Props
- `avatarOnly: boolean` – render only the avatar as trigger. Default `false`.
- `user: object` – user data containing `id`, `name`, and `email`.
- `items: any[]` – menu item definitions.
- `headerLink: string | null` – link for the header when `avatarOnly` is true. Default `null`.
- `linkComponent: string | object` – component used for internal links. Default `'a'`.

## Slots
- `trigger` – custom trigger element. Slot props: `{ user, toggle }`.
- `avatarMenuHeader` – content above menu items when `avatarOnly` is true.

## Events
- None
