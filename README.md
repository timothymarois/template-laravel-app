# Laravel App Template

This is a starter kit for any project and it can be used as a quick playground to get you from 0 to 100 in 5 minutes.

## Setup

Install client-side:

```bash
npm install
```

Install server-side: (should do this through the docker container)

```bash
composer install
```

## Build

For local testing:

```bash
npm run dev
```

```bash
npm run build
```

## Features:

- ✅ Laravel 11
- ✅ Vuejs 3
- ✅ Tailwind 3
- ✅ PrimeVue 3 (unstyled)
- ✅ Inertia (Laravel + Vue)
---
- Run docker: `./vendor/bin/sail up` and then you can use the container in VSC or docker desktop
- ✅ Docker with php 8.3
- ✅ Docker with mysql 8
- ✅ Docker with redis
- ✅ Docker with supervisor (for job queue workers)
---
- ✅ Phpunit `php artisan test`
- ❌ Larastan
- ❌ Laravel Pint
---
- ❌ Example: loading external component data
- ❌ Example: loading external component data (using auth)
- ❌ Example: sign up form and controller
- ❌ Example: login form
- ❌ Example: Login controller and routes
- ❌ Example: Logout
- ❌ Example: Pagination (with filters)

---

## Page props (by Inertia)

Page level props are passed by controller data. You can also pass shared props here since Inertia will merge them into one. Keep in mind that page data and shared data will need to have unique key names to avoid being replaced.

*Note: The layout and all components on the page can access these props.*

- ✅ Example: Pages, routes and controllers
- ✅ Example: [Page props](https://inertiajs.com/pages) (page-level props from controllers) 
- ✅ Example: [Shared props](https://inertiajs.com/shared-data) (across all pages)

<details>
<summary>Show example</summary>

*In controller:* 
```php
return Inertia::render('Example/Index',[
    'example' => 'Example Prop 1'
]);
```
*In page component:*
```js
const props = defineProps({
    'example': String
});
```

</details>

---

## Page title (by Inertia)

Dynamically change the page title based on the page or layout.

- ✅ Example: Page titles

<details>
<summary>Show example</summary>

```vue
<!-- include the inertia Head component on the page -->
<Head title="Home Page" />
```
</details>

---

## Page layouts

The ability to create universal layouts for specific pages.

- ✅ Page layouts
- ✅ Example: Shared page layouts

<details>
<summary>Show example</summary>

*Define your layout name in props or omit it for default layout.*

```js
const props = defineProps({
    'layout': 'Example'
});
```
</details>

---

## Routes (by Ziggy)

The ability to grab the routes in Vue based on the laravel route names. You can control what routes are visible to end-users by modifying `/config/ziggy.php`.

- ✅ [Named routes](https://github.com/tighten/ziggy)
- ✅ Example: Route name to path

<details>
<summary>Show example</summary>

```js
const path = route('login')
// http://localhost/api/login
```
</details>

---

## Auto-imports

Saves you time by simplifying the import of reusable stores, components and functionality by auto-importing. You can modify the import settings inside your `vite.config.js`.

*Note: Auto-import components supports directory name prefix for same name components.*

- ✅ [Auto-imports](https://github.com/unplugin/unplugin-auto-import)
- ✅ [Auto-import Vue Components](https://github.com/unplugin/unplugin-vue-components)
- ✅ Includes the auto-import of Vue & Inertia methods.
- ✅ Includes the auto-import of Layouts, Components, Stores and Composables.
- ✅ Includes the auto-import of [PrimeVue components](https://primevue.org/autocomplete/)

---

## Client-side state management

Allows you to have client-side state management across your application. This also includes using LocalStorage to save persisted state. 

*Note: For most of the use-cases, you can use page props from Laravel controllers; you should only use state management in rare cases that you need to control the state on the client-side only. A good example of this, saving the user's table columns and sorting.*

- ✅ [Pinia](https://pinia.vuejs.org/) state management 
- ✅ [Pinia persistent state](https://github.com/prazdevs/pinia-plugin-persistedstate)
- ✅ Example: Pinia store (with state, getters and actions)
- ✅ Example: Pinia store (with persist localstorage)
- ✅ Example: Pinia store (shared across pages)

---

## Performance:

- Controllers will directly impact the speed of page loads, keep shared and page-level props to a minimum.
- Do not load everything in page-level controllers. Load the minimum required to serve the page and additional data in components after the page is loaded or mounted.
