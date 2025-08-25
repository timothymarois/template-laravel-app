# Laravel Guide

This guide outlines architecture and coding conventions for Laravel projects. Every contributor must follow these practices.

### Table of Contents

- [Architecture](#architecture)
- [Philosophy](#philosophy)
- [Structure](#structure)
- [Conventions](#conventions)
  - [Class Naming](#class-naming)
  - [File & Folder Structure](#file--folder-structure)
  - [Variable Naming](#variable-naming)
  - [Database Naming](#database-naming)
  - [Method Naming](#method-naming)
  - [Configuration & Environment](#configuration--environment)
  - [Exception & Error Handling](#exception--error-handling)
  - [Formatting](#formatting)
  - [Language & Terminology](#language--terminology)

## Architecture

Defines the structure and design rules that keep the codebase modular, scalable, testable, and maintainable. Every developer is expected to understand and follow this architecture.

## Philosophy

The application uses a layered, service-oriented approach. Business logic lives in service classes. Controllers coordinate requests, models map data, and jobs handle queued tasks. Each layer must stay within its responsibility.

## Structure

The application is organized into the following directories:

```
App/
├── Console/
│   └── Commands/
├── Contracts/
├── Enums/
├── Exceptions/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Jobs/
├── Models/
├── Notifications/
├── Events/
├── Listeners/
├── Providers/
└── Services/
```

### `App/Console`

Contains the console kernel and scheduled task configuration. No additional logic.

### `App/Console/Commands`

CLI commands handle input and output, trigger jobs, or call services. They must not contain business logic or data access.

### `App/Contracts`

Interfaces for services, repositories, or API clients. They enable dependency inversion and hold no implementation.

### `App/Enums`

PHP enums for fixed domain values such as statuses, types, or roles. They should not encapsulate workflow logic.

### `App/Exceptions`

Custom exception classes representing meaningful, application-specific failures. They contain no business logic or side effects.

### `App/Events`

Data objects signaling that something happened, such as `UserRegistered` or `InvoicePaid`. They carry no logic, model interaction, or side effects and are dispatched by services.

### `App/Http/Controllers`

Entry point for web and API requests. Controllers delegate to services, form requests, or jobs and must not contain business rules, database access, or branching beyond routing.

### `App/Http/Middleware`

Request-level filters for headers, authentication, throttling, and similar concerns. Middleware must not perform domain actions, business logic, or data access.

### `App/Http/Requests`

Centralize validation and authorization. All validation rules, custom rule objects, and authorization checks live here, not in controllers or services.

### `App/Http/Resources`

Transform data for frontend or API consumers. Resources cannot access the database, mutate state, or trigger behavior.

### `App/Jobs`

Small, focused tasks executed asynchronously. Jobs may invoke services but must not contain business rules.

### `App/Listeners`

Handle side effects in response to events, such as sending notifications or writing logs. They must not mutate core state, make business decisions, or dispatch additional events. Offload complex work to jobs or services.

### `App/Models`

Eloquent models define schema, relationships, accessors, mutators, and scopes. They represent data only and must not send notifications, invoke services, or span domains.

### `App/Notifications`

Outbound communication classes, including Laravel Notifications and Mailables. They format and deliver messages and must be triggered by services or listeners without side effects or business rules.

### `App/Providers`

Service providers bootstrap the application by binding interfaces, loading routes, or registering observers. They must not contain domain logic.

### `App/Services`

All business logic resides in service classes. Services coordinate models, jobs, APIs, notifications, and decisions. They should be small, composable, and testable, each representing a clear use case such as registering a user or processing a webhook. Services must not access HTTP request data or render responses.

### `App/Support`

Stateless helpers such as formatters or string utilities. They may use static methods or be injected but cannot hold application state, call services, or contain domain-specific behavior. Their sole purpose is to assist other layers with general-purpose logic.

## Conventions

These naming and structural conventions keep the codebase readable and predictable. They are enforced during code review.

### Class Naming

- **Models**: singular, PascalCase (e.g. `User`, `InvoiceItem`).
- **Controllers**: PascalCase with `Controller` suffix (e.g. `UserController`).
- **Services**: PascalCase with `Service` suffix (e.g. `SubscriptionService`).
- **Jobs**: PascalCase with verb/action + `Job` (e.g. `SendEmailJob`).
- **Form Requests**: PascalCase with `Request` suffix (e.g. `StoreUserRequest`).
- **Resources**: PascalCase with `Resource` suffix (e.g. `UserResource`).
- **Enums**: PascalCase and clearly scoped (e.g. `UserStatus`).
- **Events**: PascalCase, past tense, describes something that happened (e.g. `UserRegistered`).
- **Listeners**: PascalCase, imperative, describes action (e.g. `SendWelcomeEmail`).
- **Notifications & Mailables**: PascalCase, descriptive; use `Notification` or `Mail` suffix only when necessary (e.g. `PasswordResetNotification`).

### File & Folder Structure

- Place each file in its proper folder as defined in the Architecture section.
- Group services by domain when needed (e.g. `Services/Users/UserService.php`).
- Avoid flat "god folders" such as `Services/` or `Jobs/` when domain contexts exist.

#### Events & Listeners

- Events live in `App/Events/{Domain}/`.
- Listeners live in `App/Listeners/{Domain}/`.
- Group both by domain or feature (e.g. `User/`, `Billing/`, `Auth/`).
- Events contain only data.
- Listeners handle side effects like emails, logs, or notifications and must not trigger workflows or mutate domain state directly.

### Variable Naming

- Use `camelCase` for variables, parameters, and methods.
- Names must be descriptive; avoid abbreviations unless standard.

#### Booleans

- Prefix with `is`, `has`, `can`, `should`, or `needs` (e.g. `isAdmin`, `hasAccess`).

#### Collections

- Use plural nouns (e.g. `$users`, `$invoices`).

#### Constants

- `UPPER_SNAKE_CASE` (e.g. `MAX_RETRIES`).

### Database Naming

- **Tables**: `snake_case`, plural (e.g. `users`, `invoice_items`).
- **Columns**: `snake_case`, singular; use `*_id` for foreign keys and `created_at`, `updated_at` for timestamps.
- **Enum values**: `snake_case` strings or native enums.
- **Boolean columns**: prefix with `is_`, `has_`, or `can_` (e.g. `is_active`).

### Method Naming

- Methods must be action-oriented and intention-revealing (e.g. `sendInvoice()`, `syncContacts()`).
- CRUD services use `create`, `update`, `delete`, `restore`, `list`, `find`, `sync`, `validate`, `resolve`, `dispatch`.
- Event/state/status methods use verbs like `markAsPaid()`, `setActive()`, `deactivate()`, `archive()`.

### Configuration & Environment

- Store API keys and tokens in `.env` and access them via `config/services.php`.
- Do not call `env()` outside `config/` files.
- Namespace third-party service credentials under their own config key.

### Exception & Error Handling

- Throw custom exceptions for domain errors.
- Do not use `abort()` in services.
- Ensure exceptions are type-safe and traceable.

### Formatting

- Follow PSR-12 formatting via Laravel Pint.
- Order imports: classes, then functions, then constants.
- No trailing commas on single-line arrays or function calls.
- Use short array syntax `[]`.

### Language & Terminology

- Use consistent domain language.
- Prefer real-world nouns and verbs over technical names (e.g. `lead`, `contact`, `campaign` instead of `userTag`, `dataBlob`).

<!-- end of Laravel Guide -->
