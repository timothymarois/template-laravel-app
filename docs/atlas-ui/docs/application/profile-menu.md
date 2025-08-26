# ProfileMenu

User avatar dropdown menu for account actions.

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

## Usage

```vue
<ProfileMenu
  :user="user"
  :items="profileMenuItems"
  :avatar-only="true"
  headerLink="/"
  :linkComponent="RouterLink"
/>
```

## API

### Props
| Prop | Type | Default | Description |
| ---- | ---- | ------- | ----------- |
| `avatarOnly` | `boolean` | `false` | Render only the avatar as trigger. |
| `user` | `object` | `null` | User data containing `id`, `name`, and `email`. |
| `items` | `any[]` | `[]` | Menu item definitions. |
| `headerLink` | `string \| null` | `null` | Link for the header when `avatarOnly` is true. |
| `linkComponent` | `string \| object` | `'a'` | Component used for internal links. |

### Slots
- `trigger` – custom trigger element. Slot props: `{ user, toggle }`.
- `avatarMenuHeader` – content above menu items when `avatarOnly` is true.

### Events
- None.
