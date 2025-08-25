# Backend Guide

This guide combines the architecture and coding conventions for the Laravel backend. All contributors must follow these practices.

## Architecture


This document defines the structure, boundaries, and design rules for this application. It exists to ensure the codebase remains modular, scalable, testable, and maintainable as it evolves. All developers are expected to understand and follow this architecture without exception.

## Philosophy

The application follows a layered service-oriented architecture. Business logic is isolated in service classes. Controllers act only as request coordinators. Models are used for data mapping and persistence, not behavior. Jobs are queued tasks with minimal logic. Every part of the application must respect its own responsibility and never reach across layers inappropriately.

## Structure

### Directories

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

### `App/Console`

This contains the console kernel and configuration for scheduled tasks. It does not contain any logic beyond what Laravel requires to register and schedule commands.

### `App/Console/Commands`

CLI command classes live here. They are responsible for input/output, triggering jobs, or delegating to service classes. No business logic or data access should be placed directly in these commands.

### `App/Contracts`

All interfaces used for binding services, repositories, or third-party API clients are placed here. These interfaces enable dependency inversion. No logic or implementation lives in this directory.

### `App/Enums`

This directory holds PHP enums that represent fixed domain values such as statuses, types, or roles. Enums must only be used for clearly bounded values and should not encapsulate workflow logic.

### `App/Exceptions`

Custom exception classes live here. These are used to represent meaningful, application-specific failure cases. They must not contain business logic or side effects.

### `App/Events`

Events represent that something has already happened in the application. They are strictly data objects and must not contain any logic, model interaction, or side effects. Events are dispatched by services to signal state transitions, such as `UserRegistered` or `InvoicePaid`.

### `App/Http/Controllers`

Controllers are the entry point for web and API requests. Their only job is to delegate work to service classes, form requests, or jobs. Controllers must never contain any business rules, database access, or conditional behavior beyond routing.

### `App/Http/Middleware`

Middleware operates at the request level to filter, modify, or authorize requests. It must not perform domain actions, business logic, or data access. Middleware is meant for request concerns like headers, auth, throttling, and content-type handling.

### `App/Http/Requests`

Form request classes centralize validation and authorization. All validation must be placed here — not in controllers or services. These classes are also the only place where validation rules, custom rule objects, and authorization checks should live.

### `App/Http/Resources`

This layer is responsible for transforming data for frontend or API consumers. Resources must not access the database or contain side effects. They are serialization-only and should never mutate state or trigger behavior.

### `App/Jobs`

Jobs encapsulate deferred tasks to be executed asynchronously. Jobs are expected to be small and focused. They are allowed to invoke service classes but must not contain business rules themselves.

### `App/Listeners`

Listeners handle passive side effects in response to events. They may send notifications, write to logs, or call external systems — but they must not mutate core application state, perform business decisions, or dispatch additional events. Complex side effects should be offloaded to jobs or services.

### `App/Models`

Eloquent models define the data schema, relationships, accessors, mutators, and scopes. They represent data structures only. Models must never contain behavior that spans domains, perform actions like sending notifications, or invoke services.

### `App/Notifications`

This directory contains all outbound communication classes, including Laravel Notifications and custom Mailables. These classes are passive — they format and deliver messages via email, Slack, or other channels. No side effects or business rules are allowed. They must be triggered by services or listeners.

### `App/Providers`

Service providers handle application bootstrapping, such as binding interfaces, loading routes, or registering observers. Providers must not contain domain logic or workflow behavior.

### `App/Services`

All business logic in the application must be placed in service classes. Services coordinate models, jobs, APIs, notifications, and business decisions. No other layer is allowed to contain this logic. Services must be small, composable, and testable. A service should represent a clear business use case or workflow, such as "register user", "send invoice", or "process webhook". Services may depend on other services, interfaces, or helpers, but must not access HTTP request data or render responses.

### `App/Support`

Support classes contain reusable, stateless utility logic such as formatters, string manipulation, or time calculations. Support classes may use static methods or act as injected helpers. They must not contain application state, service calls, or domain-specific behavior. Their only purpose is to assist other layers with general-purpose logic.

## Boundaries and Discipline

The success of this architecture depends on discipline. Controllers must not contain logic. Services must not contain view code. Models must not trigger workflows. Utilities must not evolve into service containers. The boundaries between layers must be clear and strictly enforced.

Every developer on the team is responsible for keeping these boundaries intact. This is not a guideline — it is the architecture.

## Conventions


This document defines the naming, structure, and formatting conventions that all developers must follow. These standards exist to ensure the codebase is readable, predictable, and easy to navigate across the entire team.

Conventions are not suggestions. They are enforced during code review and must be followed in all feature and bugfix work.

---

## Class Naming

### Models
- Singular, PascalCase.
- Must match the corresponding table name convention (snake_case, plural).
- Example: `User`, `InvoiceItem`, `ContactLead`.

### Controllers
- PascalCase with suffix `Controller`.
- Example: `UserController`, `AuthController`.

### Services
- PascalCase with suffix `Service`.
- Example: `SubscriptionService`, `ImportService`.

### Jobs
- PascalCase with verb/action suffix like `Job`.
- Example: `SendEmailJob`, `ImportCsvJob`.

### Form Requests
- PascalCase with `Request` suffix.
- Example: `StoreUserRequest`, `UpdateLeadRequest`.

### Resources
- PascalCase with `Resource` suffix.
- Example: `UserResource`, `ThreadMessageResource`.

### Enums
- PascalCase and clearly scoped.
- Example: `UserStatus`, `PaymentMethod`.

### Events
- PascalCase, past-tense, describes something that **has happened**.
- Example: `UserRegistered`, `InvoicePaid`.

### Listeners
- PascalCase, imperative, describes what the listener **does**.
- Example: `SendWelcomeEmail`, `SyncContactToCrm`.

### Notifications & Mailables
- PascalCase, descriptive. Suffixes like `Notification`, `Mail` only when necessary.
- Example: `PasswordResetNotification`, `WelcomeMail`.

---

## File & Folder Structure

- Every file must live in the correct folder as defined in the Architecture section above.
- Services must be grouped by domain if needed (e.g. `Services/Users/UserService.php`).
- No flat "god folders" (e.g., `Services/`, `Jobs/`) if domain contexts exist.

### Events & Listeners

- Events must live in `App/Events/{Domain}/`.
- Listeners must live in `App/Listeners/{Domain}/`.
- Both must be grouped by domain or feature (e.g., `User/`, `Billing/`, `Auth/`).
- Events must contain no logic — only data.
- Listeners must only handle **side effects** (emails, logs, notifications). They must not trigger other workflows or mutate domain state directly.

---

## Variable Naming

### General Rules
- Use `camelCase` for all variables, parameters, and method names.
- Use descriptive, intention-revealing names — no abbreviations unless industry-standard.

### Booleans
- Prefix with `is`, `has`, `can`, `should`, or `needs`.
- Example: `isAdmin`, `hasAccess`, `canEdit`, `shouldQueue`.

### Collections
- Use plural nouns for arrays and collections.
- Example: `$users`, `$invoices`, `$notifications`.

### Constants
- UPPER_SNAKE_CASE.
- Example: `MAX_RETRIES`, `DEFAULT_TIMEOUT`.

---

## Database Naming

- Tables: `snake_case`, plural.
    - Example: `users`, `invoice_items`, `password_resets`.
- Columns: `snake_case`, singular.
    - Use `*_id` for foreign keys (no constraints unless explicitly defined).
    - Use `created_at`, `updated_at` for timestamps.
- Enum values: `snake_case` strings or native enums if supported.
- Boolean columns: prefixed with `is_`, `has_`, `can_`.
    - Example: `is_active`, `has_error`, `can_login`.

---

## Method Naming

- Method names must be action-oriented and intention-revealing.
- Example: `sendInvoice()`, `updateSubscription()`, `syncContacts()`.

### CRUD naming for services:
- `create`, `update`, `delete`, `restore`, `list`, `find`, `sync`, `validate`, `resolve`, `dispatch`.

### Events/State/Status methods:
- `markAsPaid()`, `setActive()`, `deactivate()`, `archive()`.

---

## Configuration & Environment

- All API keys and tokens must be stored in `.env` and accessed via `config/services.php`.
- Never use `env()` outside of `config/` files.
- All third-party service credentials should be namespaced under their own config key.

---

## Exception & Error Handling

- Always throw custom exceptions when representing a domain error.
- Never use `abort()` in services.
- All exceptions must be type-safe and traceable through the stack.

---

## Formatting

- PSR-12 formatting is enforced via Laravel Pint.
- Imports must be ordered: classes, then functions, then constants.
- No trailing commas on single-line arrays or function calls.
- Always use short array syntax: `[]`, not `array()`.

---

## Language & Terminology

- Use consistent domain language throughout the app.
- Prefer real-world nouns/verbs over technical or implementation-based names.
- Example: use `lead`, `contact`, `campaign` — not `userTag`, `dataBlob`, `genericObject`.
