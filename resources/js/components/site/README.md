# Site Components

Components for the public website (marketing pages, landing pages, unauthenticated flows).

## Rules

1. **May use Inertia** - `usePage()`, `router.visit()`, `Link`
2. **Public-facing** - No authentication required by default
3. **Route-aware** - Can define route-specific behavior
4. **Website-specific** - Only for public website pages

## Structure

```
site/
├── layout/         # SiteLayout
└── index.ts
```

## Usage

```typescript
import { SiteLayout } from '@/components/site';

// Legacy aliases still available:
import { PublicLayout, DefaultLayout } from '@/components/site';
```

## Design Wrappers

When site pages need different styling for a UI component, create a wrapper in `site/ui/`:

```
site/ui/
├── button/
│   ├── Button.vue    # Wrapper with site styling (rounded, larger)
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
    <BaseButton v-bind="props" class="rounded-full shadow-lg">
        <slot />
    </BaseButton>
</template>
```

## Distinction from App

- `app/` = authenticated application, dashboard, management interfaces
- `site/` = public website, marketing pages, unauthenticated flows
