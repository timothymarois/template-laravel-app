# Component Architecture

Components follow a 2-layer architecture with clear separation between reusable primitives and application-specific logic.

## Overview

```
components/
├── ui/     # Base components (shadcn + custom)
├── app/    # Authenticated application components
└── site/   # Public marketing page components
```

| Layer | Purpose | Example |
|-------|---------|---------|
| `ui/` | Stateless, reusable primitives | `Button`, `Card`, `DataTable` |
| `app/` | App layouts, navigation, modals | `AppLayout`, `Sidebar` |
| `site/` | Public pages, marketing | `SiteLayout`, `Hero` |

## Component Reuse Priority

**Before writing ANY new component, follow this checklist:**

1. **Search existing components first** — Check if a component already exists in `components/ui/`, `components/app/`, or `components/site/`
2. **Check shadcn-vue** — If not found, check [shadcn-vue components](https://www.shadcn-vue.com/docs/components)
3. **Install from shadcn** — If available: `pnpm dlx shadcn-vue@latest add <component>`
4. **Create new only as last resort** — Only create custom components when nothing suitable exists

**Reuse over recreation** — Always prefer composing existing components over creating new ones.

## ui/ — Base Components

All base-level UI components live here. This is the single source of truth for all UI primitives.

**Rules:**
- **Isolated & Reusable** — No application-specific logic, no business rules
- **Single Purpose** — Each component does one thing well
- **Stateless** — No Inertia, routes, auth, or external dependencies
- **Props-driven** — All behavior controlled via props and events

**What belongs in ui/:**
- Buttons, inputs, cards, modals, tables, forms
- Generic data display components
- Layout primitives (grids, containers)

**What does NOT belong in ui/:**
- Components that fetch data
- Components with hardcoded routes or API calls
- Components tied to specific features

### Naming Convention

shadcn primitives use `*Base` suffix:
- `ButtonBase` — Raw shadcn button
- `Button` — Enhanced version with loading states, icons

## app/ — Application Components

Components for the main application (authenticated dashboard, management interfaces).

```
app/
├── layout/         # AppLayout, AppShell, AppTopbar
├── navigation/     # Sidebar, Topbar, ProfileMenu
├── page/           # Header, Footer, Content, SideNav
├── modals/         # EditUserModal, DeleteUserModal
└── index.ts
```

**Rules:**
- May use Inertia (`usePage()`, `router.visit()`, `Link`)
- May use authentication state
- May define route-specific behavior
- May contain business logic specific to app features
- Compose from `ui/` components

**What belongs in app/:**
- User management modals
- Navigation components using auth state
- Feature-specific forms and wizards
- Dashboard widgets with data fetching

## site/ — Website Components

Components for the public website (marketing pages, landing pages).

```
site/
├── layout/         # SiteLayout
└── index.ts
```

**Rules:**
- May use Inertia and routes
- Public-facing, no authentication required
- For marketing and public website pages
- Compose from `ui/` components

## Import Patterns

```typescript
// Base components
import { Button, Card, DataTable } from '@/components/ui';

// Application-specific
import { AppLayout, Sidebar } from '@/components/app';

// Website-specific
import { SiteLayout } from '@/components/site';
```

## Decision Tree

```
Need a component?
│
├─ 1. SEARCH FIRST: Does it already exist?
│     └─ Check ui/, app/, site/ directories
│     └─ If found → USE IT
│
├─ 2. CHECK SHADCN: Is it a standard UI pattern?
│     └─ Check https://www.shadcn-vue.com/docs/components
│     └─ If available → pnpm dlx shadcn-vue@latest add <name>
│
├─ 3. DETERMINE LOCATION: Where should new component live?
│     │
│     ├─ Is it reusable with NO app logic?
│     │  └─ YES → ui/<component>/
│     │
│     ├─ Does it use Inertia/routes/auth?
│     │  ├─ Authenticated app → app/<category>/
│     │  └─ Public website → site/<category>/
│     │
│     └─ Is it feature-specific (modals, widgets)?
│        ├─ App feature → app/modals/ or app/page/
│        └─ Site feature → site/page/
│
└─ 4. COMPOSE: Build new components from existing ui/ primitives
```

## Component Showcase

The application includes a comprehensive component showcase at `resources/js/pages/admin/components/` that demonstrates:

- Correct usage patterns for all UI components
- Prop combinations and variants (sizes, states, styles)
- Form integration patterns
- Real-world use cases

**Before implementing any UI component, review the showcase examples.**

Examples are organized by category:
- `forms/` — Input, Select, Checkbox, Switch, TagsInput
- `actions/` — Button, Dialog, Sheet, Menu, Command
- `display/` — Card, Badge, Alert, Avatar, Tabs, Toast
- `data/` — Table, Pagination, Actions
- `charts/` — Area, Bar, Line, Pie charts
