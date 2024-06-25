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

### Tech Stack

- ✅ Laravel 11
- ✅ Vuejs 3
- ✅ Inertia (Laravel + Vue)
- ✅ Tailwind 3
- ✅ PrimeVue 3 (unstyled)

---

### Docker

Docker is used for your local env. *Do not use your custom or OS php/env*.

Run: `./vendor/bin/sail up`, once containers are built, you can also run them within VSC using the [docker extension](https://marketplace.visualstudio.com/items?itemName=ms-azuretools.vscode-docker) or if you perfer the [docker desktop](https://www.docker.com/products/docker-desktop/) app.

- ✅ Docker with php 8.3
- ✅ Docker with mysql 8
- ✅ Docker with redis
- ✅ Docker with supervisor (for job queue workers)

---

### Tests and Linting

- ✅ Phpunit `php artisan test`
- ❌ [Larastan](https://github.com/larastan/larastan)
- ❌ [Laravel Pint](https://laravel.com/docs/11.x/pint)

---

### Monitoring

- ✅ [Log Viewer](https://github.com/opcodesio/log-viewer) `/log-viewer`
- ✅ [Laravel Horizon](https://laravel.com/docs/11.x/horizon) `/horizon`
- ❌ [Laravel Telescope](https://laravel.com/docs/11.x/horizon) `/telescope`

---

### Authentication

For user authentication with Laravel and Vuejs we will use Laravel Sanctum. 

- ✅ [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum) *(local session auth)*
- ❌ [Laravel Passport](https://laravel.com/docs/11.x/passport) *(oauth and third parties)*
- ❌ OAUTH: Google SSO
- ✅ Security: CSRF-token protected (XSRF)
- ✅ Security: Rate-limit login protected
- ✅ Example: Register form and controller
- ✅ Example: Login form and controller
- ✅ Example: Logout
- ❌ Example: Middleware auth route and auth-only page
- ❌ Example: Login redirect back location
- ❌ Example: Email verification
- ❌ Example: Forgot/reset password form and controller

---

### Page props (by Inertia)

Page level props are passed by controller data. You can also pass shared props here since Inertia will merge them into one. Keep in mind that page data and shared data will need to have unique key names to avoid being replaced.

*Note: The layout and all components on the page can access these props.*

- ✅ Return Inertia view or JSON response based Content-Type
- ✅ Example: [Page props](https://inertiajs.com/pages) (page-level props from controllers) 
- ✅ Example: [Shared props](https://inertiajs.com/shared-data) (across all pages)

<details>
<summary>Show example</summary><br>

*In controller, return only inertia view* 
```php
return Inertia::render('Example/Index', ['posts' => $posts->paginate()]);
```

*In controller, return inertia view or json*

Note: For json response, you should use `axois` requests.

```php
return response()->inertiaOrJson('Posts/Index', ['posts' => $posts->paginate()]);
```

*In page component for Inertia views*

```js
const props = defineProps({
    'posts': Object
});
```

</details>

---

### Page title (by Inertia)

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

### Page layouts

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

### Routes (by Ziggy)

The ability to grab the routes in Vue based on the laravel route names. You can control what routes are visible to end-users by modifying `/config/ziggy.php`.

- ✅ [Named routes](https://github.com/tighten/ziggy)
- ✅ Example: Route name to path

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

### Form requests (by Inertia)

You should use the built-in `useForm` method, it will handle all the states of submission.

- ✅ [Form helper](https://inertiajs.com/forms#form-helper)

<details>
<summary>Show example</summary><br>

Define the form:

```js
const form = useForm({
    email: null,
    password: null
})
```

To submit the form and with the named routes:

```js
form.post(route('post.store'))
```

Additional reactive form attributes:

```js
// identify if the form values have changed
form.isDirty

// get the reported errors from the submission (based on field name)
form.errors.email

// if the form is currenting being processed
form.processing
```

</details>

---

### Router requests (by Inertia & Axois)

You can use the built-in methods to fetch data or make manual visits

- ✅ [Router requests](https://inertiajs.com/manual-visits)
- ✅ [Axois requests](https://axios-http.com/docs/example)

<details>
<summary>Show example</summary>

Use Inertia built-in `router` to visit routes and modify browser histoty.

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

### Icons

Easily add svg icons that can change size and color.

- ✅ [PrimeVue Icons](https://primevue.org/icons/)
- ✅ Search thousands of icons at [Iconify](https://icon-sets.iconify.design/)

<details>
<summary>Show example</summary>

*From PrimeVue icons*

```html
<i class="pi pi-check"></i>
```

*SVG icon example*

```html
<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"><path fill="currentColor" d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3m-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3m0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5m8 0c-.29 0-.62.02-.97.05c1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5"/></svg>
```

</details>

---

### Auto-imports

Saves you time by simplifying the import of reusable stores, components and functionality by auto-importing. You can modify the import settings inside your `vite.config.js`.

*Note: Auto-import components supports directory name prefix for same name components.*

- ✅ [Auto-imports](https://github.com/unplugin/unplugin-auto-import)
- ✅ [Auto-import Vue Components](https://github.com/unplugin/unplugin-vue-components)
- ✅ Includes the auto-import of Vue & Inertia methods.
- ✅ Includes the auto-import of Layouts, Components, Stores and Composables.
- ✅ Includes the auto-import of [PrimeVue components](https://primevue.org/autocomplete/)

---

### Client-side state management

Allows you to have client-side state management across your application. This also includes using LocalStorage to save persisted state. 

*Note: For most of the use-cases, you can use page props from Laravel controllers; you should only use state management in rare cases that you need to control the state on the client-side only. A good example of this, saving the user's table columns and sorting.*

- ✅ [Pinia](https://pinia.vuejs.org/) state management 
- ✅ [Pinia persistent state](https://github.com/prazdevs/pinia-plugin-persistedstate)
- ✅ Example: Pinia store (with state, getters and actions)
- ✅ Example: Pinia store (with persist localstorage)

---

### Utilities

You can create global utility functions for the client-side at `/resources/js/utils` and they will be auto-imported into your components. 

*Note: Currently, you can not use these methods in `<template>` but you can use them in your component script. If you really need to use a method in your template, you can add them within the `app.js` and prefix it with a `$` to keep consistent.*

- ✅ Auto-load utility methods

Built-in methods:

- ✅ `transformNumber(n, decimals = 2)` - Transform a string/number into a comma seperated number.
- ✅ `formatCurrency(n, currency = 'USD', invalid = 'Invalid')` - Formats a string or number into a currency.
- ✅ `formatIntoSlug(str)` - Creates a safe slug for URL from string.
- ✅ `formatIntoURL(str)` - Formats a string and ensures its a URL (has https://)

---

## Performance:

- Controllers will directly impact the speed of page loads, keep shared and page-level props to a minimum.
- Do not load everything in page-level controllers. Load the minimum required to serve the page and additional data in components after the page is loaded or mounted.

---

### TODO:

- ❌ Example: loading external component data (JSON??)
- ❌ Example: public API
- ❌ Example: Pagination (with filters)
