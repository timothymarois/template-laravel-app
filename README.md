# Laravel Vue Inertia Template

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

For the design side, we want to focus on customization, theming, and utility components. PrimeVue offers the most comprehensive set of utility components that are fully customizable without bloating. Tree-shaking makes sure you only import the components you actually use keeping your overall project light-weight. 

- ✅ Tailwind v4 – [Documentation](https://tailwindcss.com/docs/installation)
- ✅ PrimeVue v4 (unstyled) – [Documentation](https://primevue.org/)

---

## Tests and Linting

Pre-installed code-linting and automated test services to help keep your CI pipeline protected from lower code quality and breaking changes.

Linting is used on both PHP and JS side to keep all collaborators using the same format.

- ✅ PHPUnit - `php artisan test`
- ✅ [Larastan](https://github.com/larastan/larastan) - `./vendor/bin/phpstan analyse`
- ✅ [Laravel Pint](https://laravel.com/docs/11.x/pint) - `./vendor/bin/pint`
- ✅ [Vue ESLint](https://eslint.vuejs.org/) - `npm run eslint -- --fix` (or with `--quiet`)

---

## Monitoring

Pre-installed monitoring packages allow you to view logs, worker jobs, and debug effortlessly in real-time.

- ✅ [Log Viewer](https://github.com/opcodesio/log-viewer) - `/log-viewer`
- ✅ [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - `/horizon`

**External Services:**

- ✅ [Sentry.io](https://sentry.io/) | [Docs](https://docs.sentry.io/platforms/php/guides/laravel/) - It's recommended to use a service to collect and notify you of ongoing errors. Sentry is a great tool since it connects directly with Jira and the suspecting commits that caused breaking changes.

---

## Local Installation

These simple steps allow you to install this project on your local env. For production, you should follow the #deployment section

<details>
<summary>Install client-side</summary>

```bash
npm install
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
npm run dev
```
</details>

---

## Deployment: 

For deployment you will need to run a series of commands before and after new code release.

<details>
<summary>Before release</summary>

*These commands should run BEFORE new code is deployed.*

**(1) Install composer deps:**

```bash
composer install
```

**(2) Install package deps**

```bash
npm ci
```

**(3) Database migrations:**

```bash
php artisan migrate --force
```

**(4) Build Client-side (non-SSR):**

```bash
npm run build
```

**(4) Build (with SSR):**

[Learn more about SSR](https://inertiajs.com/server-side-rendering)

```bash
npm run build-ssr
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
<summary>After release</summary>

*These commands should run AFTER new code is deployed.*

```bash
php artisan config:cache
php artisan route:cache
php artisan queue:restart
```

</details>

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

### Toast Notifications (by PrimeVue)

You can dispatch global toast notifications on the platform using the PrimeVue [Toast Service](https://primevue.org/toast/).

- ✅ Toast Notifications

<details>
<summary>Show example</summary>

```js
import { useToast } from "primevue/usetoast";
const toast = useToast();
// succes 
toast.add({ severity: 'success', summary: 'Success Message', detail: 'Message Content', life: 3000 });
// error
toast.add({ severity: 'error', summary: 'Error Message', detail: 'Message Content', life: 3000 });
```
</details>

---

### Icons

Add svg/imported icons that can change size and color.

- ✅ [Tabler Icons](https://tabler.io/icons) *(recommended)*
