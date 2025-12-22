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
* 4-space indentation for all JS/TS/Vue files.

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
│   ├── ui/             # Layer 1: Primitives (shadcn-vue)
│   ├── composed/       # Layer 2: Composed components
│   ├── admin/          # Layer 3a: Admin application components
│   └── web/            # Layer 3b: Public website components
├── pages/              # Inertia pages (organized by route)
│   ├── Index.vue       # Home page
│   └── Users/          # /users/* routes
├── composables/        # Vue composables
├── tests/              # Vitest unit tests
└── utils/              # Utilities
```

---

## Component Architecture

The UI components follow a 3-layer architecture for clear separation of concerns:

### Layer Overview

| Layer | Location | Purpose | Rules |
|-------|----------|---------|-------|
| **ui/** | `components/ui/` | Primitives (shadcn-vue) | CLI-managed, do NOT modify directly |
| **composed/** | `components/composed/` | Composed components | Combines ui/ primitives, stateless |
| **admin/** | `components/admin/` | Admin app components | Uses Inertia, routes, auth |
| **web/** | `components/web/` | Public web components | Uses Inertia, routes |

### Decision Guide

Use this simple test to determine where a component belongs:

1. **Is it a single-purpose primitive?** → `ui/` (but usually already exists via shadcn)
2. **Does it combine 2+ ui/ components?** → `composed/`
3. **Does it use `usePage()`, `router.visit()`, or auth?** → `admin/` or `web/`

### Layer 1: `ui/` - Primitives

shadcn-vue generated components. **Never modify these directly.**

```bash
# Add new primitives via CLI:
pnpm dlx shadcn-vue@latest add <component>
```

Examples: `Button`, `Input`, `Dialog`, `Sheet`, `Table`, `Card`, etc.

### Layer 2: `composed/` - Composed Components

Components that combine multiple ui/ primitives into reusable patterns.

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
├── editor/         # TipTap editor (requires optional deps)
└── index.ts
```

**Rules:**
- Combine ui/ primitives with enhanced APIs (severity, loading states, etc.)
- Must remain **stateless** - no Inertia, no routes, no auth
- No API calls or data fetching

### Layer 3a: `admin/` - Admin Application Components

Components specific to admin/dashboard functionality.

```
admin/
├── layout/         # AdminLayout, AppShell, AppTopbar
├── navigation/     # Sidebar, Topbar, ProfileMenu
├── page/           # Header, Footer, Content, SideNav, SideContent
├── modals/         # EditUserModal, DeleteUserModal
└── index.ts
```

**Rules:**
- May use Inertia (`usePage()`, `router.visit()`, `Link`)
- May use authentication state
- May define route-specific behavior

### Layer 3b: `web/` - Public Website Components

Components for public-facing pages.

```
web/
├── layout/         # DefaultLayout
├── modals/         # (add public website modals here)
└── index.ts
```

### Import Patterns

```typescript
// Recommended: Import from layer barrels
import { Button, Dialog, DataTable } from '@/components/composed';
import { AdminLayout, Sidebar } from '@/components/admin';
import { DefaultLayout } from '@/components/web';

// Direct ui/ import (rare - prefer composed wrappers)
import { Button } from '@/components/ui/button';

// Category-specific import
import { DataTable, TableActions } from '@/components/composed/data';
```

### Pages Organization

Pages follow the route structure with clear separation between public and admin:

```
pages/
├── Index.vue           # / (public home)
├── Login.vue           # /login (guest)
├── Register.vue        # /register (guest)
└── admin/              # /admin/* (authenticated)
    ├── Index.vue       # /admin (dashboard)
    └── users/          # /admin/users/*
        ├── Index.vue   # /admin/users
        ├── Show.vue    # /admin/users/:id
        └── SimpleTable.vue # /admin/users/table
```

### Route Naming Convention

Routes use prefixed names for clarity:

| Route | Name | Page |
|-------|------|------|
| `/` | `home` | `pages/Index.vue` |
| `/login` | `login` | `pages/Login.vue` |
| `/register` | `register` | `pages/Register.vue` |
| `/admin` | `admin.index` | `pages/admin/Index.vue` |
| `/admin/users` | `admin.users.index` | `pages/admin/users/Index.vue` |
| `/admin/users/:id` | `admin.users.show` | `pages/admin/users/Show.vue` |

Use `$route('admin.users.index')` in templates, `route('admin.users.index')` in scripts.

---

## Required Checks

Run these commands before committing changes:

### Quick Commands
```bash
npm run check        # Run ALL checks (PHP + JS)
npm run check:php    # Run PHP checks only (Pint, Larastan, Pest)
npm run check:js     # Run JS checks only (ESLint, Vitest, Build)
```

### Linting & Testing
```bash
npm run lint         # Check ESLint issues
npm run lint:fix     # Auto-fix ESLint issues
npm run test         # Run JS unit tests (Vitest)
```

### Individual Tools (if needed)
```bash
./vendor/bin/pint            # PHP code style
./vendor/bin/phpstan analyse # Static analysis
./vendor/bin/pest            # PHP tests (or: composer test)
npm run test                 # JS tests (Vitest)
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
| JS/Vue     | Vitest       | Tests required for new features |
| TypeScript | tsc          | Must compile without errors     |

---

All contributions must follow this document, the referenced guides, all PRDs, and the README. Non-compliant contributions will be rejected.
