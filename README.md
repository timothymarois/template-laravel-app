# Laravel Vue Inertia Starter

[![PHP Checks](https://github.com/timothymarois/template-laravel-app/actions/workflows/php-checks.yml/badge.svg)](https://github.com/timothymarois/template-laravel-app/actions/workflows/php-checks.yml)
[![JavaScript Checks](https://github.com/timothymarois/template-laravel-app/actions/workflows/js-checks.yml/badge.svg)](https://github.com/timothymarois/template-laravel-app/actions/workflows/js-checks.yml)

Production-grade applications require more than just code—they need authentication, real-time updates, background jobs, testing, monitoring, security hardening, SEO, and deployment pipelines. Setting all of this up correctly takes weeks.

**This starter kit handles all of it.** Every layer has been considered, implemented, and wired together so you can focus entirely on your application logic. Up and running in 5 minutes.

---

## Everything You Need

**Core Stack**

- ✅ Laravel 13 + Vue 3 + Inertia 3 — SPA experience without API complexity
- ✅ Tailwind 4 + shadcn-vue — beautiful, accessible components you own and customize
- ✅ 50+ UI components — production-grade, fully customizable, ready out of the box

**Production Ready**

- ✅ SSR enabled by default — SEO-friendly, fast first paint
- ✅ Security headers, CORS, rate limiting — hardened out of the box
- ✅ Sanctum authentication — session-based auth with CSRF protection

**Real-Time & Background Jobs**

- ✅ Reverb WebSockets + Echo — real-time events, zero external dependencies
- ✅ Horizon queues — Redis-powered job processing with dashboard

**Multi-Tenancy (Optional)**

- ✅ stancl/tenancy ^3.10 — DB-per-tenant isolation, off by default. Enable with `php artisan tenancy:enable`. See [`docs/guidelines/tenancy-using.md`](docs/guidelines/tenancy-using.md).

**Developer Experience**

- ✅ Solo dev runner — all processes in one terminal
- ✅ Ziggy routes — use Laravel named routes directly in Vue
- ✅ Hot reload — instant feedback during development

**Testing & Code Quality**

- ✅ Pest + Vitest — full test coverage, backend and frontend
- ✅ Larastan + ESLint + Stylelint + Pint — static analysis on both stacks
- ✅ Single command checks — `pnpm check` runs everything before you commit

**Monitoring & Debugging**

- ✅ Horizon dashboard — monitor queues and failed jobs
- ✅ Sentry-ready — error tracking integration configured
- ✅ Centralized logging — logs to stderr, shipped to the sink of your choice (Loki/Grafana, Axiom, …)
- ✅ Health checks — `/up` for the container, plus deep `/health` covering DB, Redis, Horizon, queue, scheduler & Reverb

**SEO & Social**

- ✅ Open Graph + Twitter Cards — social sharing just works
- ✅ Sitemap generation — auto-generates from your routes
- ✅ Meta tag management — per-page title, description, images

---

## Quick Start

We recommend [Laravel Herd](https://herd.laravel.com/) for local development—it provides PHP, nginx, and database services with zero configuration.

```bash
# Clone and install
git clone <repo-url> my-app && cd my-app
pnpm install
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and start
php artisan migrate
pnpm dev
```

Visit `http://localhost:8000` (or your Herd domain) to see the app. Register a new account to access `/admin`. The component showcase is at `/admin/components`.

---

## For coding agents

The full prompts live in [`.template/ADOPT.md`](.template/ADOPT.md). Hand your agent the launcher for the
flow you want — it reads the detailed prompt from there and follows it.

**🌱 Starting a new project** from this clone:

```
Set this repo up as our own product from the template-laravel-app starter. Read .template/ADOPT.md and
follow the "Start a new project" prompt end to end — rename the project to <OUR NAME>, strip the template
machinery, and rewrite .knowledge/ for our product. Ask me about the component ontology and anything you
must infer. Start by reading AGENTS.md.
```

**⬆️ Upgrading this fork** to a newer template version:

```
Upgrade this fork to the latest template version. Read .template/ADOPT.md and follow the "Upgrade an
existing fork" prompt — find our template-manifest version, apply the .template/migrations/ in order
(honoring any skip notes), and mold each migration to what this fork actually is. Don't break our
customizations; stop and ask before any hard gate. Start by reading AGENTS.md.
```

---

## Documentation

Contributor and agent documentation lives in [`.knowledge/`](.knowledge/) — start at
[`.knowledge/README.md`](.knowledge/README.md) for the knowledge map (orientation, how-to guides, and tested
PRDs), or [`.knowledge/OVERVIEW.md`](.knowledge/OVERVIEW.md) for the platform in plain language. The rules
every agent follows are in [`AGENTS.md`](AGENTS.md). This documentation system is
[knowledge-template](https://github.com/timothymarois/knowledge-template).

### Quick Links
- [Tech Stack](#tech-stack) — Core technologies and why they're chosen
- [Component Architecture](#component-architecture) — How components are organized
- [Development Tools](#development-tools) — Testing, linting, and dev runner
- [Deployment](#deployment) — Build commands and production setup
- [Security](#security) — Headers, rate limiting, CORS

---

## Tech Stack

### Core

**Laravel** (PHP) handles the backend, **Vue 3** (JavaScript) powers the frontend, and **Inertia** bridges them seamlessly—giving you SPA behavior without building a separate API.

| Package | Version | Purpose |
|---------|---------|---------|
| [Laravel](https://laravel.com/docs/13.x) | 13 | Backend framework |
| [Vue.js](https://vuejs.org/guide/introduction.html) | 3 | Frontend framework |
| [Inertia.js](https://inertiajs.com/) | 3 | SPA without API complexity |

### Design

Components are fully customizable. shadcn-vue installs components directly into your project—you own the code.

| Package | Purpose |
|---------|---------|
| [Tailwind CSS 4](https://tailwindcss.com/docs/installation) | Utility-first styling |
| [shadcn-vue](https://www.shadcn-vue.com/) | Accessible component primitives |
| [Lucide Icons](https://lucide.dev/) | Icon library |

<details>
<summary><strong>Adding shadcn components</strong></summary>

```bash
pnpm dlx shadcn-vue@latest add button
pnpm dlx shadcn-vue@latest add dialog
```

Components install to `resources/js/components/ui/`.

**Theming:** Modify CSS custom properties in `resources/css/theme.css` under `:root` and `.dark` selectors.

</details>

---

## Component Architecture

Components follow a 3-layer structure:

```
components/
├── ui/     # Base components (shadcn + custom)
├── app/    # Authenticated application components
└── site/   # Public marketing page components
```

| Layer | Purpose | Example |
|-------|---------|---------|
| `ui/` | Stateless, reusable primitives | `Button`, `Card`, `DataTable` |
| `app/` | App layouts, navigation, modals | `AppLayout`, `Sidebar` |
| `site/` | Public pages, marketing | `SiteLayout`, `Hero` |

```typescript
import { Button, Card } from '@/components/ui';
import AppLayout from '@/components/app/layout/AppLayout.vue';
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
```

See `AGENTS.md` for detailed component guidelines.

---

## Development Tools

**Code style:** 4-space indentation across all files (JS, Vue, CSS, PHP).

### Commands

| Command | Description |
|---------|-------------|
| `pnpm dev` | Start development server |
| `pnpm check` | Run all checks (use before committing) |
| `php artisan solo` | Run all dev processes in one terminal |

<details>
<summary><strong>All available commands</strong></summary>

| Command | Description |
|---------|-------------|
| `pnpm lint` | ESLint on JS/Vue |
| `pnpm lint:fix` | ESLint with auto-fix |
| `pnpm lint:css` | Stylelint on CSS |
| `pnpm lint:css:fix` | Stylelint with auto-fix |
| `pnpm test` | Run Vitest |
| `pnpm check:php` | Pint + Larastan + Pest |
| `pnpm check:js` | ESLint + Stylelint + typecheck + Vitest |

**Individual tools:**
- [Pest](https://pestphp.com/) — `./vendor/bin/pest`
- [Vitest](https://vitest.dev/) — `pnpm test:watch`
- [Larastan](https://github.com/larastan/larastan) — `./vendor/bin/phpstan analyse`
- [Laravel Pint](https://laravel.com/docs/13.x/pint) — `./vendor/bin/pint`

</details>

### Monitoring

| Tool | URL | Purpose |
|------|-----|---------|
| [Horizon](https://laravel.com/docs/13.x/horizon) | `/horizon` | Monitor queues and jobs |

For production error tracking, [Sentry](https://docs.sentry.io/platforms/php/guides/laravel/) integrates directly with Jira and git commits. Application logs go to `stderr` and are shipped to a central log store of your choice — see the [Logging guide](docs/guidelines/logging.md).

**Health checks:** `/up` is the lightweight container/load-balancer gate. `/health` (via `spatie/laravel-health`) deep-checks each dependency — database, Redis, Horizon, queue, scheduler, and Reverb — for uptime monitoring; each check self-gates to the services a project actually runs. See the [Health Checks guide](docs/guidelines/health-checks.md).

### Solo (Dev Runner)

[Solo](https://github.com/soloterm/solo) runs all development processes in a single terminal UI:

```bash
php artisan solo
```

Configured commands: SSR server, queue worker, Reverb, scheduler, and more. See `config/solo.php`.

---

## Deployment

### Self-Hosted (Docker + Coolify)

This template ships with a **production-ready Docker setup** for self-hosting on any VPS — no managed PaaS required. One universal image runs the app, SSR, queues, scheduler, and WebSockets; you enable only what you need. We recommend [Coolify](https://coolify.io/) on a [DigitalOcean](https://www.digitalocean.com/) droplet, but it works on any Docker host (AWS, Hetzner, Linode, bare metal).

See [`docker/README.md`](docker/README.md) for the full setup, knobs, and Coolify configuration.

### Build

```bash
# 1. Install dependencies
composer install --optimize-autoloader --no-dev
pnpm install --frozen-lockfile

# 2. Run migrations
php artisan migrate --force

# 3. Build frontend
pnpm build          # Client-side only
pnpm build-ssr      # Client + SSR (recommended)
```

### Post-deployment

```bash
php artisan optimize           # Cache config, routes, events
php artisan view:cache         # Compile Blade views
php artisan sitemap:generate   # Regenerate sitemap
php artisan horizon:terminate  # Restart queue workers
php artisan reverb:restart     # Restart WebSocket server
```

<details>
<summary><strong>Running Reverb in production</strong></summary>

Run Reverb as a daemon via [Supervisor](http://supervisord.org/):

```ini
[program:reverb]
command=php /path/to/artisan reverb:start --host=0.0.0.0 --port=8080
user=www-data
autostart=true
autorestart=true
```

Configure your reverse proxy (Nginx) to handle WebSocket connections on port 443 and proxy to Reverb. See [Reverb docs](https://laravel.com/docs/13.x/reverb#production).

</details>

---

## Server-Side Rendering

SSR is enabled by default for better SEO and faster initial loads.

### Configuration

Settings in `config/inertia.php`:

```php
'ssr' => [
    'enabled' => env('INERTIA_SSR_ENABLED', true),
    'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),
],
```

### Running SSR

```bash
pnpm build-ssr              # Build SSR bundle
php artisan inertia:start-ssr   # Start SSR server
php artisan inertia:stop-ssr    # Stop (when redeploying)
```

### SSR-Safe Code

Guard browser APIs with `isClient`:

```typescript
import { isClient } from '@/utils';

if (isClient) {
    // Safe: window, document, localStorage
}
```

---

## Security

### Security Headers

The `SecurityHeaders` middleware adds:
- `X-Frame-Options: SAMEORIGIN` — Prevents clickjacking
- `X-Content-Type-Options: nosniff` — Prevents MIME sniffing
- `Referrer-Policy: strict-origin-when-cross-origin`
- `Permissions-Policy` — Restricts camera, microphone, geolocation
- `Strict-Transport-Security` — Forces HTTPS in production

### Rate Limiting

Pre-configured in `AppServiceProvider`:

| Limiter | Limit | Use case |
|---------|-------|----------|
| `api` | 60/min per user | General API |
| `auth` | 5/min per IP | Login, registration |
| `uploads` | 10/min per user | File uploads |

Apply to routes: `Route::middleware('throttle:auth')->post('/login', ...)`

### CORS

Set allowed origins in production:

```env
CORS_ALLOWED_ORIGINS=https://example.com,https://api.example.com
```

### Authentication

Sanctum provides session-based SPA auth with CSRF protection, rate-limited login attempts, and session regeneration.

---

## WebSockets

[Laravel Reverb](https://laravel.com/docs/13.x/reverb) provides real-time WebSocket communication, pre-configured with [Laravel Echo](https://laravel.com/docs/13.x/broadcasting#client-side-installation).

```bash
php artisan reverb:start
```

### Vue Composables

```js
import { useChannel, usePrivateChannel, useListen } from '@/composables/useEcho';

// Public channel (auto-cleanup on unmount)
const { channel } = useChannel('orders');
channel.value.listen('OrderShipped', (e) => console.log(e));

// Simplified listener
useListen('orders', 'OrderShipped', (e) => console.log(e.order));
useListen('user.1', 'MessageSent', (e) => console.log(e), { private: true });
```

<details>
<summary><strong>Creating broadcast events (Laravel)</strong></summary>

```bash
php artisan make:event OrderShipped
```

```php
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OrderShipped implements ShouldBroadcast
{
    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [new Channel('orders')];
    }
}

// Dispatch: event(new OrderShipped($order));
```

</details>

<details>
<summary><strong>Environment configuration</strong></summary>

```env
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
```

</details>

---

## SEO & Social Sharing

Layouts include built-in meta tags for SEO and social sharing (Open Graph + Twitter Cards), rendered server-side for optimal indexing.

### Usage

```vue
<SiteLayout
    title="Product Page"
    description="Discover our amazing product features"
    ogImage="/images/product-og.jpg"
>
    <!-- content -->
</SiteLayout>
```

| Prop | Description |
|------|-------------|
| `title` | Page title (`<title>` and og:title) |
| `description` | Meta description |
| `ogImage` | Social sharing image (1200x630px recommended) |
| `ogImageAlt` | Alt text for social image |
| `siteName` | Site name for og:site_name |

### Sitemap

```bash
php artisan sitemap:generate
```

Creates `public/sitemap.xml` with public routes only (excludes admin, auth, API).

<details>
<summary><strong>Adding dynamic pages to sitemap</strong></summary>

Edit `addDynamicPages()` in `GenerateSitemap.php`:

```php
protected function addDynamicPages(Sitemap $sitemap): void
{
    Product::where('published', true)->each(function ($product) use ($sitemap) {
        $sitemap->add(
            Url::create(route('products.show', $product))
                ->setLastModificationDate($product->updated_at)
                ->setPriority(0.8)
        );
    });
}
```

For production, uncomment the sitemap line in `public/robots.txt`:
```
Sitemap: https://yourdomain.com/sitemap.xml
```

</details>

---

## Patterns

### Named Routes (Ziggy)

Access Laravel routes in Vue via [Ziggy](https://github.com/tighten/ziggy). Control visibility in `/config/ziggy.php`.

```js
// Get route path
route('login')  // http://localhost/login

// With parameters
route('posts.show', 123)  // http://localhost/posts/123
route('accounts.posts.show', [1, 123])  // http://localhost/accounts/1/posts/123

// Navigate
$inertia.visit(route('posts.create'))
```

Use `$route()` in templates, `route()` in script.

### Rendering Pages (Inertia)

```php
use Inertia\ResponseFactory;

class UserController extends Controller
{
    public function __construct(protected ResponseFactory $inertia) {}

    public function index(): Response
    {
        return $this->inertia->render('admin/users/Index', [
            'users' => User::paginate(),
        ]);
    }
}
```

### API Requests

**Inertia Router** — For page visits with browser history:

```js
router.get(route('users'), { search: 'John' }, { replace: true })
router.post(url, data, options)
router.delete(url, options)
```

**Axios** — For background data fetching without navigation:

```js
axios.get(url)
    .then((response) => { /* handle success */ })
    .catch((error) => { /* handle error */ })
```

### Toast Notifications (Sonner)

```js
import { toast } from 'vue-sonner'

toast.success('User saved successfully')
toast.error('Something went wrong')
```

### Icons

```vue
<script setup>
// Lucide (recommended)
import { Settings, Trash2, Plus } from 'lucide-vue-next';

// Tabler
import { IconHome } from '@tabler/icons-vue';
</script>

<template>
    <Settings class="text-gray-800 size-6" />
    <IconHome class="text-gray-800 size-6" />
</template>
```

Browse icons: [Lucide](https://lucide.dev/) | [Tabler](https://tabler.io/icons)

---

## License

[MIT](LICENSE)
