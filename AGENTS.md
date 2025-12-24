# AGENTS

This document defines the standards and contribution rules for all Agents working on the application.

---

## Purpose

The application is a unified system composed of **Laravel**, **Vue 3 + Inertia**, and **TailwindCSS with shadcn-vue**.

**Agents must follow these rules:**

* PRDs override all assumptions. All PRDs live in `/docs/prd/`.
* No contributor may implement behavior not defined in PRDs or unless explicitly asked by the user.
* If clarity is missing, request clarification before committing code.
* Review existing documentation before making changes.

---

## Tech Stack

### Backend

* Laravel 12+ (PHP 8.4+)
* Redis (queue + cache via Horizon)
* Inertia server adapter

### Frontend

* Vue 3 (`<script setup lang="ts">`)
* Inertia.js Vue adapter
* TailwindCSS 4
* shadcn-vue (Radix Vue primitives)
* Lucide Icons

---

## Core Principles

1. Maintain strict consistency between Laravel and Vue code.
2. Only implement behaviors explicitly defined in PRDs.
3. **Keep Laravel controllers thin** and move all **business logic into Services**.
4. Every file must have a clear, single purpose.
5. Use deterministic logic with no hidden side effects.
6. Follow the conventions below to ensure maintainability.

---

## Laravel/PHP Guidelines

### General PHP & Laravel Standards

* All PHP files must use `declare(strict_types=1);`.
* Follow **PSR-12** and run **Laravel Pint** before committing.
* Use modern PHP syntax (enums, DTOs, readonly properties where appropriate).
* Avoid helper-based architecture; favor dependency-injected services.
* Never use `use function` imports.

### Controllers

* Keep controllers thin.
* Do not contain business logic or data transformation beyond request handling.
* Delegate all work to Services or Actions.
* Use Form Requests for validation.

### Services & Actions

* Place domain logic inside `app/Services`.
* Services handle business processes; Actions handle small, single-purpose operations.
* Services should not directly touch HTTP layer concerns.
* Prefer constructor injection for all dependencies.

### Models & Database Layer

* Keep models lightweight.
* Move heavy logic to Services (extend `ModelService` where applicable).
* Use Query Builder or dedicated repository-like Service methods for complex queries.
* Avoid N+1 queries; use eager loading.

### Migrations & Data

* Migrations must be idempotent and reversible.
* Use proper indexes for frequently queried fields.
* Avoid storing computed/transient data in the database.

### Testing

* Use factories for model creation.
* Write Feature tests for endpoints and workflows.
* Write Unit tests for Services.

---

## Vue/Frontend Guidelines

### Component Standards

* Use `<script setup lang="ts">` for all components.
* Use typed props only – no untyped props.
* PascalCase for component file and component names.
* 4-space indentation for all JS/TS/Vue/CSS files.

### shadcn-vue

* Components live in `resources/js/components/ui/`.
* Add new components via: `pnpm dlx shadcn-vue@latest add <component>`
* Theme variables are in `resources/css/base.css`.

### Icons

* Use Lucide Icons as the primary icon library.
* Import only the icons you need: `import { Settings } from 'lucide-vue-next'`

### UI Consistency

* All buttons, links, and interactive elements **must have** `cursor-pointer`.
* All interactive elements must have proper hover/focus states.
* Follow existing component patterns for consistency.

### Routing

* Use Ziggy for named routes: `route('posts.show', id)`
* In templates, use `$route()` instead of `route()`.
* Use Inertia `router` for navigation, Axios for background API calls.

### Notifications

* Use Sonner for toast notifications: `toast.success('Message')`

---

## File & Directory Structure

### Laravel
```
app/
├── Console/Commands/   # Artisan commands
├── Enums/              # PHP backed enums
├── Http/
│   ├── Concerns/       # Reusable controller traits
│   ├── Controllers/    # Thin controllers
│   └── Requests/       # Form Request validation
├── Models/             # Eloquent models
├── Integrations/       # Third-party APIs, external services
├── Services/           # Business logic
│   ├── Models/         # Per-model services (extend ModelService)
│   └── <Domain>/       # Feature services grouped by domain
└── Support/            # Small helpers, traits, utilities
```

### Vue
```
resources/js/
├── components/         # Reusable components (see Component Architecture below)
│   ├── ui/             # Base components (shadcn + custom enhanced)
│   ├── app/            # Application components (authenticated app)
│   └── site/           # Website components (public marketing pages)
├── pages/              # Inertia pages (organized by route)
│   ├── Index.vue       # Home page
│   └── admin/          # /admin/* routes
├── composables/        # Vue composables
├── tests/              # Vitest unit tests
└── utils/              # Utilities
```

---

## Component Architecture

The UI components follow a simple 2-layer architecture:

### Layer Overview

| Layer | Location | Purpose | Rules |
|-------|----------|---------|-------|
| **ui/** | `components/ui/` | Base components (shadcn + enhanced) | [README](resources/js/components/ui/README.md) |
| **app/** | `components/app/` | Application components | [README](resources/js/components/app/README.md) |
| **site/** | `components/site/` | Website components | [README](resources/js/components/site/README.md) |

### ui/ - Base Components

All base-level UI components live here. This is the single source of truth for all UI primitives.

**Before creating a new component:**
1. Check if shadcn-vue has the component: https://www.shadcn-vue.com/docs/components
2. If available, install it: `pnpm dlx shadcn-vue@latest add <component>`
3. Only create a custom component if shadcn doesn't have what you need

**Rules:**
- Contains ALL base-level components (shadcn + custom)
- Components are **stateless** - no Inertia, routes, or auth
- shadcn primitives use `*Base` suffix (ButtonBase, CardBase)
- Enhanced versions wrap `*Base` components with added features (loading states, icons, etc.)

### app/ - Application Components

Components for the main application (authenticated dashboard, management interfaces).

```
app/
├── layout/         # AppLayout, AppShell, AppTopbar
├── navigation/     # Sidebar, Topbar, ProfileMenu
├── page/           # Header, Footer, Content, SideNav, SideContent
├── modals/         # EditUserModal, DeleteUserModal
└── index.ts
```

**Rules:**
- May use Inertia (`usePage()`, `router.visit()`, `Link`)
- May use authentication state
- May define route-specific behavior
- For authenticated application functionality

### site/ - Website Components

Components for the public website (marketing pages, landing pages, unauthenticated flows).

```
site/
├── layout/         # SiteLayout
└── index.ts
```

**Rules:**
- May use Inertia and routes
- Public-facing, no authentication required
- For marketing and public website pages

### Import Patterns

```typescript
// Base components
import { Button, Card, DataTable } from '@/components/ui';

// Application-specific
import { AppLayout, Sidebar } from '@/components/app';

// Website-specific
import { SiteLayout } from '@/components/site';
```

### Decision Tree: Where Does My Component Go?

```
Adding a new component?
│
├─ Is it a UI primitive (button, input, card)?
│  ├─ Available in shadcn? → pnpm dlx shadcn-vue@latest add <name>
│  └─ Custom enhanced? → Add to ui/<component>/
│
├─ Uses Inertia/routes/auth?
│  ├─ Authenticated app → Add to app/<category>/
│  └─ Public website → Add to site/<category>/
│
└─ Application-specific (modals, page sections)?
   ├─ App → app/modals/ or app/page/
   └─ Site → site/page/
```

### Pages Organization

Pages follow the route structure with clear separation between site and app:

```
pages/
├── Index.vue           # / (public home) - uses SiteLayout
├── Login.vue           # /login (guest) - uses SiteLayout
├── Register.vue        # /register (guest) - uses SiteLayout
└── admin/              # /admin/* (authenticated) - uses AppLayout
    ├── Index.vue       # /admin (dashboard)
    └── users/          # /admin/users/*
        ├── Index.vue   # /admin/users
        ├── Show.vue    # /admin/users/:id
        └── SimpleTable.vue
```

### Route Naming Convention

Routes use prefixed names for clarity:

| Route | Name | Layout |
|-------|------|--------|
| `/` | `home` | SiteLayout |
| `/login` | `login` | SiteLayout |
| `/register` | `register` | SiteLayout |
| `/admin` | `admin.index` | AppLayout |
| `/admin/users` | `admin.users.index` | AppLayout |

Use `$route('admin.users.index')` in templates, `route('admin.users.index')` in scripts.

---

## Required Checks

Run these commands before committing changes:

### Quick Commands
```bash
pnpm check        # Run ALL checks (PHP + JS)
pnpm check:php    # Run PHP checks only (Pint, Larastan, Pest)
pnpm check:js     # Run JS checks only (ESLint, Stylelint, Vitest, Build)
```

### Linting & Testing
```bash
pnpm lint         # Check ESLint issues (JS/Vue)
pnpm lint:fix     # Auto-fix ESLint issues
pnpm lint:css     # Check Stylelint issues (CSS)
pnpm lint:css:fix # Auto-fix Stylelint issues
pnpm test         # Run JS unit tests (Vitest)
```

### Individual Tools (if needed)
```bash
./vendor/bin/pint            # PHP code style
./vendor/bin/phpstan analyse # Static analysis
./vendor/bin/pest            # PHP tests (or: composer test)
pnpm test                    # JS tests (Vitest)
```

All checks must pass before committing.

---

## Code Quality Standards

| Layer      | Tool         | Requirement                     |
|------------|--------------|---------------------------------|
| PHP        | Laravel Pint | Must pass                       |
| PHP        | Larastan     | Level 5 minimum                 |
| PHP        | Pest         | Tests required for new features |
| JS/Vue     | ESLint       | Must pass                       |
| CSS        | Stylelint    | Must pass                       |
| JS/Vue     | Vitest       | Tests required for new features |
| TypeScript | tsc          | Must compile without errors     |

---

All contributions must follow this document, the referenced guides, all PRDs, and the README. Non-compliant contributions will be rejected.
