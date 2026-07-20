# Codemap — template-laravel-app

> Last updated: 2026-07-20 — inventory only; conventions live in the repo's AGENTS.md.

Laravel 13 (PHP 8.4) + Vue 3.5 + Inertia + Tailwind 4 + shadcn-vue + Vite 7 + TypeScript (strict).

**Key integrations:** Atlas (`atlas-php/atlas` — unified AI SDK, auto-discovered), stancl/tenancy (multi-tenancy, shipped **inert** — `TENANCY_ENABLED=false`), Laravel Reverb + Echo (WebSockets), Horizon (Redis queues), Sentry (errors), spatie/laravel-health + Discord notifications, Cashier (billing), Socialite (OAuth), Sanctum (auth/tokens), flysystem S3, archtechx/enums, Spatie Sitemap, TipTap (editor), Unovis (charts), Maska (masks), vue-sonner (toasts), vue-draggable-plus, lucide + tabler icons, Ziggy (routes).

> **Tenancy is inert by default.** Everything tagged _(tenancy)_ below — `app/Tenancy/`, the `Tenant`/`Domain`/`TenantInvite` models, tenancy services/controllers/middleware/commands, `routes/tenant.php`, the `central/`+`tenant/` migration dirs — ships but stays dormant until `TENANCY_ENABLED=true`. Treat as nonexistent while disabled (see repo AGENTS.md → Optional multi-tenancy).

## Models (app/Models/ — 5)

- **User** — CentralConnection, HasApiTokens, Notifiable. Fields: name, email, password, timezone, is_active, `role` (UserRole cast), last_seen_at/ip/user_agent. `tenants()` (BelongsToMany via `tenant_user`), `isOnline()`.
- **Concerns/CentralConnection** — trait pinning models to the central DB connection.
- _(tenancy)_ **Tenant** (extends stancl BaseTenant; HasDatabase, HasDomains, SoftDeletes; `users()`, `isReady()`, `markReady/Failed()`, `hasFailed()`), **Domain**, **TenantInvite** (`tenant()`, `invitedBy()`, `isExpired/Accepted/Pending()`).

## Enums (app/Enums/ — 2)

- **UserRole**, **TenantRole** _(tenancy)_

## Services (app/Services/ — 5)

- **Models/ModelService** — abstract base; **Models/UserService** — user CRUD.
- _(tenancy)_ **Tenancy/**: TenantInviteService, TenantMembershipService, TenantProvisioningService.

## Controllers (app/Http/Controllers/ — 8)

- **PageController** — public home; **Auth/SessionController** (login/logout), **Auth/RegisterController**.
- **Admin/DashboardController**, **Admin/UserController** (CRUD + `simpleTable`, `prepareIndexFilters`), **Admin/ComponentController** (showcase).
- Base **Controller**; _(tenancy)_ **Tenancy/InviteController** (show/accept/decline).

## HTTP Concerns (app/Http/Concerns/ — 1)

- **InertiaDataTableOptions** — data-table state (search, filters, pagination, sorting, session persistence).

## Form Requests (app/Http/Requests/ — 4)

- Auth: `LoginRequest`, `RegisterRequest`; User: `StoreUserRequest`, `UpdateUserRequest`.

## Middleware (app/Http/Middleware/ — 7)

- **HandleInertiaRequests** (shared data), **TrackLastSeen**, **EnsureUserIsActive**, **SecurityHeaders**.
- _(tenancy)_ **Tenancy/**: InitializeTenancyBySlug, EnsureTenantReady, EnsureUserBelongsToTenant.

## Health (app/Health/ — 5)

- **Checks/ReverbCheck**; **DiscordHealthChannel**, **DiscordWebhook**; **Listeners/NotifyOnHealthRecovery**, **Listeners/NotifyOnMaintenanceMode**.

## Providers (app/Providers/ — 3)

- **AppServiceProvider** (rate limiters), **HorizonServiceProvider**, **TenancyServiceProvider** (gates all tenancy on `tenancy.enabled`).

## Tenancy internals (app/Tenancy/ — 5) _(inert)_

- **Bootstrappers/SignedUrls**, **Contracts/ExistingDataMigrator**, **NullExistingDataMigrator**, **Listeners/ConditionalDeleteTenantDatabase**, **Listeners/MarkTenantReady**.

## Notifications (app/Notifications/ — 1)

- _(tenancy)_ **Tenancy/TenantInvitationNotification**.

## Commands (app/Console/Commands/ — 7)

- **StartFresh** (`start:fresh`), **GenerateSitemap** (`sitemap:generate`), **EnsureStorage** (`app:ensure-storage`).
- _(tenancy)_ **Tenancy/**: `tenancy:enable`, `tenancy:provision`, `tenancy:migrate-existing`, `tenancy:purge-deleted`.

## Support (app/Support/ — 2)

- **Caster** (filter type casting), **PhoneNumber** (formatting/validation).

## Helpers (app/helpers.php — 4)

- `tenant_user()`, `central_user()`, `current_actor()`, `tenant_url()`.

## Routes (routes/ — 5)

`web.php`, `components.php`, `api.php`, `channels.php`, `tenant.php`.

- **Public:** `GET /` (home), `GET /health` (spatie health JSON).
- **Guest:** `GET /register|/login`, `POST /auth/register|/auth/login`.
- **Auth (`auth:sanctum`):** `POST /logout`; admin group — `GET /admin/` (dashboard), `/admin/users/*` (resource + `users/table`, `users/filters`), `/admin/components/*` (showcase).
- **API:** `GET /user` (auth:sanctum, throttle:api). **Broadcast:** `App.Models.User.{id}`.
- _(tenancy)_ `web.php` invite endpoints and `tenant.php` context routes register only when enabled.

## Pages (resources/js/pages/ — 57 .vue)

- **Public:** `Index.vue`, `Login.vue`, `Register.vue`. **Errors:** `errors/{404,500,503}.vue`.
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

- Central top-level (4): users, cache, jobs, personal_access_tokens.
- _(tenancy)_ **central/** (4): tenants, domains, tenant_user, tenant_invites. **tenant/** — empty scaffold (`.gitkeep`).

## Config (config/ — 21)

Notable: `tenancy.php`, `health.php`, `horizon.php`, `reverb.php`, `broadcasting.php`, `sentry.php`, `solo.php`.

## Testing

- **Backend (Pest — tests/, 31 test files):** Feature — AuthenticationFlow, EnsureStorage, EnsureUserIsActive, TrackLastSeen, UserController, GoogleAnalytics, Tenancy/{DisabledState, EnableCommand, MigrateExistingCommand}. Unit — Services/UserService, Enums/{UserRole, TenantRole}, Health/{DiscordHealthChannel, NotifyOnHealthRecovery, NotifyOnMaintenanceMode, ReverbCheck}, Models/TenantInvite, Notifications/TenantInvitationNotification, Tenancy/NullExistingDataMigrator.
- **Central** _(tenancy enabled-state suite)_ — EnabledStateSmoke, InviteController, MultiTenantAccess, PathModeRouting, ProvisionCommand, SignedUrlsBootstrapper, TenantInviteService, TenantLifecycle, TenantMembershipService, TenantProvisioningService (bases: CentralBaseTestCase, TenantBaseTestCase).
- **Frontend (Vitest + Vue Test Utils, happy-dom — resources/js/tests/, 41 test files):** UI component helpers, composables, and utils.

## Code Quality

- **PHP:** Pint (PSR-12), Larastan (level 5). **JS:** ESLint (strict TS + Vue 3), Stylelint.
- **Master:** `pnpm check` (php + js + docs + build); `pnpm check:tenancy` / `pnpm check:all` add the enabled-state tenancy suite.
- Rate limits (`AppServiceProvider`): `api`, `auth` (per-IP), `uploads` — see `AppServiceProvider` for the current per-window values.

## Documentation

- `.knowledge/` — the agent knowledge system. Always-loaded orientation trio: `BRIEF.md` (what & why), `CODEMAP.md` (this file), `MEMORY.md` (current friction), plus `OVERVIEW.md`. On-demand homes: `prd/`, `prd-drafts/`, `research/` (+ `references/` visuals), `guides/` (writing standards `docs-*.md` + project how-tos). See `.knowledge/README.md` for the map; `.knowledge/tmp/` is git-ignored scratch.
- `.template/` _(hidden)_ — fork release artifacts: `CHANGELOG.md`, `CHANGELOG-LEGACY.md`, `README.md`, `migrations/`.

---
*Editing this file? Follow the standard first: [`guides/docs-codemap.md`](./guides/docs-codemap.md).*
