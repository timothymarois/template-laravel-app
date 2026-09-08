# Codemap — template-laravel-app

> Inventory only; conventions live in [`AGENTS.md`](../AGENTS.md). Counts are artifacts, not lines —
> when one stops matching the tree, that section is stale.

Laravel 13 (PHP 8.4) + Vue 3.5 + Inertia + Tailwind 4 + shadcn-vue + Vite 7 + TypeScript (strict).

**Key integrations:** Laravel Reverb + Echo (WebSockets), Horizon (Redis queues), Sentry (errors), spatie/laravel-health + Discord notifications, Cashier (billing), Socialite (OAuth), Sanctum (auth/tokens), flysystem S3, archtechx/enums, Spatie Sitemap, TipTap (editor), Unovis (charts), Maska (masks), vue-sonner (toasts), vue-draggable-plus, lucide + tabler icons, Ziggy (routes).

## Models (app/Models/ — 1)

- **User** — HasApiTokens, Notifiable. Fields: name, email, password, timezone, is_active, `role` (UserRole cast), last_seen_at/ip/user_agent. `isOnline()`.

## Enums (app/Enums/ — 1)

- **UserRole** — `canManageAllUsers()`, `canImpersonate()`.

## Services (app/Services/ — 2)

- **Models/ModelService** — abstract base; **Models/UserService** — user CRUD.

## Controllers (app/Http/Controllers/ — 9)

- **PageController** — public home; **ReleaseController** — deployed version as JSON; **Auth/SessionController** (login/logout), **Auth/RegisterController**, **Auth/PasswordResetController** (forgot/reset, non-enumerating).
- **Admin/DashboardController**, **Admin/UserController** (CRUD + `simpleTable`, `prepareIndexFilters`), **Admin/ComponentController** (showcase).
- Base **Controller**.

## HTTP Concerns (app/Http/Concerns/ — 1)

- **InertiaDataTableOptions** — data-table state (search, filters, pagination, sorting, session persistence).

## Form Requests (app/Http/Requests/ — 6)

- Auth: `LoginRequest`, `RegisterRequest`, `ForgotPasswordRequest`, `ResetPasswordRequest`; User: `StoreUserRequest`, `UpdateUserRequest`.

## Middleware (app/Http/Middleware/ — 5)

- **HandleInertiaRequests** (shares an allow-listed `user` + `flash.{status,error}`), **TrackLastSeen**, **EnsureUserIsActive**, **EnsureUserIsAdmin** (aliased `admin`, gates the whole admin prefix), **SecurityHeaders**.

## Policies (app/Policies/ — 1)

- **UserPolicy** — viewAny/view/create/update/delete against `UserRole::canManageAllUsers()`; delete also refuses self-deletion.

## Health (app/Health/ — 5)

- **Checks/ReverbCheck**; **DiscordHealthChannel**, **DiscordWebhook**; **Listeners/NotifyOnHealthRecovery**, **Listeners/NotifyOnMaintenanceMode**.

## Providers (app/Providers/ — 2)

- **AppServiceProvider** (password rules, rate limiters, health checks), **HorizonServiceProvider**.

## Commands (app/Console/Commands/ — 3)

- **StartFresh** (`start:fresh`), **GenerateSitemap** (`sitemap:generate`), **EnsureStorage** (`app:ensure-storage`).

## Support (app/Support/ — 2)

- **Caster** (filter type casting), **PhoneNumber** (formatting/validation).

## Routes (routes/ — 4)

`web.php`, `components.php`, `api.php`, `channels.php`.

- **Public:** `GET /` (home), `GET /up` (container gate), `GET /health` (spatie health JSON), `GET /release` (deployed version).
- **Guest:** `GET /register|/login`, `POST /auth/register|/auth/login`; password reset — `GET /forgot-password` (`password.request`), `POST /auth/forgot-password` (`password.email`), `GET /reset-password/{token}` (`password.reset`), `POST /auth/reset-password` (`password.store`). The three unauthenticated POSTs share one `throttle:auth` bucket (5/min/IP).
- **Auth (`auth:sanctum`):** `POST /logout`; admin group (additionally `admin` middleware) — `GET /admin/` (dashboard), `/admin/users/*` (resource + `users/table`, `users/filters`), `/admin/components/*` (showcase).
- **API:** `GET /user` (auth:sanctum, throttle:api). **Broadcast:** `App.Models.User.{id}`.

## Pages (resources/js/pages/ — 59 .vue)

- **Public:** `Index.vue`, `Login.vue`, `Register.vue`, `ForgotPassword.vue`, `ResetPassword.vue`. **Errors:** `errors/{404,500,503}.vue`.
- **Admin:** `admin/Index.vue`, `admin/users/{Index,Show}.vue`.
- **Component showcase** (`admin/components/`): `forms/` (Input, InputMasks, Textarea, Select, Checkbox, Combobox, Switch, Slider, Fields, Editor, Upload, PinInput, input/Tags, calendar/{DateInput,DateRangeInput}), `actions/` (Button, Command, Dialog, Menu, Sheet), `display/` (Alert, Card, Badge, Avatar, Tooltip, Popover, Loading, Tabs, Accordion, Toast, Carousel, Resizable, CodeBlock, ViewToggle), `data/` (Table, Actions, Pagination), `charts/` (Bar, Line, Area, Pie).

## UI Components (resources/js/components/ui/ — 49)

Data: accordion, alert, avatar, badge, card, carousel, chart, code-block, data-table, pagination, progress, skeleton, table, tooltip, view-toggle | Forms: checkbox, combobox, date-picker, input (NumberInput, MaskInput), label, pin-input, radio-group, range-calendar, select, select-popover, slider, switch, tags-input, textarea | Overlays: alert-dialog, dialog, popover, sheet, dropdown-menu, context-menu | Nav: tabs, sidebar | Layout: collapsible, resizable, scroll-frame, separator, command | Editor: editor (TipTap) | Upload: upload | Feedback: sonner, spinner, form-errors | Actions: button | Other: calendar.

## App Components (resources/js/components/app/ — 15 .vue, by subfolder)

- **layout/** — AppLayout, AppShell, AppTopbar.
- **modals/** — DeleteUserModal, EditUserModal.
- **navigation/** — ModeToggle, ProfileMenu, Sidebar, Topbar.
- **page/** — Content, Footer, Header, PageSidebar, SideContent, SideNav.

## Site Components (resources/js/components/site/ — 1)

- **layout/SiteLayout** — public layout with SEO meta.

## Composables (resources/js/composables/)

- **ui/**: useModal, useModelValue, useScroll, useSelectableOptions. **inertia/**: useDataTableOptions.
- **useFormSubmit**; **useEcho.js**: getEcho, useChannel, usePrivateChannel, usePresenceChannel, useListen.

## Utils (resources/js/utils/)

- **format/** — formatBytes, formatCurrency, formatDate, formatDatetime, formatDateValue, formatNumber, formatPercentage, formatSlug, formatUSPhoneNumber, formatValidURL, formatYmdDate, parseUtcDate.
- **validate/** — isEmpty, isNumeric, isValidEmail, isValidURL. **file/** — getFileExtension, normalizeFiles, validateFileSize, validateFileType.
- **array/** getRandomItem | **browser/** isClient | **vue/** expandTransition, hasSlotContent, inertia/isPageActive | **math/** roundTo | **cn.ts**.

## Frontend Entry Points & Plugins (resources/js/ — 6)

- `app.js`, `bootstrap.js`, `setup.js`, `ssr.js`, `ziggy.js`, `env.d.ts`.
- **plugins/inertia/** — `ziggy.js` (Ziggy route helper wiring).

## Migrations (database/migrations/)

- Top-level (4): users (+ `password_reset_tokens`, `sessions`), cache, jobs, personal_access_tokens.

## Release scripts (scripts/ — 4)

Stack-neutral bash + `python3`, no dependencies. `production` is the deployed branch; `main` stays at
version `0.0.0`. Identity and target come from `template-manifest.json` -> `deploy`. Procedure:
[`guides/releasing.md`](./guides/releasing.md).

| Script | What it does |
|--------|--------------|
| prepare-production-release | Bumps both manifests on a `release/vX.Y.Z` branch cut from `origin/main` |
| production-release-version | Reads the stored version, refusing `0.0.0` or manifests that disagree |
| publish-production-release | Verifies the deploy is live via `/release`, `/up`, `/health`, then tags and publishes |
| assert-neutral-main-version | Guards the invariant that `main` never carries a release version |

## Config (config/ — 21)

Notable: `release.php` (reads `version` from `composer.json`, served by `/release`), `health.php`, `horizon.php`, `reverb.php`, `broadcasting.php`, `sentry.php`, `solo.php`.

## Testing

- **Backend (Pest — 18 test files: Feature 10, Unit 8):** Feature — AgentInstructions, AuthenticationFlow, EnsureStorage, EnsureUserIsActive, Example, GoogleAnalytics, PasswordReset, ReleaseVersion, TrackLastSeen, UserController. Unit — Example, PhoneNumber, Services/UserService, Enums/UserRole, Health/{DiscordHealthChannel, NotifyOnHealthRecovery, NotifyOnMaintenanceMode, ReverbCheck}.
- **Frontend (Vitest + Vue Test Utils, happy-dom — resources/js/tests/, 41 test files):** UI component helpers, composables, and utils.

## Code Quality

- **PHP:** Pint (PSR-12), Larastan (level 5). **JS:** ESLint (strict TS + Vue 3), Stylelint.
- **Master:** `pnpm check` (php + js + release + deploy + build). There is no automated documentation check — `docs/` correctness is a review concern.
- Rate limits (`AppServiceProvider`): `api`, `auth` (per-IP), `uploads` — see `AppServiceProvider` for the current per-window values.

## Documentation

- `docs/` — the documentation home. `BRIEF.md` (what & why), `CODEMAP.md` (this file), `concepts/` (how a subsystem works and how it fails), `guides/` (one task each). Index: [`docs/README.md`](./README.md).
- `AGENTS.md` — the rules, including the worked code galleries. Not documentation; law.
- `.template/` _(hidden)_ — template maintenance only, deletable by a fork: `CHANGELOG.md`, `CHANGELOG-LEGACY.md`, `README.md`, `ADOPT.md`, `migrations/`.
