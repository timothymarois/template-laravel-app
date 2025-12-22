# Admin Components (Layer 3a)

Application-specific components for admin/dashboard functionality.

## Rules

1. **May use Inertia** - `usePage()`, `router.visit()`, `Link`
2. **May use authentication** - Access user state, permissions
3. **Route-aware** - Can define route-specific behavior
4. **Admin-specific** - Only for admin/dashboard pages

## Structure

```
admin/
├── layout/         # AdminLayout, AppShell, AppTopbar
├── navigation/     # Sidebar, Topbar, ProfileMenu
├── page/           # Header, Footer, Content, SideNav, SideContent
├── modals/         # EditUserModal, DeleteUserModal, etc.
└── index.ts
```

## Usage

```typescript
import { AdminLayout, Sidebar, ProfileMenu } from '@/components/admin';
import { EditUserModal } from '@/components/admin/modals';
```

## Adding Modals

Place user-specific modals in `admin/modals/`. Example pattern:

```vue
<script setup>
import { DialogConfirmation } from '@/components/composed';
import { useModal } from '@/composables';
import { useForm } from '@inertiajs/vue3';

const { activeState, onOpen, onClose } = useModal();
const showModal = activeState('MODAL_NAME');

// Form and submission logic...
</script>
```

## Layouts

- `AdminLayout` - Main admin layout with sidebar, topbar, and modals
- `AppShell` - Core shell without route-specific configuration
- `AppTopbar` - Top bar component
