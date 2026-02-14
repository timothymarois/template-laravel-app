# Site Components

Components for the public website (marketing pages, landing pages, unauthenticated flows).

## Rules

1. **May use Inertia** - `usePage()`, `router.visit()`, `Link`
2. **Public-facing** - No authentication required by default
3. **Route-aware** - Can define route-specific behavior
4. **Website-specific** - Only for public website pages

## Usage

```js
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
```

## Distinction from App

- `app/` = authenticated application, dashboard, management interfaces
- `site/` = public website, marketing pages, unauthenticated flows
