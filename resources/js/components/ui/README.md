# UI Components

Base-level, stateless UI components. Single source of truth for all UI primitives.

## Rules

1. **Stateless only** - No Inertia, routes, auth, or business logic
2. **Self-contained** - Each component in its own folder with `index.ts`
3. **Primitives use `*Base` suffix** - e.g., `InputBase.vue`, `CheckboxBase.vue`
4. **Enhanced versions are main export** - e.g., `Input.vue` wraps `InputBase.vue`

## Usage

```typescript
// From barrel export
import { Button, Input, DataTable } from '@/components/ui';

// From specific folder
import { Input, InputBase } from '@/components/ui/input';
import { Select } from '@/components/ui/select-popover';
```

## Adding shadcn Components

```bash
pnpm dlx shadcn-vue@latest add <component>
```

Check https://www.shadcn-vue.com/docs/components first before creating custom components.

## Adding Custom Components

1. Create folder: `ui/<component>/`
2. Create component: `<ComponentName>.vue`
3. Create barrel: `index.ts`
4. Export from `ui/index.ts`
