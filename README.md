# Laravel Vue Inertia Template

This starter kit is designed for any project, providing a quick playground that gets you up and running within 5 minutes of setup. The goal is to offer an ideal starting point, eliminating the need to reinvent the wheel or re-implement foundational systems for each project. It addresses all common use cases, **allowing you to focus solely on your application requirements**.

> Inertia empowers you to build a modern, JavaScript-based single-page application without the tiresome complexity.

---

## Tech Stack

#### Core

This template uses **Laravel** (PHP) as your backend and **Vuejs** (JavaScript) as your frontend. Inertia is implemented so that both can communicate seamlessly. Inertia can save you a monumental amount of time when trying to handle client-side state management.

- ✅ Laravel 11 – [Documentation](https://laravel.com/docs/11.x)
- ✅ Vuejs 3 – [Documentation](https://vuejs.org/guide/introduction.html)
- ✅ Inertia – [Documentation](https://inertiajs.com/) | [Why Inertia?](https://inertiajs.com/who-is-it-for)
- ✅ MySQL 8 – Database can be changed to any other engine. Since MySQL is the most common and practical for most use-cases, it is included by default.

#### Design

For the design side, we want to focus on customization, theming, and utility components. PrimeVue offers the most comprehensive set of utility components that are fully customizable without bloating. Tree-shaking makes sure you only import the components you actually use keeping your overall project light-weight. 

- ✅ Tailwind 3 – [Documentation](https://tailwindcss.com/docs/installation)
- ✅ PrimeVue 4 – [Documentation](https://primevue.org/) | [Unstyled with Tailwind](https://tailwind.primevue.org/)

---

## Tests and Linting

Pre-installed code-linting and automated test services to help keep your CI pipeline protected from lower code quality and breaking changes.

Linting is used on both PHP and JS side to keep all collaborators using the same format.

- ✅ Phpunit - `php artisan test`
- ✅ [Larastan](https://github.com/larastan/larastan) - `./vendor/bin/phpstan analyse`
- ✅ [Laravel Pint](https://laravel.com/docs/11.x/pint) - `./vendor/bin/pint`
- ✅ [Vue ESLint](https://eslint.vuejs.org/) - `npm run eslint -- --fix` (or with `--quiet`)

---

## Monitoring

Built-in monitoring packages allow you to view logs, worker jobs, and debug effortlessly in real-time.

- ✅ [Log Viewer](https://github.com/opcodesio/log-viewer) - `/log-viewer`
- ✅ [Laravel Horizon](https://laravel.com/docs/11.x/horizon) - `/horizon`
- ✅ [Laravel Telescope](https://laravel.com/docs/11.x/horizon) - `/telescope` works on local with `TELESCOPE_ENABLED=true` in `.env`
- ✅ [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) - `APP_DEBUG=true`
- ✅ [Vue Devtools](https://chromewebstore.google.com/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd) - Chrome Extension

**External Services:**

- ✅ [Sentry.io](https://sentry.io/) | [Docs](https://docs.sentry.io/platforms/php/guides/laravel/) - It's recommended to use a service to collect and notify you of ongoing errors. Sentry is a great tool since it connects directly with Jira and the suspecting commits that caused breaking changes.
- ❌ [Codecov.io](https://codecov.io/) - It's recommended to use a code coverage service to monitor your automate test coverage across your application's business logic. 

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

**(4) Build Client-side:**

```bash
npm run build
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

### Authentication

For user authentication with Laravel and Vuejs we will use Laravel Sanctum. 

This list is to keep track of the examples that are pre-installed and ready out of the box.

- ✅ [Laravel Sanctum](https://laravel.com/docs/11.x/sanctum) *(local session auth)*
- ❌ [Laravel Passport](https://laravel.com/docs/11.x/passport) *(oauth and third parties)*
- ❌ OAUTH: Google SSO
- ✅ Security: CSRF-token protected (XSRF)
- ✅ Security: Rate-limit login protected
- ✅ Example: Register form and controller
- ✅ Example: Login form and controller
- ✅ Example: Logout
- ✅ Example: Middleware auth route and auth-only page
- ✅ Example: Redirect back to page after login
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

*In controller, redirect or json*

If you want to use as an API, you can return the JSON, or if you want to redirect the user's page with inertia.

```php
return response()->redirectOrJson('route.name', ['posts' => $posts->paginate()]);
```

*In page component for Inertia views*

```js
const props = defineProps({
    'posts': Object
});
```

*In non-page components*

```js
const page = usePage()
const posts = computed(() => page.props?.posts)
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
- ✅ Example: page forms and submissions
- ✅ Example: modal forms and submissions

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

- ✅ Example: Success Toast Notification

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

*Note: For most of the use-cases, you can use page props from Laravel controllers (with Inertia); you should only use state management in rare cases that you need to control the state on the client-side only. A good example of this, saving the user's table columns and sorting.*

- ✅ [Pinia](https://pinia.vuejs.org/) state management 
- ✅ [Pinia persistent state](https://github.com/prazdevs/pinia-plugin-persistedstate)
- ✅ Example: Pinia store (with state, getters and actions)
- ✅ Example: Pinia store (with persist localstorage)

---

### Utilities

You can create global utility functions for the client-side at `/resources/js/utils` and they will be auto-imported into your components. 

*Note: Currently, you can not use these methods in `<template>` but you can use them in your component script. If you really need to use a method in your template, you can add them within the `app.js` and prefix it with a `$` to keep consistent.*

- ✅ Auto-load utility methods

---

### PrimeVue Presets:

Modifies the lara preset as "lara-mod". You can create or use the default presets as they are included.

Currently modified components:

- Button
- Menu
- MenuBar
- Drawer

## Architectural Reasoning:

With over 15 years of development experience, I have built various web-based platforms, from static websites and management systems to custom CRMs and e-commerce websites. This template aims to address many of the challenges in creating and maintaining long-term projects.

**Challenges of Separate UI and API Projects**

Creating a separate API and UI project was once considered the best approach and remains popular today. However, after numerous projects, it has become clear that this separation introduces unnecessary complexities for building web-based applications at scale. These complexities in devops, deployments, local environments, team organization and even your data source of truth are the reasons why you should not split your UI and API.

1. **Source of Truth:** 
    - **Issue:** Splitting the UI and API encourages maintaining two sources of truth. Data is stored and manipulated both on the server and client-side state management.
    - **Impact:** This dual maintenance increases complexity and the risk of inconsistencies, leading to potential data integrity issues.

2. **DevOps and Deployments:**
    - **Issue:** Managing deployments becomes more complicated as the application scales. Database migrations, dependency installs, and update sequences need to be synchronized between the UI and API.
    - **Impact:** Ensuring synchronized deployments is challenging, especially when one part fails. This requires sophisticated orchestration tools like Kubernetes, adding unnecessary complexity.

3. **Team Organization:**
    - **Issue:** Splitting the UI and API can hinder collaboration and flexibility. Full-stack developers need to navigate two separate codebases, complicating changes and feature implementation.
    - **Impact:** This split can introduce authentication challenges and security holes that could have been avoided with a unified project structure.

**Laravel and PHP: A Mature and Stable Choice**

JavaScript frameworks, though powerful, are still in their early stages and lack the maturity of Laravel and PHP. For instance, maintaining a Laravel project from over 5 years ago is feasible due to its stability and backward compatibility. In contrast, JavaScript projects often become impractical to maintain due to rapid obsolescence and lack of testable code focus.

**Minimizing JavaScript's Control Over the Server**

By leveraging Laravel for the backend and Vue.js for the frontend, using Inertia.js to bridge the gap, we minimize JavaScript's control over the server. This approach benefits from Laravel's stability, testability, and maintainability while keeping the frontend modern and dynamic.

In short, the frontend's job should only be to visually display the data, keeping business logic and manipulation to a minimum.

**Benefits of a Unified Approach**

- **Consistency:** 
    - A single source of truth for data reduces complexity and potential errors.
- **Simplified Deployments:** 
    - Coordinated deployments for the entire application, reducing the risk of synchronization issues.
- **Enhanced Collaboration:** 
    - Full-stack developers can seamlessly work on both frontend and backend, improving efficiency and code quality.
- **Security:** 
    - Unified projects reduce the attack surface by eliminating separate authentication mechanisms and potential security holes.

**Conclusion**

This template leverages the strengths of Laravel and Vue.js, using Inertia.js to seamlessly bridge the gap between them. This approach allows you to focus on your application requirements without reinventing the wheel, ensuring a stable, maintainable, and enjoyable development experience. As the development landscape evolves, this architecture remains aligned with modern trends, ensuring long-term viability and scalability.

### Performance and Concerns

With this approach, there are also some concerns to discuss:

1. Controllers will directly impact the speed of page loads, keep shared and page-level props to a minimum or at least cache common ones that don't need to run database queries with each page view. 
2. Do not load everything in page-level controllers. Load the minimum required to serve the page and additional data in components after the page is loaded or mounted.
3. What if you need an API response instead of inertia view without duplicating controllers? This has been handled with different responses based on content type. You can [request JSON](https://github.com/timothymarois/template-laravel-app/blob/main/app/Providers/InertiaServiceProvider.php) and it will return the properties as an API normally would. You can also create specific API routes as you normally do.
4. What if you need just a static website without a database or API results? You can use this template, however, if you truely just need a static (server-less website) I would recommended using [Nuxt.js](https://nuxt.com/) as it handles server-less websites effortlessly. A good example would be a company or personal portfolio website. Keep in mind, while Nuxt can handle many use-cases, once you need that database or API, you should try to avoid the complexities of splitting out your UI and API.
