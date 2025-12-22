# Web Components (Layer 3b)

Application-specific components for public-facing website pages.

## Rules

1. **May use Inertia** - `usePage()`, `router.visit()`, `Link`
2. **Public-facing** - No authentication required by default
3. **Route-aware** - Can define route-specific behavior
4. **Web-specific** - Only for public website pages

## Structure

```
web/
├── layout/         # DefaultLayout
├── modals/         # (add public website modals here)
└── index.ts
```

## Usage

```typescript
import { DefaultLayout } from '@/components/web';
```

## Adding Components

When adding public website features:

1. Create layout variants in `web/layout/`
2. Create public modals in `web/modals/`
3. Export from appropriate `index.ts` files

## Distinction from Admin

- `admin/` = authenticated dashboard, management interfaces
- `web/` = public pages, marketing, unauthenticated flows
