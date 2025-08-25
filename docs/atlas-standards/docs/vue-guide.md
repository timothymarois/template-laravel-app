# Vue Guide

This document defines the structure and rules for building Vue 3 components using Inertia.js and TailwindCSS. It mirrors the backend architecture and ensures clean separation of concerns, modular design, and long-term maintainability.

---

## Core Principles

- Pages only coordinate — no logic or bloat.
- All logic is extracted to composables or services.
- Components are atomic, reusable, and scoped.

---

## Structure Overview

### Directories

    resources/
    ├── assets/ 
    │   └── css/
    │   └── images/
    ├── js/
    │   └── pages/
    │   └── components/
    │   └── composables/
    │   └── services/
    │   └── enums/
    │   └── utils/
    ├── tests/
    │   └── components/
    │   └── composables/
    │   └── services/
    │   └── utils/
    └── views/

### Pages

- One file per route.
- Receives props from Inertia.
- Delegates everything to components, composables, or layouts.
- Never contains logic, mutation, or reusable markup.

### Components

Organized into subfolders:
- `Layouts/` – App and auth wrappers (`AppLayout.vue`).
- `{domain}/` – Reusable, grouped form blocks (`UserForm.vue`).
- Always use composition api `<script setup>`.

> Components are purely presentational. No logic or heavy state. When using state, try to keep local, but prefer composables.

### Composables

- Named like `useXxx.js` (e.g., `useModal`, `usePagination`).
- Extract reusable logic from components/pages.
- Must be stateless and scoped.

### Services

- Optional layer for wrapping Inertia visits, API helpers, or uploaders.
- Encapsulates external data interactions.

### Enums

- Provide a single source of truth for shared constants (e.g., statuses, roles) so the front-end matches Laravel's enums without magic values.
- Auto-generated from the backend using `php artisan atlas:export-enums`. See the [Enum Exporter docs](https://github.com/tmarois/atlas-laravel/blob/main/docs/features/enum-exporter.md).

### Utils

- Stateless helpers (`formatDate`, `slugify`).
- No app state, DOM access, or side effects.

---

## Naming Conventions

| Layer       | Format     | Example             |
|-------------|------------|---------------------|
| Pages       | PascalCase | `Users/Index.vue`   |
| Components  | PascalCase | `UserForm.vue`      |
| Composables | camelCase  | `useModal.js`       |
| Services    | camelCase  | `api.js`            |
| Utils       | camelCase  | `formatDate.js`     |

---

## Best Practices

- Keep components under 300 lines when possible.
- All state-related logic must go into composables.
- Avoid deeply nested components — flatten where possible.
- Use Tailwind for styling; never write custom CSS unless scoped.
- Register layouts only at the page level.
- Forms should always try use `useForm` when possible.
