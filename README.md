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
- ✅ Example: Pages, routes and controllers
- ✅ Example: Page titles
- ✅ Example: Shared page layouts
- ✅ Example: Page props (page-level props from controllers) 
- ✅ Example: Shared props (across all pages)
- ❌ Example: loading external component data
- ❌ Example: loading external component data (using auth)
- ❌ Example: sign up form and controller
- ❌ Example: login form
- ❌ Example: Login controller and routes
- ❌ Example: Logout
- ❌ Example: Pagination (with filters)

## Auto-imports

Saves you time by simplifying the import of reusable stores, components and functionality by auto-importing. You can modify the import settings inside your `vite.config.js`.

*Note: Since this will auto-import components based on their name, keep in mind of same name components across the application.*

- ✅ [Auto-imports](https://github.com/unplugin/unplugin-auto-import)
- ✅ [Auto-import Vue Components](https://github.com/unplugin/unplugin-vue-components)

## Client-side state management

Allows you to have client-side state management across your application. This also includes using LocalStorage to save persisted state. 

*Note: For most of the use-cases, you can use page props from Laravel controllers; you should only use state management in rare cases that you need to control the state on the client-side only. A good example of this, saving the user's table columns and sorting.*

- ✅ [Pinia](https://pinia.vuejs.org/) state management 
- ✅ [Pinia persistent state](https://github.com/prazdevs/pinia-plugin-persistedstate)
- ✅ Example: Pinia store (with state, getters and actions)
- ✅ Example: Pinia store (with persist localstorage)
- ❌ Example: Pinia store (shared across pages)

## Performance:

- Controllers are directly impacting the speed of page loads, keep shared and page-level props to a minimum.
- Do not load everything in page-level controllers. Load the minimum required to serve the page and additional (or high impact) items for component specific data loaded after page load.
