# AGENTS

This document defines the standards and contribution rules for all agents (human or AI) working on this project. All rules must be followed — non-compliant contributions will be rejected.

> **Searching the repo:** the template's own changelog + migration guides live in the **hidden `.template/`** directory. Default code search (the Grep tool / `ripgrep`) **skips hidden dirs** — pass `--hidden`, use `grep -r` / `find`, or read the path directly when you need them.

---

## Core Principles

1. **Trust the user, verify before contradicting.** When the user raises a concern, investigate before dismissing. Only contradict with evidence.
2. **Review before changing.** Read files before editing them. Review related docs in `/docs/` and this file before starting work.
3. **Keep backend and frontend in sync.** Naming, props, enums, and routes must match between Laravel and Vue at all times.
4. **Single purpose, no side effects.** Every file and function should do one thing. Don't bury hidden behavior.
5. **No duplicate code.** Search the codebase before writing new logic. Reuse, extend, or refactor — don't duplicate.
6. **Migrations require user approval.** Any change to database schema, user data, or persisted state must be confirmed before proceeding.
7. **No legacy or backwards compatibility.** Always use the current, modern approach. Never add fallbacks, polyfills, or backwards-compatible patterns unless the user explicitly requests it.
8. **Validation is backend-only.** All form/submission validation is handled by Laravel Form Requests. Do not duplicate validation logic on the frontend — display server-returned errors instead.

---

## Tech Stack

### Backend

* Laravel 13+ (PHP 8.4+)
* Redis (queue + cache via Horizon)
* Inertia server adapter

### Frontend

* Vue 3 (`<script setup>`)
* Inertia.js Vue adapter
* TailwindCSS 4
* shadcn-vue (Radix Vue primitives)
* Lucide Icons

---

## Laravel/PHP Conventions

### General

* All PHP files must use `declare(strict_types=1)`.
* Follow **PSR-12** and run **Laravel Pint** before completing any task.
* Use modern PHP syntax (enums, DTOs, readonly properties).
* **All methods must have return types** — no untyped methods.
* Services must use constructor injection — no facades or helpers inside services.
* Never use `use function` imports.

### Controllers

* **Keep controllers thin** — handle requests and delegate, nothing more.
* Use Form Requests for validation — never validate inline.
* All business logic lives in Services or Actions.
* Form Requests are the **single source of truth** for all validation — frontend must not duplicate these rules.

### Services & Actions

* Domain logic goes in `app/Services`.
* Services handle business processes; Actions handle small, single-purpose operations.
* Services must not touch HTTP layer concerns.

### Models & Database

* Keep models lightweight — move heavy logic to Services.
* Avoid N+1 queries; use eager loading.
* Migrations must be idempotent and reversible with proper indexes.

### Testing

* Use factories for model creation.
* Feature tests for endpoints/workflows, unit tests for Services.

### PHP Examples

**Thin controller with Form Request and DI:**

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->create($request->validated());
        return redirect()->route('admin.users.index');
    }
}
```

**Form Request — validation always lives here, never inline:**

```php
<?php

declare(strict_types=1);

namespace App\Http\Requests;

class StoreUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
        ];
    }
}
```

**Service with dependency injection:**

```php
<?php

declare(strict_types=1);

namespace App\Services;

class OrderService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly NotificationService $notifications,
    ) {}

    public function complete(Order $order): void
    {
        $this->gateway->charge($order->total);
        $this->notifications->send($order->user, new OrderCompleted($order));
    }
}
```

**Action — single-purpose operation:**

```php
<?php

declare(strict_types=1);

namespace App\Actions;

class ActivateUser
{
    public function execute(User $user): void
    {
        $user->update(['is_active' => true, 'activated_at' => now()]);
    }
}
```

**DTO with readonly properties:**

```php
<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

final readonly class CreateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone = null,
    ) {}
}
```

**Modern PHP enums:**

```php
<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

**Eager loading:**

```php
$users = User::with(['posts', 'roles'])->paginate(25);
```

**Never use `use function` imports:**

```php
// ❌ Wrong
use function App\Helpers\formatCurrency;

// ✅ Correct — use class imports or call helpers directly
use App\Support\Currency;
```

---

## Vue/Frontend Conventions

### Component Standards

* Use `<script setup>` for all components.
* **TypeScript (`lang="ts"`) is only required for reusable `ui/` components** that need strict prop typing. Page components, `app/`, and `site/` components can use plain JS.
* Use typed props when using TypeScript; standard `defineProps` is fine otherwise.
* PascalCase for component files and names.
* **Template-first ordering** — `<template>` above `<script setup>` in all Vue files.
* 4-space indentation for all JS/TS/Vue/CSS files.
* **Barrel imports (`index.ts`) are only for `ui/` components.** Import `app/` and `site/` components directly from their file path.

### Reactivity Rules

* **Prefer `computed` over watchers.** If a watcher's callback sets a ref, use `computed` instead.
* **Avoid `defineExpose`** unless integrating with third-party libraries requiring imperative access.
* **Avoid `provide`/`inject`** for standard data flow — use props down, events up.
* **Use template-based layout wrapping** — do not use `defineOptions` for layout assignment.
* **Use Inertia `useForm` for all form submissions.** Never manually create refs for form fields or error state — `useForm` provides reactive state, error handling (`form.errors`), processing state (`form.processing`), and dirty tracking out of the box.

### Template Rules

* **No inline styles** — use Tailwind classes, never `:style` bindings.
* **`v-for` must always have `:key`** — no exceptions.
* **Never use `v-if` and `v-for` on the same element** — wrap in a `<template v-for>` and put `v-if` on the child.
* **Keep template expressions simple** — if logic is more than a basic condition or property access, move it to a `computed` or method.
* **PascalCase for components in templates** — `<UserCard>` not `<user-card>`.

### Code Quality Rules

* **No `console.log` in committed code** — remove all debug logging before completing a task.
* **Use `async`/`await` over `.then()` chains.**

### Component Responsibility

Components should be **thin and focused on rendering**. If a component has complex logic, it's doing too much.

**Where logic should live:**

| Logic Type               | Location                   | Example                                             |
|--------------------------|----------------------------|-----------------------------------------------------|
| Reusable state/behavior  | `composables/`             | `useSearch()`, `usePagination()`, `useFormErrors()` |
| Pure data transformation | `utils/`                   | `formatCurrency()`, `groupBy()`, `slugify()`        |
| API/data fetching logic  | `composables/`             | `useUsers()`, `useNotifications()`                  |
| One-off component state  | Component `<script setup>` | A local `ref` or `computed`                         |

**Rules:**
* **Components should not exceed ~100 lines of script logic.** If a component's `<script setup>` is growing large, extract logic into a composable or utility.
* **If the same logic appears in 2+ components, extract it immediately** — into a composable (if stateful/reactive) or a utility function (if pure).
* **Components should not contain data transformation, formatting, or business logic.** Move these to `utils/` or `composables/`.
* **A component's script should primarily be:** props, emits, a few refs/computed, and event handlers that delegate to composables or utils.

**Composable naming:** Always prefix with `use` — `useSearch`, `useFilters`, `usePagination`.

### shadcn-vue

* Components live in `resources/js/components/ui/`.
* Add new components via: `pnpm dlx shadcn-vue@latest add <component>`
* Theme variables are in `resources/css/base.css`.

### Icons

* Use Lucide Icons: `import { Settings } from 'lucide-vue-next'`

### UI Consistency

* All interactive elements **must have** `cursor-pointer` and proper hover/focus states.
* Follow existing component patterns for consistency.

### Routing

* Use Ziggy named routes: `route('posts.show', id)` in scripts, `$route()` in templates.
* Use Inertia `router` for navigation, Axios for background API calls.

### Notifications

* Use Sonner: `toast.success('Message')`

### Vue Examples

**Page component with layout, props, and computed:**

```vue
<template>
    <AppLayout title="Users">
        <div>
            <!-- page content -->
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/components/app/layout/AppLayout.vue';

const props = defineProps({
    users: { type: Array, required: true },
});

const search = ref('');
const filteredUsers = computed(() =>
    props.users.filter(u => u.name.toLowerCase().includes(search.value.toLowerCase()))
);
</script>
```

**Computed over watchers — derive state, don't sync it:**

```vue
<!-- ✅ Correct — computed derives the value -->
<script setup>
import { computed, ref } from 'vue';

const price = ref(100);
const quantity = ref(2);
const total = computed(() => price.value * quantity.value);
</script>

<!-- ❌ Wrong — watcher setting a ref (use computed instead) -->
<script setup>
import { ref, watch } from 'vue';

const price = ref(100);
const quantity = ref(2);
const total = ref(200);

watch([price, quantity], ([p, q]) => {
    total.value = p * q;
});
</script>
```

**Props down, events up — standard component communication:**

```vue
<!-- Parent -->
<template>
    <UserCard :user="user" @delete="handleDelete" @edit="handleEdit" />
</template>

<!-- UserCard.vue -->
<template>
    <div>
        <h3>{{ user.name }}</h3>
        <Button class="cursor-pointer" @click="emit('edit', user.id)">Edit</Button>
        <Button variant="destructive" class="cursor-pointer" @click="emit('delete', user.id)">Delete</Button>
    </div>
</template>

<script setup>
import { Button } from '@/components/ui';

defineProps({
    user: { type: Object, required: true },
});

const emit = defineEmits(['edit', 'delete']);
</script>
```

**Reactive form — submit to backend, display server errors:**

```vue
<template>
    <form @submit.prevent="submit">
        <div>
            <Input v-model="form.name" placeholder="Name" />
            <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
        </div>
        <div>
            <Input v-model="form.email" placeholder="Email" />
            <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
        </div>
        <Button type="submit" class="cursor-pointer" :disabled="form.processing">
            Save
        </Button>
    </form>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { Button, Input } from '@/components/ui';

const form = useForm({
    name: '',
    email: '',
});

function submit() {
    form.post(route('admin.users.store'));
}
</script>
```

```vue
<!-- ❌ Wrong — manually recreating form state and validation -->
<script setup>
import { ref } from 'vue';

const name = ref('');
const email = ref('');
const errors = ref({});
const processing = ref(false);

function submit() {
    processing.value = true;
    errors.value = {};
    if (!name.value) errors.value.name = 'Name is required';
    if (!email.value) errors.value.email = 'Email is required';
    if (Object.keys(errors.value).length) {
        processing.value = false;
        return;
    }
    // ...manual axios call, manual error handling, etc.
}
</script>
```

**Ziggy named routes (scripts vs templates):**

```vue
<template>
    <Link :href="$route('admin.users.show', user.id)" class="cursor-pointer hover:underline">
        {{ user.name }}
    </Link>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

function navigateToUser(id) {
    router.visit(route('admin.users.show', id));
}
</script>
```

**Inertia for navigation, Axios for background calls:**

```vue
<script setup>
import { router } from '@inertiajs/vue3';
import axios from 'axios';

// Page navigation — use Inertia
function goToUser(id) {
    router.visit(route('admin.users.show', id));
}

// Background action (no page change) — use Axios
async function toggleFavorite(id) {
    await axios.post(route('api.users.favorite', id));
}
</script>
```

**Conditional rendering and list iteration:**

```vue
<!-- ✅ Correct — v-if and v-for on separate elements -->
<template>
    <div v-if="users.length">
        <template v-for="user in users" :key="user.id">
            <div v-if="user.isActive">
                {{ user.name }}
            </div>
        </template>
    </div>
    <p v-else>No users found.</p>
</template>

<!-- ❌ Wrong — v-if and v-for on the same element -->
<template>
    <div v-for="user in users" v-if="user.isActive" :key="user.id">
        {{ user.name }}
    </div>
</template>
```

**Keep template expressions simple:**

```vue
<!-- ✅ Correct — complex logic in computed -->
<template>
    <span>{{ formattedAddress }}</span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    user: { type: Object, required: true },
});

const formattedAddress = computed(() =>
    [props.user.street, props.user.city, props.user.state].filter(Boolean).join(', ')
);
</script>

<!-- ❌ Wrong — complex logic inline in template -->
<template>
    <span>{{ [user.street, user.city, user.state].filter(Boolean).join(', ') }}</span>
</template>
```

**Notifications with Sonner:**

```vue
<script setup>
import { toast } from 'vue-sonner';

function save() {
    toast.success('User saved successfully');
}
</script>
```

**Extract logic into composables — keep components thin:**

```js
// ✅ Correct — composables/useSearch.js
import { computed, ref } from 'vue';

export function useSearch(items, key = 'name') {
    const search = ref('');
    const filtered = computed(() =>
        items.value.filter(item =>
            item[key].toLowerCase().includes(search.value.toLowerCase())
        )
    );
    return { search, filtered };
}
```

```vue
<!-- ✅ Correct — component delegates to composable -->
<template>
    <Input v-model="search" placeholder="Search..." />
    <div v-for="user in filtered" :key="user.id">{{ user.name }}</div>
</template>

<script setup>
import { useSearch } from '@/composables/useSearch';
import { Input } from '@/components/ui';

const props = defineProps({
    users: { type: Array, required: true },
});

const { search, filtered } = useSearch(() => props.users);
</script>
```

```vue
<!-- ❌ Wrong — search, filtering, sorting, formatting all crammed in the component -->
<script setup>
import { computed, ref } from 'vue';

const props = defineProps({ users: { type: Array, required: true } });

const search = ref('');
const sortField = ref('name');
const sortDir = ref('asc');

const filtered = computed(() =>
    props.users.filter(u => u.name.toLowerCase().includes(search.value.toLowerCase()))
);
const sorted = computed(() =>
    [...filtered.value].sort((a, b) =>
        sortDir.value === 'asc'
            ? a[sortField.value].localeCompare(b[sortField.value])
            : b[sortField.value].localeCompare(a[sortField.value])
    )
);
const formatted = computed(() =>
    sorted.value.map(u => ({
        ...u,
        fullName: `${u.firstName} ${u.lastName}`,
        joinedDate: new Date(u.created_at).toLocaleDateString(),
    }))
);
// ...more and more logic piling up
</script>
```

**Extract pure transformations into utils:**

```js
// ✅ Correct — utils/format.js
export function formatDate(date) {
    return new Date(date).toLocaleDateString();
}

export function fullName(user) {
    return `${user.firstName} ${user.lastName}`;
}
```

```vue
<!-- ✅ Correct — component imports utils, stays focused on rendering -->
<template>
    <span>{{ fullName(user) }} — joined {{ formatDate(user.created_at) }}</span>
</template>

<script setup>
import { formatDate, fullName } from '@/utils/format';

defineProps({
    user: { type: Object, required: true },
});
</script>
```

---

## File & Directory Structure

### Laravel
```
app/
├── Console/Commands/   # Artisan commands
├── Enums/              # PHP backed enums
├── Http/
│   ├── Concerns/       # Reusable controller traits
│   ├── Controllers/    # Thin controllers
│   ├── Middleware/     # HTTP middleware
│   └── Requests/       # Form Request validation
├── Jobs/               # Queued jobs
├── Models/             # Eloquent models
├── Notifications/      # Mail / notification classes
├── Providers/          # Service providers
├── Services/           # Business logic
│   ├── Models/         # Per-model services (extend ModelService)
│   └── <Domain>/       # Feature services grouped by domain
└── Support/            # Small helpers, traits, utilities
```

`app/Actions/` (single-purpose operations) and `app/DataTransferObjects/` are conventional homes created on first use — they follow PSR-4, so add them when you write the first one rather than expecting them to pre-exist. Third-party API clients live under `app/Services/<Domain>/` or `app/Support/`.

### Vue
```
resources/js/
├── components/
│   ├── ui/             # Base components (shadcn + custom enhanced)
│   ├── app/            # Application components (authenticated app)
│   └── site/           # Website components (public marketing pages)
├── pages/              # Inertia pages (organized by route)
│   ├── Index.vue       # / (public home)
│   └── admin/          # /admin/* routes
├── composables/        # Reusable stateful logic (useSearch, usePagination, etc.)
├── tests/              # Vitest unit tests
└── utils/              # Pure helper functions (formatDate, slugify, etc.)
```

---

## Component Architecture

### Component Showcase (REQUIRED REFERENCE)

Before implementing any UI component, review the showcase at `resources/js/pages/admin/components/`:
- `forms/` — Input, Select, Checkbox, Switch, TagsInput, etc.
- `actions/` — Button, Dialog, Sheet, Menu, Command
- `display/` — Card, Badge, Alert, Avatar, Tabs, Toast
- `data/` — Table, Pagination, Actions
- `charts/` — Area, Bar, Line, Pie charts

### Layers

| Layer     | Location           | Purpose                             | Rules                                            |
|-----------|--------------------|-------------------------------------|--------------------------------------------------|
| **ui/**   | `components/ui/`   | Base components (shadcn + enhanced) | [README](resources/js/components/ui/README.md)   |
| **app/**  | `components/app/`  | Application components              | [README](resources/js/components/app/README.md)  |
| **site/** | `components/site/` | Website components                  | [README](resources/js/components/site/README.md) |

**ui/** — Isolated, stateless, props-driven. **Must use `lang="ts"` with typed props.** No Inertia, routes, auth, or business logic. shadcn primitives use `*Base` suffix; enhanced versions wrap them.

**app/** — May use Inertia, auth state, route-specific behavior. For authenticated dashboard and management interfaces. Compose from `ui/` primitives.

**site/** — May use Inertia and routes. For public-facing marketing pages. Compose from `ui/` primitives.

### Decision Tree

```
Need a component?
├─ 1. SEARCH FIRST: Check ui/, app/, site/ — USE IT if it exists
├─ 2. CHECK SHADCN: pnpm dlx shadcn-vue@latest add <component>
├─ 3. DETERMINE LOCATION:
│     ├─ Reusable, no app logic → ui/
│     ├─ Uses Inertia/auth → app/
│     └─ Public website → site/
└─ 4. COMPOSE from existing ui/ primitives
```

### Import Patterns

**Barrel imports (`index.ts`) are only used for `ui/` components.** For `app/` and `site/`, use direct file imports — no barrel files.

```typescript
// ui/ — barrel imports (multiple primitives in one line)
import { Button, Card, DataTable } from '@/components/ui';

// app/ — direct imports (no barrel)
import AppLayout from '@/components/app/layout/AppLayout.vue';
import Sidebar from '@/components/app/navigation/Sidebar.vue';

// site/ — direct imports (no barrel)
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
```

---

## Optional multi-tenancy

The template ships `stancl/tenancy` installed but **inert** (`TENANCY_ENABLED=false` by default). While disabled, treat tenancy code as nonexistent — don't import `Tenant`/`Domain`, run `tenants:*` commands, or add tenant routes/middleware. The full disabled-state contract, enabling, and usage are in [`docs/guidelines/tenancy-using.md`](docs/guidelines/tenancy-using.md); adopting tenancy on a fork with existing user data is in [`docs/guidelines/tenancy-migrating.md`](docs/guidelines/tenancy-migrating.md).

---

## Optional Docker / Deployment

An **optional** Coolify deploy setup lives in `docker/` + the root `Dockerfile` and `.dockerignore` (use it only if you deploy via Docker/Coolify). A fork changes only the documented **knobs**; the managed core tracks this template, and new Docker capabilities originate here — never in a fork. Setup, knobs, and the full drift + versioning policy: [`docker/README.md`](docker/README.md).

---

## Required Checks

Run before completing any task. All must pass.

```bash
pnpm check         # The standard gate: check:php + check:js + check:docs, then check:build
pnpm check:php     # PHP only — Pint, Larastan, Pest
pnpm check:js      # JS only — ESLint, Stylelint, tsc (typecheck), Vitest
pnpm check:docs    # docs/ lint (ESLint + Stylelint)
pnpm check:build   # Client + SSR + docs builds
pnpm check:tenancy # Pest against the tenancy suite (phpunit.tenancy.xml) — run when tenancy is enabled
pnpm check:all     # check + check:tenancy
```

| Layer      | Tool         | Requirement                                |
|------------|--------------|--------------------------------------------|
| PHP        | Laravel Pint | Must pass                                  |
| PHP        | Larastan     | Level 5 minimum                            |
| PHP        | Pest         | Tests required for new features            |
| JS/Vue     | ESLint       | Must pass                                  |
| CSS        | Stylelint    | Must pass                                  |
| JS/Vue     | Vitest       | Tests required for new features            |
| TypeScript | tsc          | Must compile without errors (for TS files) |

Auto-fix: `pnpm lint:fix` (ESLint), `pnpm lint:css:fix` (Stylelint).

---

## Post-Task Review

Before marking any task complete, verify:

- [ ] All core principles (above) were followed
- [ ] Backend and frontend are in sync — props, routes, enums all match
- [ ] Validation is backend-only — no frontend validation logic, errors displayed from server responses
- [ ] Forms use Inertia `useForm` — no manual refs for form fields, errors, or processing state
- [ ] PHP files use `declare(strict_types=1)` and pass Pint/Larastan
- [ ] Controllers are thin; logic is in Services or Actions
- [ ] All PHP methods have return types; validation uses Form Requests
- [ ] Vue components use `<script setup>` (with `lang="ts"` only for `ui/` components)
- [ ] No unnecessary `watch`, `defineExpose`, `provide`/`inject`, or `defineOptions` for layouts
- [ ] No inline styles, no `console.log`, no `v-if` + `v-for` on the same element
- [ ] Interactive elements have `cursor-pointer` and hover/focus states
- [ ] No duplicate code — all new logic checked against existing codebase
- [ ] Components are thin — complex logic extracted to `composables/` or `utils/`
- [ ] `pnpm check` passes

**When creating task lists or plans, the final step must always be:** _"Re-read `AGENTS.md` and perform the post-task review."_

---

All agents must follow this document, the referenced guides, all PRDs, and the README. Non-compliant contributions will be rejected.
