# AGENTS

This document defines the standards and contribution rules for all Agents working on the application.

---

## Purpose

The application is a unified system composed of **Laravel**, **Vue 3 + Inertia**, and **TailwindCSS with shadcn-vue**.

**Agents must follow these rules:**

* PRDs override all assumptions. All PRDs live in `/docs/PRD/`.
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

## Documentation

Before contributing, review the relevant guides:

* [Laravel Guide](./docs/laravel-standards/docs/laravel-guide.md) – PHP and Laravel conventions
* [Vue Guide](./docs/laravel-standards/docs/vue-guide.md) – Vue components and frontend standards
* [Agents Guide](./docs/laravel-standards/docs/agents-guide.md) – Additional agent-specific details

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

* All buttons, links, and interactive elements must use `cursor-pointer`.
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
├── Enums/              # PHP backed-Enums
├── Http/
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
├── components/         # Reusable components
│   └── ui/             # shadcn-vue components
├── pages/              # Inertia pages
├── composables/        # Vue composables
└── utils/              # Utilities
```

---

## Required Checks

Run these commands before committing changes:

### Frontend
```bash
npm run eslint
```

### Backend (requires `composer install` first)
```bash
./vendor/bin/pint
./vendor/bin/phpstan analyse
php artisan test
```

### Full Check (if available)
```bash
npm run check
```

All checks must pass before committing.

---

## Code Quality Standards

| Layer | Tool | Requirement |
|-------|------|-------------|
| PHP | Laravel Pint | Must pass |
| PHP | Larastan | Level 5 minimum |
| PHP | PHPUnit | Tests required for new features |
| JS/Vue | ESLint | Must pass |
| TypeScript | tsc | Must compile without errors |

---

All contributions must follow this document, the referenced guides, all PRDs, and the README. Non-compliant contributions will be rejected.
