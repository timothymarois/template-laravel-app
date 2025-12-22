# Composed Components (Layer 2)

Components that combine multiple `ui/` primitives into reusable patterns.

## Rules

1. **Combines 2+ ui/ primitives** - If it only wraps one, consider if it belongs here
2. **Stateless** - No Inertia, no routes, no auth, no API calls
3. **Enhanced APIs** - Add loading states, severity props, presets, etc.
4. **Reusable** - Should work in any context (admin, web, or standalone)

## Structure

```
composed/
├── button/         # Button, ButtonMenu
├── dialog/         # Dialog, DialogConfirmation
├── drawer/         # Drawer, DrawerForm
├── card/           # Card
├── form/           # Input, Select, Checkbox, LabelField, Errors
├── display/        # Avatar, Badge, TooltipIcon
├── overlay/        # Menu, Popover
├── data/           # DataTable, TableActions, CustomizeColumns, Paginator
├── layout/         # ScrollFrame
├── editor/         # TipTap editor (requires optional deps: @tiptap/vue-3)
└── index.ts
```

## Usage

```typescript
import { Button, Dialog, DataTable } from '@/components/composed';

// Or category-specific:
import { DataTable, TableActions } from '@/components/composed/data';
```

## Adding Components

1. Create component in appropriate subdirectory
2. Export from subdirectory's `index.ts`
3. Ensure it's included in main `composed/index.ts`
