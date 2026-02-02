# Vue Guidelines

Vue and frontend coding standards for the application.

## Component Standards

All components must use `<script setup lang="ts">` with typed props:

```vue
<script setup lang="ts">
interface Props {
    title: string
    count?: number
}

const props = withDefaults(defineProps<Props>(), {
    count: 0,
})
</script>

<template>
    <div>
        <h1>{{ props.title }}</h1>
        <span>{{ props.count }}</span>
    </div>
</template>
```

**Rules:**
- Use `<script setup lang="ts">` for all components
- Use typed props only — no untyped props
- PascalCase for component file and component names
- 4-space indentation for all JS/TS/Vue/CSS files

## shadcn-vue

Components live in `resources/js/components/ui/`.

```bash
# Add new components
pnpm dlx shadcn-vue@latest add button
pnpm dlx shadcn-vue@latest add dialog
```

Theme variables are in `resources/css/base.css`.

## Icons

Use Lucide Icons as the primary icon library:

```vue
<script setup lang="ts">
import { Settings, Trash2, Plus } from 'lucide-vue-next'
</script>

<template>
    <Settings class="size-6 text-gray-800" />
    <Trash2 class="size-4" />
    <Plus class="size-5" />
</template>
```

Import only the icons you need. Browse icons at [Lucide](https://lucide.dev/).

## UI Consistency

- All buttons, links, and interactive elements **must have** `cursor-pointer`
- All interactive elements must have proper hover/focus states
- Follow existing component patterns for consistency

```vue
<!-- Good: Interactive element with cursor -->
<button class="cursor-pointer hover:bg-gray-100">
    Click me
</button>

<!-- Good: Using Button component (includes cursor) -->
<Button @click="handleClick">Click me</Button>
```

## Routing

Use Ziggy for named routes:

```typescript
// In script
route('posts.show', 123)  // http://localhost/posts/123
route('accounts.posts.show', [1, 123])

// In template
$route('posts.show', 123)
```

Use Inertia `router` for navigation:

```typescript
import { router } from '@inertiajs/vue3'

router.visit(route('posts.create'))
router.post(route('posts.store'), data)
```

Use Axios for background API calls:

```typescript
import axios from 'axios'

axios.get('/api/users')
    .then((response) => { /* handle success */ })
    .catch((error) => { /* handle error */ })
```

## Notifications

Use Sonner for toast notifications:

```typescript
import { toast } from 'vue-sonner'

toast.success('User saved successfully')
toast.error('Something went wrong')
toast.info('Processing...')
```

## SSR-Safe Code

Guard browser APIs with `isClient`:

```typescript
import { isClient } from '@/utils'

if (isClient) {
    // Safe: window, document, localStorage
    localStorage.setItem('key', 'value')
}
```

## File Structure

```
resources/js/
├── components/         # Reusable components
│   ├── ui/             # Base components (shadcn + custom)
│   ├── app/            # Application components (authenticated)
│   └── site/           # Website components (public)
├── pages/              # Inertia pages (organized by route)
│   ├── Index.vue       # Home page
│   └── admin/          # /admin/* routes
├── composables/        # Vue composables
├── tests/              # Vitest unit tests
└── utils/              # Utilities
```

## Code Quality

| Tool | Requirement |
|------|-------------|
| ESLint | Must pass |
| Stylelint | Must pass |
| TypeScript | Must compile without errors |
| Vitest | Tests required for new features |

Run checks before committing:

```bash
pnpm check:js    # ESLint + Stylelint + Vitest + build
```
