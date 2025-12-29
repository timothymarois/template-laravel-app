# Laravel Vue Inertia Starter

[![Automated Checks](https://github.com/timothymarois/template-laravel-app/actions/workflows/checks.yml/badge.svg)](https://github.com/timothymarois/template-laravel-app/actions/workflows/checks.yml)

Production-grade applications require more than just code—they need authentication, real-time updates, background jobs, testing, monitoring, security hardening, SEO, and deployment pipelines. Setting all of this up correctly takes weeks.

**This starter kit handles all of it.** Every layer has been considered, implemented, and wired together so you can focus entirely on your application logic. Up and running in 5 minutes.

---

## Everything You Need

**Core Stack**

- ✅ Laravel 12 + Vue 3 + Inertia 2 — SPA experience without API complexity
- ✅ Tailwind 4 + shadcn-vue — beautiful, accessible components you own and customize
- ✅ 50+ UI components — production-grade, fully customizable, ready out of the box

**Production Ready**

- ✅ SSR enabled by default — SEO-friendly, fast first paint
- ✅ Security headers, CORS, rate limiting — hardened out of the box
- ✅ Sanctum authentication — session-based auth with CSRF protection

**Real-Time & Background Jobs**

- ✅ Reverb WebSockets + Echo — real-time events, zero external dependencies
- ✅ Horizon queues — Redis-powered job processing with dashboard

**Developer Experience**

- ✅ Solo dev runner — all processes in one terminal
- ✅ Ziggy routes — use Laravel named routes directly in Vue
- ✅ Hot reload — instant feedback during development

**Testing & Code Quality**

- ✅ Pest + Vitest — full test coverage, backend and frontend
- ✅ Larastan + ESLint + Stylelint + Pint — static analysis on both stacks
- ✅ Single command checks — `pnpm check` runs everything before you commit

**Monitoring & Debugging**

- ✅ Log Viewer — browse application logs in the browser
- ✅ Horizon dashboard — monitor queues and failed jobs
- ✅ Sentry-ready — error tracking integration configured
- ✅ Health check endpoint — load balancer and uptime monitoring

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

---

## Documentation

### Setup & Development
- [Tech Stack](#tech-stack) — Core technologies and why they're chosen
- [Component Architecture](#component-architecture) — How components are organized
- [Development Tools](#development-tools) — Testing, linting, and dev runner

### Production
- [Deployment](#deployment) — Build commands and production setup
- [SSR](#server-side-rendering) — Server-side rendering configuration
- [Security](#security) — Headers, rate limiting, CORS

### Reference
- [WebSockets](#websockets) — Real-time with Reverb and Echo
- [SEO & Social](#seo--social-sharing) — Meta tags and sitemaps
- [Patterns](#patterns) — Routes, notifications, icons, API requests

---

## Tech Stack

### Core

**Laravel** (PHP) handles the backend, **Vue 3** (JavaScript) powers the frontend, and **Inertia** bridges them seamlessly—giving you SPA behavior without building a separate API.

| Package | Version | Purpose |
|---------|---------|---------|
| [Laravel](https://laravel.com/docs/12.x) | 12 | Backend framework |
| [Vue.js](https://vuejs.org/guide/introduction.html) | 3 | Frontend framework |
| [Inertia.js](https://inertiajs.com/) | 2 | SPA without API complexity |

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

Components follow a 2-layer structure:

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
import { AppLayout } from '@/components/app';
import { SiteLayout } from '@/components/site';
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
| `pnpm check:js` | ESLint + Stylelint + Vitest + build |

**Individual tools:**
- [Pest](https://pestphp.com/) — `./vendor/bin/pest`
- [Vitest](https://vitest.dev/) — `pnpm test:watch`
- [Larastan](https://github.com/larastan/larastan) — `./vendor/bin/phpstan analyse`
- [Laravel Pint](https://laravel.com/docs/12.x/pint) — `./vendor/bin/pint`

</details>

### Monitoring

| Tool | URL | Purpose |
|------|-----|---------|
| [Log Viewer](https://github.com/opcodesio/log-viewer) | `/log-viewer` | Browse application logs |
| [Horizon](https://laravel.com/docs/11.x/horizon) | `/horizon` | Monitor queues and jobs |

For production error tracking, [Sentry](https://docs.sentry.io/platforms/php/guides/laravel/) integrates directly with Jira and git commits.

**Health check:** Laravel provides `/up` for load balancer health checks.

### Solo (Dev Runner)

[Solo](https://github.com/soloterm/solo) runs all development processes in a single terminal UI:

```bash
php artisan solo
```

Configured commands: SSR server, queue worker, Reverb, scheduler, and more. See `config/solo.php`.

---

## Deployment

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

Configure your reverse proxy (Nginx) to handle WebSocket connections on port 443 and proxy to Reverb. See [Reverb docs](https://laravel.com/docs/12.x/reverb#production).

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

[Laravel Reverb](https://laravel.com/docs/12.x/reverb) provides real-time WebSocket communication, pre-configured with [Laravel Echo](https://laravel.com/docs/12.x/broadcasting#client-side-installation).

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
