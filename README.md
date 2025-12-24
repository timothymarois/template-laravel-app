# Laravel Vue Inertia Template

[![Automated Checks](https://github.com/timothymarois/template-laravel-app/actions/workflows/checks.yml/badge.svg)](https://github.com/timothymarois/template-laravel-app/actions/workflows/checks.yml)

This starter kit is designed for any project, providing a quick playground that gets you up and running within 5 minutes of setup. The goal is to offer an ideal starting point, eliminating the need to reinvent the wheel or re-implement foundational systems for each project. It addresses all common use-cases when building web-based applications; **allowing you to focus solely on your application requirements**.

---

## Tech Stack

#### Core

This template uses **Laravel** (PHP) as your backend and **Vuejs** (JavaScript) as your frontend. Inertia is implemented so that both can communicate seamlessly. Inertia can save you a monumental amount of time when trying to handle client-side state management.

- ✅ Laravel v12 – [Documentation](https://laravel.com/docs/12.x)
- ✅ Vuejs v3 – [Documentation](https://vuejs.org/guide/introduction.html)
- ✅ Inertiajs v2 – [Documentation](https://inertiajs.com/) | [Why Inertia?](https://inertiajs.com/who-is-it-for)

> Inertia empowers you to build a modern, JavaScript-based single-page application without the tiresome complexity.

#### Design

For the design side, we want to focus on customization, theming, and utility components. shadcn-vue provides beautifully designed, accessible components built on Radix Vue primitives. Components are copied into your project, giving you full control over styling and behavior.

- ✅ Tailwind v4 – [Documentation](https://tailwindcss.com/docs/installation)
- ✅ shadcn-vue – [Documentation](https://www.shadcn-vue.com/)
- ✅ Lucide Icons – [Documentation](https://lucide.dev/)

**Adding new shadcn components:**

```bash
pnpm dlx shadcn-vue@latest add button
pnpm dlx shadcn-vue@latest add dialog
```

Components are installed to `resources/js/components/ui/`.

**Theming:**

Theme variables are defined in `resources/css/theme.css` using CSS custom properties. Modify the `:root` and `.dark` selectors to customize colors. The `base.css` file contains framework utilities and rarely needs modification.

---

## Component Architecture

Components are organized in a simple 2-layer architecture:

```
components/
├── ui/              # Base components (shadcn + custom enhanced)
├── app/             # Application components (authenticated app)
└── site/            # Website components (public marketing pages)
```

**Base Components (`ui/`):**
- All base UI components live here
- shadcn primitives (`ButtonBase`, `CardBase`) + enhanced versions (`Button`, `Card`)
- Stateless and reusable across contexts

**App Components (`app/`):**
- Layouts, navigation, modals for your application
- May use Inertia, routes, and authentication
- For authenticated application functionality

**Site Components (`site/`):**
- Layouts and components for public-facing website pages
- May use Inertia and routes
- For marketing pages, landing pages, unauthenticated flows

**Import examples:**

```typescript
// Base components
import { Button, Card, DataTable } from '@/components/ui';

// Application-specific
import { AppLayout, Sidebar } from '@/components/app';

// Website-specific
import { SiteLayout } from '@/components/site';
```

See `AGENTS.md` for detailed component guidelines and decision trees.

---

## Tests and Linting

Pre-installed code-linting and automated test services to help keep your CI pipeline protected from lower code quality and breaking changes.

Linting is used on both PHP and JS side to keep all collaborators using the same format.

**Code Style:** All code uses **4-space indentation** (JS, Vue, CSS, PHP).

**Main Commands:**

| Command             | Description                                                   |
|---------------------|---------------------------------------------------------------|
| `pnpm lint`         | Run ESLint on JS/Vue files                                    |
| `pnpm lint:fix`     | Run ESLint and auto-fix issues                                |
| `pnpm lint:css`     | Run Stylelint on CSS files                                    |
| `pnpm lint:css:fix` | Run Stylelint and auto-fix CSS issues                         |
| `pnpm test`         | Run JS unit tests (Vitest)                                    |
| `pnpm check:php`    | Run PHP checks (Pint, Larastan, Pest)                         |
| `pnpm check:js`     | Run JS checks (ESLint, Stylelint, Vitest, client build)       |
| `pnpm check`        | Run all checks (PHP + JS + SSR build) - use before committing |

**Individual Tools:**

- ✅ [Pest](https://pestphp.com/) - `composer test` or `./vendor/bin/pest`
- ✅ [Vitest](https://vitest.dev/) - `pnpm test` or `pnpm test:watch`
- ✅ [Larastan](https://github.com/larastan/larastan) - `./vendor/bin/phpstan analyse`
- ✅ [Laravel Pint](https://laravel.com/docs/12.x/pint) - `./vendor/bin/pint`
- ✅ [Vue ESLint](https://eslint.vuejs.org/) - `pnpm lint` or `pnpm lint:fix`
- ✅ [Stylelint](https://stylelint.io/) - `pnpm lint:css` or `pnpm lint:css:fix`

---

## Monitoring

Pre-installed monitoring packages allow you to view logs, worker jobs, and debug effortlessly in real-time.

- ✅ [Log Viewer](https://github.com/opcodesio/log-viewer) - `/log-viewer`
- ✅ [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - `/horizon`

**External Services:**

- ✅ [Sentry.io](https://sentry.io/) | [Docs](https://docs.sentry.io/platforms/php/guides/laravel/) - It's recommended to use a service to collect and notify you of ongoing errors. Sentry is a great tool since it connects directly with Jira and the suspecting commits that caused breaking changes.

---

## WebSockets (Reverb)

[Laravel Reverb](https://laravel.com/docs/12.x/reverb) provides real-time WebSocket communication. Pre-configured with [Laravel Echo](https://laravel.com/docs/12.x/broadcasting#client-side-installation) on the frontend.

```bash
php artisan reverb:start
```

**Vue composables (recommended):**

```js
import { useChannel, usePrivateChannel, useListen } from '@/composables/useEcho';

// Public channel with auto-cleanup on unmount
const { channel } = useChannel('orders');
channel.value.listen('OrderShipped', (e) => console.log(e));

// Private channel (requires auth)
const { channel: privateChannel } = usePrivateChannel('user.1');

// Simplified listener (auto-subscribes and cleans up)
useListen('orders', 'OrderShipped', (e) => console.log(e.order));
useListen('user.1', 'MessageSent', (e) => console.log(e), { private: true });
```

**Creating broadcast events (Laravel):**

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

**Configure `.env`:**

```
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=localhost
REVERB_PORT=8080
```

---

## Solo (Dev Runner)

[Solo](https://github.com/soloterm/solo) is a terminal UI for running multiple Laravel processes simultaneously during development. Pre-configured commands are available in `config/solo.php`.

```bash
php artisan solo
```

**Configured commands:** SSR server, Queue worker, Reverb WebSockets, Scheduler, JS/PHP checks, Migrations, and more.

---

## Local Installation

These simple steps allow you to install this project on your local env. For production, you should follow the #deployment section

<details>
<summary>Install client-side</summary>

```bash
pnpm install
```
</details>

<details>
<summary>Install server-side</summary>

*(should do this through the docker container)*

```bash
composer install
```
</details>

<details>
<summary>Run database migrations</summary>

```bash
php artisan migrate
```
</details>

<details>
<summary>Run local Dev</summary>

```bash
pnpm dev
```
</details>

---

## Production Deployment

Commands for deploying to production servers. Do not use these for local development.

<details>
<summary>Build steps</summary>

*Run these commands during your CI/CD pipeline or before deploying new code.*

**(1) Install composer deps:**

```bash
composer install --optimize-autoloader --no-dev
```

**(2) Install package deps**

```bash
pnpm install --frozen-lockfile
```

**(3) Database migrations:**

```bash
php artisan migrate --force
```

**(4) Build Client-side (non-SSR):**

```bash
pnpm build
```

**(4) Build (with SSR):**

[Learn more about SSR](https://inertiajs.com/server-side-rendering)

```bash
pnpm build-ssr
```

**(5) Run (with SSR):**

[Learn more about SSR](https://inertiajs.com/server-side-rendering)

```bash
php artisan inertia:start-ssr
```

**(6) Restart SSR server:**

You only need to do this if you are running SSR.

```bash
php artisan inertia:stop-ssr
```

</details>

<details>
<summary>Post-deployment optimization</summary>

*Run these commands after new code is live to cache assets and restart workers.*

```bash
php artisan optimize          # Cache config, routes, and events
php artisan view:cache        # Compile all Blade views
php artisan horizon:terminate # Gracefully restart Horizon workers
php artisan reverb:restart    # Gracefully restart Reverb WebSocket server
```

</details>

<details>
<summary>Running Reverb in production</summary>

Reverb should run as a daemon process via [Supervisor](http://supervisord.org/). Example config:

```ini
[program:reverb]
command=php /path/to/artisan reverb:start --host=0.0.0.0 --port=8080
user=www-data
autostart=true
autorestart=true
```

For production, configure your reverse proxy (Nginx) to handle WebSocket connections on port 443 and proxy to Reverb. See [Reverb docs](https://laravel.com/docs/12.x/reverb#production) for full setup.

</details>

---

## Server-Side Rendering (SSR)

SSR is enabled by default for improved SEO and faster initial page loads. Pages are rendered on the server before being sent to the browser, making content immediately available to search engine crawlers.

**Configuration:**

SSR settings are in `config/inertia.php`:

```php
'ssr' => [
    'enabled' => env('INERTIA_SSR_ENABLED', true),
    'url' => env('INERTIA_SSR_URL', 'http://127.0.0.1:13714'),
],
```

**Build Commands:**

| Command | Description |
|---------|-------------|
| `pnpm build` | Build client-side only |
| `pnpm build-ssr` | Build both client and SSR bundles |

**Running SSR in Production:**

```bash
# Build SSR bundle
pnpm build-ssr

# Start SSR server
php artisan inertia:start-ssr

# Stop SSR server (when redeploying)
php artisan inertia:stop-ssr
```

**SSR-Safe Code:**

When accessing browser APIs (`window`, `document`), use the `isClient` guard:

```typescript
import { isClient } from '@/utils';

if (isClient) {
    // Safe to use window, document, localStorage, etc.
}
```

---

## SEO & Social Sharing

Layouts include built-in support for SEO meta tags and social sharing (Open Graph + Twitter Cards). Tags are rendered server-side via SSR for optimal crawler indexing.

**Usage:**

```vue
<SiteLayout
    title="Product Page"
    description="Discover our amazing product features"
    ogImage="/images/product-og.jpg"
    ogImageAlt="Product screenshot"
    siteName="My App"
>
    <!-- content -->
</SiteLayout>
```

**Available props (all optional):**

| Prop | Description |
|------|-------------|
| `title` | Page title (used in `<title>` and og:title) |
| `description` | Meta description (og:description, twitter:description) |
| `ogImage` | Social sharing image URL (absolute or relative) |
| `ogImageAlt` | Alt text for social image |
| `siteName` | Site name for og:site_name |

**Supported platforms:**
- Facebook, LinkedIn, Slack, Discord (Open Graph)
- Twitter/X (Twitter Cards with `summary_large_image`)
- iMessage, WhatsApp, and other link previews

**Image recommendations:**
- Minimum size: 1200x630px for best display
- Format: JPG or PNG
- Relative paths are automatically converted to absolute URLs

---

## Features:

### Routes (by Ziggy)

The ability to grab the routes in Vue based on the laravel route names. You can control what routes are visible to end-users by modifying `/config/ziggy.php`.

- ✅ [Named routes](https://github.com/tighten/ziggy)

Note: If you are trying to use `route()` inside your vue `<template>` use `$route()`

<details>
<summary>Show example</summary><br>

You can use `php artisan route:list` to get the full named routes that are available.

To get the path of a named route

```js
const path = route('login')
// http://localhost/api/login
```

or you can do links in templates based on @click

```js
$inertia.visit(route('posts.create'))
```

Using model id in routes ([learn more](https://github.com/tighten/ziggy?tab=readme-ov-file#parameters))

```js
route('posts.show', 123);
// http://localhost/posts/123'
```

Using multiple models in routes ([learn more](https://github.com/tighten/ziggy?tab=readme-ov-file#parameters))

```js
route('accounts.posts.show', [1, 123]);
// http://localhost/accounts/1/posts/123'
```

</details>

---

### Rendering Pages (Inertia)

Controllers use dependency injection for rendering Inertia pages:

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

---

### Router & API requests (by Inertia & Axois)

You can use the built-in methods to fetch data or make manual visits

- ✅ [Router requests](https://inertiajs.com/manual-visits)
- ✅ [Axois requests](https://axios-http.com/docs/example)

<details>
<summary>Show example</summary>

Use Inertia built-in `router` to visit routes and modify browser histoty.

*Note: `router` should respond with an inertia response. Use `Axois` for API JSON responses.*

```js
// best to use replace = true to avoid browser history from being added
router.get(route('users'), { search: 'John' }, { replace: true })

// all available methods
router.get(url, data, options)
router.post(url, data, options)
router.put(url, data, options)
router.patch(url, data, options)
router.delete(url, options)
router.reload(options)

// events
router.get(url, data, {
  onBefore: (visit) => {},
  onStart: (visit) => {},
  onProgress: (progress) => {},
  onSuccess: (page) => {},
  onError: (errors) => {},
  onCancel: () => {},
  onFinish: visit => {},
})
```

Using the Axois for background fetching of data without modifying routes.

These are useful if you want to maintain current page state and load external data in components.

By default, running a axois request, should return json response.

```js
axios.get(url)
.then(function (response) {
    // handle success
})
.catch(function (error) {
    // handle error
})
.finally(function () {

});
```
</details>

---

### Toast Notifications (by Sonner)

You can dispatch global toast notifications using Sonner.

- ✅ Toast Notifications

<details>
<summary>Show example</summary>

```js
import { toast } from 'vue-sonner'

// success
toast.success('User saved successfully')

// error
toast.error('Something went wrong')
```
</details>

---

### Icons

Easily add svg/imported icons that can change size and color.

- ✅ [Lucide Icons](https://lucide.dev/) *(recommended)*
- ✅ [Tabler Icons](https://tabler.io/icons)

<details>
<summary>Show example</summary>


**From Lucide**

```vue
<template>
    <Settings class="text-gray-800 size-6" />
</template>

<script setup>
import { Settings, Trash2, Plus } from 'lucide-vue-next';
</script>
```

**From Tabler**

```vue
<template>
    <IconHome class="text-gray-800 size-6" />
</template>

<script setup>
import { IconHome } from '@tabler/icons-vue';
</script>
```

</details>
