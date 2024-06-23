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
- ✅ Example: Page props (data from controller) 
- ✅ Example: Shared props (like user data)
- ❌ Example: loading external component data
- ❌ Example: loading external component data (using auth)
- ❌ Example: sign up form and controller
- ❌ Example: login form
- ❌ Example: Login controller and routes
- ❌ Example: Logout
---

### Client State Management

**Note:** You only should use pinia state management if you need to save additional items that the client-side creates (unrelated to the server). A good example of this; saving the user's table columns and sorting. You can also use the persisted state option to save data in localstorage.

- ✅ Pinia state management 
- ✅ Pinia persistent state (local storage)
- ❌ Example: Pinia store usage

## Performance:

- Controllers are directly impacting the speed of page loads, keep shared and page-level props to a minimum.
- Do not load everything in page-level controllers. Load the minimum required to serve the page and additional (or high impact) items for component specific data loaded after page load.
