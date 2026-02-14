# App Components

Application-specific components for authenticated app functionality.

## Rules

1. **May use Inertia** - `usePage()`, `router.visit()`, `Link`
2. **May use authentication** - Access user state, permissions
3. **Route-aware** - Can define route-specific behavior
4. **App-specific** - Only for authenticated application pages

## Usage

```js
import AppLayout from '@/components/app/layout/AppLayout.vue';
import Sidebar from '@/components/app/navigation/Sidebar.vue';
import ProfileMenu from '@/components/app/navigation/ProfileMenu.vue';
import EditUserModal from '@/components/app/modals/EditUserModal.vue';
```

## Adding Modals

Place app-specific modals in `app/modals/`:

```vue
<script setup>
import { DialogConfirmation } from '@/components/ui';
import { useModal } from '@/composables';
import { useForm } from '@inertiajs/vue3';

const { activeState, onOpen, onClose } = useModal();
const showModal = activeState('MODAL_NAME');
</script>
```

## Distinction from Site

- `app/` = authenticated application, dashboard, management interfaces
- `site/` = public website, marketing pages, unauthenticated flows
