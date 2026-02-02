# AGENTS

This document defines the standards and contribution rules for all Agents working on the application.

---

## Purpose

The application is a unified system composed of **Laravel**, **Vue 3 + Inertia**, and **TailwindCSS with shadcn-vue**.

**Agents must follow these rules:**

* Documentation lives in `/docs/` and is built with VitePress.
* Review related documentation in `/docs/` before making changes.
* Architecture and guidelines are defined in the docs—follow them.
* If clarity is missing, request clarification before committing code.

---

## Tech Stack

### Backend

* Laravel 12+ (PHP 8.3+)
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

### Component Reuse Priority (IMPORTANT)

**Before writing ANY new component, follow this checklist:**

1. **Search existing components first** - Check if a component already exists in `components/ui/`, `components/app/`, or `components/site/`
2. **Check shadcn-vue** - If not found, check https://www.shadcn-vue.com/docs/components
3. **Install from shadcn** - If available: `pnpm dlx shadcn-vue@latest add <component>`
4. **Create new only as last resort** - Only create custom components when nothing suitable exists

**Reuse over recreation** - Always prefer composing existing components over creating new ones. If an existing component is close but not quite right, consider extending or wrapping it rather than building from scratch.

### Component Examples & Showcase (REQUIRED READING)

**Before implementing any UI component, you MUST review the component showcase examples.**

The application includes a comprehensive component showcase at `resources/js/pages/admin/components/` that demonstrates:
- Correct usage patterns for all UI components
- Prop combinations and variants (sizes, states, styles)
- Invalid/error states and disabled states
- Form integration patterns
- Real-world use cases and edge cases

**How to use the showcase:**

1. **Find the relevant showcase page** - Examples are organized by category:
   - `forms/` - Input, Select, Checkbox, Switch, TagsInput, etc.
   - `actions/` - Button, Dialog, Sheet, Menu, Command
   - `display/` - Card, Badge, Alert, Avatar, Tabs, Toast
   - `data/` - Table, Pagination, Actions
   - `charts/` - Area, Bar, Line, Pie charts

2. **Study the examples before implementing** - Each showcase demonstrates:
   - Basic usage with different states (empty, filled, disabled)
   - Variants and sizes
   - Error/invalid states for form validation
   - Feature combinations (clearable, searchable, multiple, etc.)

3. **Copy patterns from examples** - The showcase code shows exactly how to:
   - Structure component props
   - Handle v-model bindings
   - Apply consistent styling (grid layouts, labels, spacing)
   - Implement common patterns (form fields with labels, disabled states with opacity)

**Example pattern from showcase:**
```vue
<div class="grid grid-cols-4 gap-4">
    <div>
        <div class="text-xs text-muted-foreground mb-1">Empty</div>
        <Input v-model="empty" placeholder="Enter value..." fluid />
    </div>
    <div>
        <div class="text-xs text-muted-foreground mb-1">With Value</div>
        <Input v-model="filled" placeholder="Enter value..." fluid />
    </div>
    <div>
        <div class="text-xs text-muted-foreground mb-1">Invalid</div>
        <Input v-model="invalid" placeholder="Required..." invalid fluid />
    </div>
    <div class="opacity-50">
        <div class="text-xs text-muted-foreground mb-1">Disabled</div>
        <Input v-model="disabled" placeholder="Disabled" disabled fluid />
    </div>
</div>
```

**This ensures consistency across the application and prevents reinventing patterns that already exist.**

> **Note:** The component showcase pages at `/admin/components/` are reference examples for development. They demonstrate correct usage patterns and are safe to delete once you understand the patterns, or keep them as internal documentation.

### Layer Overview

| Layer     | Location           | Purpose                             | Rules                                            |
|-----------|--------------------|-------------------------------------|--------------------------------------------------|
| **ui/**   | `components/ui/`   | Base components (shadcn + enhanced) | [README](resources/js/components/ui/README.md)   |
| **app/**  | `components/app/`  | Application components              | [README](resources/js/components/app/README.md)  |
| **site/** | `components/site/` | Website components                  | [README](resources/js/components/site/README.md) |

### ui/ - Base Components (Reusable Only)

All base-level UI components live here. This is the single source of truth for all UI primitives.

**Rules:**
- **Isolated & Reusable** - No application-specific logic, no business rules
- **Single Purpose** - Each component does one thing well
- **Stateless** - No Inertia, routes, auth, or external dependencies
- **Props-driven** - All behavior controlled via props and events
- Contains ALL base-level components (shadcn + custom)
- shadcn primitives use `*Base` suffix (ButtonBase, CardBase)
- Enhanced versions wrap `*Base` components with added features (loading states, icons, etc.)

**What belongs in ui/:**
- Buttons, inputs, cards, modals, tables, forms
- Generic data display components
- Layout primitives (grids, containers)

**What does NOT belong in ui/:**
- Components that fetch data
- Components with hardcoded routes or API calls
- Components tied to specific features (use `app/` or `site/` instead)

### app/ - Application Components (Feature-Specific)

Components for the main application (authenticated dashboard, management interfaces). These contain **application-specific logic** that doesn't belong in reusable UI components.

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
- May contain business logic specific to app features
- Compose from `ui/` components - don't recreate primitives

**What belongs in app/:**
- User management modals (EditUserModal, DeleteUserModal)
- Navigation components that use auth state
- Feature-specific forms and wizards
- Dashboard widgets with data fetching

### site/ - Website Components (Feature-Specific)

Components for the public website (marketing pages, landing pages, unauthenticated flows). These contain **site-specific logic** separate from the authenticated app.

```
site/
├── layout/         # SiteLayout
└── index.ts
```

**Rules:**
- May use Inertia and routes
- Public-facing, no authentication required
- For marketing and public website pages
- Compose from `ui/` components - don't recreate primitives

**What belongs in site/:**
- Marketing page sections
- Public forms (contact, newsletter)
- Landing page components

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
Need a component?
│
├─ 1. SEARCH FIRST: Does it already exist?
│     └─ Check ui/, app/, site/ directories
│     └─ If found → USE IT (don't recreate)
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

| Route          | Name                | Layout     |
|----------------|---------------------|------------|
| `/`            | `home`              | SiteLayout |
| `/login`       | `login`             | SiteLayout |
| `/register`    | `register`          | SiteLayout |
| `/admin`       | `admin.index`       | AppLayout  |
| `/admin/users` | `admin.users.index` | AppLayout  |

Use `$route('admin.users.index')` in templates, `route('admin.users.index')` in scripts.

---

## Required Checks

Run these commands before committing changes:

### Quick Commands
```bash
pnpm check        # Run ALL checks (PHP + JS + SSR build) - use before committing
pnpm check:php    # Run PHP checks only (Pint, Larastan, Pest)
pnpm check:js     # Run JS checks only (ESLint, Stylelint, Vitest, client build)
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
