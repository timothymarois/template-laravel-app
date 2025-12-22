# UI Components

Base-level UI components for the application. This is the single source of truth for all UI primitives.

## Before Creating New Components

1. **Check shadcn-vue first**: https://www.shadcn-vue.com/docs/components
2. If available, install it: `pnpm dlx shadcn-vue@latest add <component>`
3. Only create custom components if shadcn doesn't have what you need

## Adding shadcn Components

```bash
pnpm dlx shadcn-vue@latest add <component>
```

New primitives are renamed to `*Base.vue` and enhanced wrappers are created as the main export.

## Component Naming

- **shadcn primitives**: `ButtonBase.vue`, `CardBase.vue`, `SelectBase.vue`
- **Enhanced wrappers**: `Button.vue`, `Card.vue`, `Select.vue` (main exports)

## Usage

```typescript
// Import enhanced components (recommended)
import { Button, Card, DataTable } from '@/components/ui';

// Or from specific folder
import { Button, ButtonMenu } from '@/components/ui/button';
```

## Rules

- Components must be **stateless** - no Inertia, routes, or auth
- Enhanced versions wrap `*Base` components with added features
- Add new custom components following the same pattern:
  1. Create folder: `ui/<component>/`
  2. Create component: `<ComponentName>.vue`
  3. Create barrel: `index.ts`
  4. Export from `ui/index.ts`
