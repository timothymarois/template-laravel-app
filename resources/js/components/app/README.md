# App Components

Application-specific components for authenticated app functionality.

## Rules

1. **May use Inertia** - `usePage()`, `router.visit()`, `Link`
2. **May use authentication** - Access user state, permissions
3. **Route-aware** - Can define route-specific behavior
4. **App-specific** - Only for authenticated application pages

## Structure

```
app/
├── layout/         # AppLayout, AppShell, AppTopbar
├── navigation/     # Sidebar, Topbar, ProfileMenu
├── page/           # Header, Footer, Content, SideNav, SideContent
├── modals/         # EditUserModal, DeleteUserModal, etc.
└── index.ts
```

## Usage

```typescript
import { AppLayout, Sidebar, ProfileMenu } from '@/components/app';
import { EditUserModal } from '@/components/app/modals';
```

## Design Wrappers

When app pages need different styling for a UI component, create a wrapper in `app/ui/`:

```
app/ui/
├── button/
│   ├── Button.vue    # Wrapper with app-specific styling
│   └── index.ts
└── index.ts
```

Example wrapper:

```vue
<script setup lang="ts">
import { Button as BaseButton } from '@/components/ui/button';
const props = defineProps<ButtonProps>();
</script>

<template>
    <BaseButton v-bind="props" class="app-specific-styles">
        <slot />
    </BaseButton>
</template>
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
