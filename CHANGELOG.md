# Changelog

Any project using this template should follow the changelog here if its relevant to make the needed updates to be compatible. 

Note: once you update a project on that uses this template, be sure to copy this changelog so that project can also understand which version its currently using.

# Released

## v4.4.0 - 04/26/2026

Optional Google Analytics (gtag.js) scaffold and a `template-version.json` lineage marker so every fork can declare which template version it's currently aligned with — regardless of whether the fork keeps its own product `CHANGELOG.md` (e.g. rundesk-web-app uses product semver for end users; this file tracks template lineage separately).

### New
- `services.google_analytics.measurement_id` config block reading `GOOGLE_ANALYTICS_ID`.
- `@if ($gaId = config('services.google_analytics.measurement_id'))` block in `resources/views/app.blade.php` that emits the standard gtag.js loader + init snippet only when the id is set.
- `tests/Feature/GoogleAnalyticsTest.php` — verifies the snippet is emitted when configured and omitted when empty.
- `GOOGLE_ANALYTICS_ID=` placeholder in `.env.example` with usage notes.
- `template-version.json` at the repo root with four fields: `template` (always `template-laravel-app`), `repo` (canonical template URL), `version` (the highest template version whose changes are fully applied), `updated` (ISO date of the last bump).

### Migration
- Copy `tests/Feature/GoogleAnalyticsTest.php` into your project.
- In `config/services.php`, add the `google_analytics` block reading `env('GOOGLE_ANALYTICS_ID')` (or hard-code your property as the `env()` default if you want the snippet to render without any env wiring).
- In `resources/views/app.blade.php`, paste the `@if ($gaId = config('services.google_analytics.measurement_id')) ... @endif` block immediately after `@inertiaHead` and before the closing `</head>` tag.
- Add `GOOGLE_ANALYTICS_ID=` to your `.env.example`. To activate without env, default the config: `env('GOOGLE_ANALYTICS_ID', 'G-XXXXXXXXXX')`.
- Copy `template-version.json` into your project. Set `version` to the highest template version whose migration has been *fully* applied in your codebase. When you next apply a template migration, bump `version` to that release and refresh `updated`. A surveyor can run `jq -r .version */template-version.json` (or similar) to see every fork's template version at a glance.

## v4.3.0 - 04/20/2026

Deployment and CI reliability — new storage bootstrap command, removal of composer scripts that broke `composer install --no-dev`, and a PHPStan memory fix so `pnpm check:php` passes consistently.

### New
- `app:ensure-storage` Artisan command — idempotently creates missing `storage/` subdirectories and writes the standard Laravel `.gitignore` file inside each. Safe to run on every deploy.
- Passport OAuth key generation is gated behind a string-based `class_exists` check, so the command works whether or not `laravel/passport` is installed — and produces no IDE or static-analysis errors when absent.
- Feature tests covering directory creation, idempotency, partial-tree recovery, `.gitignore` restoration, and the Passport-skip path.

### Changed
- Bumped PHPStan's memory limit to `512M` in both `composer.json` (`analyse` script) and `package.json` (`check:php` script). The default 128M was intermittently crashing PHPStan's parallel worker on this codebase.

### Removed
- `post-install-cmd` from `composer.json` — it ran `ide-helper:generate`, which fails under `composer install --no-dev` (since `barryvdh/laravel-ide-helper` is a dev dependency). This was the common "had to use `--no-scripts` on deploy" failure.
- `post-update-cmd` from `composer.json` — it ran the same `ide-helper:generate` plus a stale `vendor:publish --tag=laravel-assets` (no package in this stack publishes under that tag).

### Migration
- Copy `app/Console/Commands/EnsureStorage.php` and `tests/Feature/EnsureStorageTest.php` into your project.
- In your `composer.json`, delete the `post-install-cmd` and `post-update-cmd` entries if they still contain `ide-helper:generate` or `vendor:publish --tag=laravel-assets`. Keep `post-autoload-dump`, `post-root-package-install`, `post-create-project-cmd`, and the named scripts.
- In your `composer.json`, change the `analyse` script to `"phpstan analyse --memory-limit=512M"`.
- In your `package.json`, change the `check:php` script's `phpstan analyse` invocation to `phpstan analyse --memory-limit=512M`.
- Run `composer update --lock` to refresh the lock file's content hash.
- (Optional but recommended) Add `php artisan app:ensure-storage` to your deploy script — idempotent, safe to run every time. Developers who want IDE helper files can still run `php artisan ide-helper:generate` manually.

## v4.2.0 - 02/14/2026

### Convention Compliance
- Replaced Tabler icons with Lucide in all `app/` components (AppLayout, ProfileMenu, Header, SideNav)
- Removed `lang="ts"` from 9 `app/` components — converted to plain JS with runtime `defineProps`
- Replaced barrel imports with direct file imports across ~55 consumer files
- Deleted 7 barrel `index.ts` files from `app/` and `site/` directories
- Replaced inline SVG breadcrumb chevron in Header with Lucide `ChevronRight`

## v4.1.0 - 02/01/2026

### Documentation
- Added VitePress documentation site in `/docs/`
- New docs: Getting Started, Architecture, and Guidelines sections
- Commands: `pnpm docs:dev`, `pnpm docs:build`

### Code Quality
- Added ESLint and Stylelint to docs with 4-space indentation
- Parallelized `pnpm check` using `concurrently` for faster CI
- Updated AGENTS.md to reference VitePress docs instead of PRDs

## v4.0.0 - 12/26/2025

Major overhaul with new UI framework, comprehensive testing, real-time features, and optional multi-tenancy.

### UI Framework
- Replaced PrimeVue with shadcn-vue (Radix Vue primitives)
- Updated to Tailwind CSS v4 with new CSS theme variables
- Added 50+ production-ready UI components
- Added Stylelint for CSS linting (`pnpm lint:css`)

### New Components
- Combobox, Command palette, TagsInput, MaskInput, NumberInput
- Tiptap rich text editor with customizable tools
- Charts (Area, Bar, Line, Pie, Donut, Radar)
- Carousel, Resizable panels, Context menus
- Dropzone file uploads, Loading states, Alerts

### Real-Time & WebSockets
- Added Laravel Reverb WebSocket support
- Added Echo composables (`useChannel`, `usePrivateChannel`, `useListen`)
- Optional WebSocket via `VITE_REVERB_ENABLED` env variable

### Security
- Added SecurityHeaders middleware (X-Frame-Options, CSP, HSTS, etc.)
- Added custom rate limiting (API, auth, uploads)
- Added Horizon access control via allowed emails config
- Added custom error pages (403, 404, 500, 503)
- Added password rules enforcement

### Testing & Code Quality
- Added Pest PHP testing framework (`composer test`)
- Added Vitest for JS/Vue unit testing (`pnpm test`)
- Added GitHub Actions workflow for automated checks
- New pnpm scripts: `check`, `check:php`, `check:js`, `lint`, `lint:fix`
- Converted all existing PHP tests to Pest syntax

### SEO & Production
- Added SSR support with documentation
- Added SEO/social meta tags (Open Graph, Twitter Cards)
- Added sitemap generation command (`php artisan sitemap:generate`)
- Added user last seen tracking middleware
- Added user activation status and enforcement

### Developer Experience
- Switched from npm to pnpm for package management
- Updated AGENTS.md and README.md with comprehensive docs
- Removed atlas-ui dependency

## v3.1.0 - 08/11/2025

- Added SSR support.

## v3.0.0 -

- Added @atlas/ui.
- Added Atlas Laravel.
- Laravel Standards and docs.

## v2.2.0 - 06/13/2025

Improves the overall layout app functionality.

- Added `optimizeDeps` and `ssr` in `vite.config.js
- Added examples with new `<TableActions>`
- Updated `HandlesIndexOptions` to allow for session management and filters
- Updated examples with routes/index for filtering on POST tables
- Updated atlas for `useDataTableOptions` improvements
- Moved `ziggy` plugin into the atlas package
- For `<Table>` usage, use:
1. `activeColumnList` prop array of view columns
2. `itemTotal` prop for total users
3. Update the columns from `field` to `key`

Add new dep: 
```
"vuedraggable": "^4.1.0"
```

## v2.1.3 - 05/30/2025

Improves the overall layout app functionality.

- Updated layoutApp with more example options with title and custom width.
- Updated page content for new side content examples
- Updated the atlas deps for `"@tiptap/extension-placeholder": "^2.12.0",`
- Updated `base.css` with scrollbar styling
- Updated atlas to `v1.1.19`

## v2.1.2 - 05/22/2025

Adds examples of datatables atlas component and composable usage with inertia response.

- Updated composer deps for `league/flysystem-aws-s3-v3`
- Updated `config/session.php` for "172800" (48 hours).
- Updated examples to use new data table component, button menu and profile menu.
- Updated examples to use `<LinkPaginator>` for laravel
- Added new trait for reusable data tables options `HandlesIndexOptions`

## v2.1.1 - 05/18/2025

Adds examples of datatables atlas component and composable usage with inertia response.

- Updated `vite.config.js` for auto-loading of `useDataTableOptions` 

## v2.1.0 - 05/17/2025

Updated atlas for page-level app components.

This update simplifies page components making building faster app-page-level with built-in navs.

- Updated `eslint.config.js` removing annoying component prop limit
- Updated `vite.config.js` updating for atlas component auto-loading
- Updated `tailwind.config.js` with `'./node_modules/atlas-ui/src/**/*.{js,ts,vue}'`
- Updated example templates to favor new atlas `<LayoutApp>` and replacing example layouts
- Updates Atlas fields for better auto-loading `<AtlasFormField>` is now `<LabelField>` etc.
- Atlas adds new Tiptap editor (optional deps), if you want to install them, use:

```
"@tiptap/extension-bold": "^2.11.7",
"@tiptap/extension-bullet-list": "^2.11.7",
"@tiptap/extension-hard-break": "^2.11.7",
"@tiptap/extension-link": "^2.11.7",
"@tiptap/extension-list-item": "^2.11.7",
"@tiptap/extension-ordered-list": "^2.11.7",
"@tiptap/starter-kit": "^2.11.7",
"@tiptap/vue-3": "^2.11.7",
```

## v2.0.2 - 05/04/2025

Minor example changes. Around the release for new AtlasFrame and scroll control.

- Replaces the `<PageMain>` component with new `<AtlasFrame page>`
- Replaces the `usePageTop` with the new atlas `useScroll` composable
- Updated `vite.config.js`

## v2.0.1 - 05/01/2025

Minor update, most of the changes are examples.

- Updated `vite.config.js`
- Updated `resources/js/setup.js`

## v2.0.0 - 04/26/2025

This update centralizes the base components into atlas ui repo

- Install package `npm install github:tmarois/atlas-ui#semver:^1.0.0`
- Updated `tailwind.config.js`
- Updated `vite.config.js` (new components and composables for autoloading)
- Updated `resources/js/setup.js`
- Updated `resources/css/app.css`
- Deleted all `/components/_volt/~` (now loading from custom atlas-ui package), this removes all the volt components.

# Unreleased 

## v1.1.0 - 04/04/2025

- Removed the lara-mod presets and moved to [PrimeVue Volt](https://volt.primevue.org/)
- Removes a lot of unneeded auto-loading
- Removes Pinia state management
- Added Laravel Solo command

## v1.0.0 - 07/27/2024

Updated this template from PrimeVue 3 to PrimeVue 4

1. `npm remove primevue`
2. `npm install -D primevue`
3. `npm i -D tailwindcss-primeui`
4. Add `require('tailwindcss-primeui')` to tailwind plugins config.
5. Replace the `resources/css/theme.css` file
6. Remove all the custom attributes in tailwind config theme `colors`.
7. If you made custom theming, you will need to update those.
8. Use the Lara-mod theme or Download the [v4 presets](https://github.com/primefaces/primevue-tailwind/releases)
