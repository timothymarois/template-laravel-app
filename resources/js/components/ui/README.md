# UI Primitives (Layer 1)

shadcn-vue generated components. **Do NOT modify directly.**

## Adding Components

```bash
pnpm dlx shadcn-vue@latest add <component>
```

## Usage

Prefer importing composed wrappers from `@/components/composed` instead of directly from `ui/`.

```typescript
// Prefer this:
import { Button, Dialog } from '@/components/composed';

// Over this:
import { Button } from '@/components/ui/button';
```

## Why?

The `composed/` layer provides enhanced APIs (loading states, severity props, etc.) while `ui/` contains raw primitives meant to be extended, not used directly.
