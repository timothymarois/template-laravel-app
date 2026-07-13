# AGENTS

Rules for every agent working in this repository. These rules are law; where they conflict with your
general habits, this file wins.

This is a **Laravel 13 + Inertia/Vue 3 starter template**: a thin HTTP layer over service/action business
logic, with a Vue 3 SPA rendered through Inertia (TailwindCSS 4, shadcn-vue, Redis/Horizon, optional
`stancl/tenancy`). The *what & why* lives in [`.ai/BRIEF.md`](./.ai/BRIEF.md); the knowledge map is
[`.ai/docs/README.md`](./.ai/docs/README.md). This file defines how you build here.

---

## Before You Work

**Load light by default, then pull depth only when the task reaches for it.**

1. **Always** (keeps context light): read [`.ai/BRIEF.md`](./.ai/BRIEF.md) (what & why) and
   [`.ai/CODEMAP.md`](./.ai/CODEMAP.md) (where things are). Nothing else is required up front.
   [`.ai/docs/README.md`](./.ai/docs/README.md) is the map to everything below.
2. **On demand, when your task enters an area — pull only what you need:** `.ai/docs/lessons/<area>.md`
   (what we learned + the fix — **read before touching the area**); `.ai/docs/guides/<task>.md` (the
   recipe for a recurring procedure); `.ai/docs/PRD/PRD-<System>.md` (the tested contract, if you're
   changing its behavior); `.ai/docs/design/<slug>.md` (if it's proposed, not yet built);
   `.ai/docs/research/` + `.ai/docs/references/` (when designing or generating visuals). Current state is
   read from here — what's in `design/` is in flight, what's in `PRD/` is shipped; there is no status file.
3. Read every file before editing it. Search the codebase before writing new logic — if it exists, reuse,
   extend, or refactor. Never duplicate.
4. When the user raises a concern, investigate before contradicting. Contradict only with evidence — a
   header, a test, a benchmark — never a hunch.

> **Searching the repo:** the template's own changelog + migration guides live in the **hidden
> `.template/`** directory. Default code search (the Grep tool / `ripgrep`) **skips hidden dirs** — pass
> `--hidden`, use `grep -r` / `find`, or read the path directly when you need them.

## Hard Gates — Require Explicit Approval

- **Migrations / persisted state.** Any change to database schema, user data, or stored state is confirmed first.
- **Dependencies.** Do not add, remove, or version-bump a Composer or pnpm package (or a pinned engine/runtime) without approval.
- **Deletions.** Do not delete files or directories outside the task's immediate scope without approval.
- **Commits.** Do not commit or push unless told to.
- **This file.** Never modify `AGENTS.md` without approval. If a rule seems wrong or missing, raise it.

## Never

- Never touch `.env` or commit credentials, tokens, or keys. Access env only through `config/` — never call
  `env()` outside `config/`.
- Never leave debug output (`dd()`, `dump()`, `console.log`), commented-out code, or disabled tests in
  completed work.
- Never validate on the frontend — Laravel Form Requests are the single source of truth; the frontend only
  displays server-returned errors.
- Never let backend and frontend drift — names, props, enums, and routes match between Laravel and Vue at
  all times; rename on one side, rename on the other in the same task.
- Never add legacy fallbacks, polyfills, or backwards-compatible patterns unless the user explicitly asks —
  always use the current, modern approach.

---

## Tech Stack

### Backend

- Laravel 13+ (PHP 8.4+)
- Redis (queue + cache via Horizon)
- Inertia server adapter

### Frontend

- Vue 3 (`<script setup>`)
- Inertia.js Vue adapter
- TailwindCSS 4
- shadcn-vue (Radix Vue primitives)
- Lucide Icons

## Architecture — the one rule that matters

The HTTP layer is thin; business logic lives in services and actions. Dependencies point **inward** and
**one-way**.

| Layer | Owns | May depend on | Must not |
|---|---|---|---|
| Controllers | Receive a Form Request, delegate, return a response | Services, Actions | Hold business logic, validate inline, authorize inline |
| Form Requests | Validation + authorization (via Policies) | Policies | — |
| Services | Business processes | Models, other services (constructor-injected) | Touch the HTTP layer; use facades/helpers |
| Actions | Small, single-purpose operations | Models, services | Touch the HTTP layer |
| Vue `ui/` → `app/` → `site/` | Stateless kit → auth surfaces → public pages | inner tiers only | `ui/` may not be app-aware (no Inertia/auth/routes) |

- **Every file and function does one thing** — no hidden side effects, no buried behavior.
- **shadcn primitives use the `*Base` suffix**; enhanced `ui/` versions wrap them. Deps flow `site`/`app`
  → `ui`, never the reverse.

**Placement rule:** no app logic → `ui/`; needs Inertia/auth → `app/`; public marketing → `site/`. Search
the component showcase (`resources/js/pages/admin/components/`) before building a new one — it likely exists.

## Best Practices — Do / Don't

The real conventions of this stack, written as enforceable rules. Prefer the right/wrong example over prose.

### Backend — General

- All PHP files must use `declare(strict_types=1)`.
- Follow **PSR-12** and run **Laravel Pint** before completing any task.
- Use modern PHP syntax (enums, DTOs, `readonly` properties).
- **All methods must have return types** — no untyped methods.
- Services must use constructor injection — no facades or helpers inside services.
- Never use `use function` imports.

### Backend — Controllers

- **Keep controllers thin** — handle requests and delegate, nothing more.
- Use Form Requests for validation — never validate inline.
- All business logic lives in Services or Actions.
- Form Requests are the **single source of truth** for all validation — the frontend must not duplicate these rules.
- Authorize in Policies (via the Form Request) — **don't** inline role checks in controllers/Blade.

### Backend — Services & Actions

- Domain logic goes in `app/Services`.
- Services handle business processes; Actions handle small, single-purpose operations.
- Services must not touch HTTP-layer concerns.

### Backend — Models & Database

- Keep models lightweight — move heavy logic to Services.
- Avoid N+1 queries; use eager loading.
- Migrations must be idempotent and reversible with proper indexes.

### Backend — Testing

- Use factories for model creation.
- Feature tests for endpoints/workflows, unit tests for Services.
- Full recipe: [`.ai/docs/guides/write-tests.md`](.ai/docs/guides/write-tests.md).

### PHP Examples

**Thin controller with Form Request and DI:**

```php
✅ <?php

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
❌ public function store(Request $request)          // inline validation + logic + auth
   {
       if (! $request->user()->isAdmin()) abort(403);
       $request->validate(['name' => 'required']);
       User::create([...]);
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

### Frontend — Component Standards

- Use `<script setup>` for all components.
- **TypeScript (`lang="ts"`) is only required for reusable `ui/` components** that need strict prop typing.
  Page components, `app/`, and `site/` components can use plain JS.
- Use typed props when using TypeScript; standard `defineProps` is fine otherwise.
- PascalCase for component files and names.
- **Template-first ordering** — `<template>` above `<script setup>` in all Vue files.
- 4-space indentation for all JS/TS/Vue/CSS files.
- **Barrel imports (`index.ts`) are only for `ui/` components.** Import `app/` and `site/` components
  directly from their file path.

### Frontend — Reactivity Rules

- **Prefer `computed` over watchers.** If a watcher's callback sets a ref, use `computed` instead.
- **Avoid `defineExpose`** unless integrating with third-party libraries requiring imperative access.
- **Avoid `provide`/`inject`** for standard data flow — use props down, events up.
- **Use template-based layout wrapping** — do not use `defineOptions` for layout assignment.
- **Use Inertia `useForm` for all form submissions.** Never manually create refs for form fields or error
  state — `useForm` provides reactive state, error handling (`form.errors`), processing state
  (`form.processing`), and dirty tracking out of the box.

### Frontend — Template Rules

- **No inline styles** — use Tailwind classes, never `:style` bindings.
- **`v-for` must always have `:key`** — no exceptions.
- **Never use `v-if` and `v-for` on the same element** — wrap in a `<template v-for>` and put `v-if` on the child.
- **Keep template expressions simple** — if logic is more than a basic condition or property access, move
  it to a `computed` or method.
- **PascalCase for components in templates** — `<UserCard>` not `<user-card>`.

### Frontend — Code Quality

- **No `console.log` in committed code** — remove all debug logging before completing a task.
- **Use `async`/`await` over `.then()` chains.**

### Frontend — Component Responsibility

Components should be **thin and focused on rendering**. If a component has complex logic, it's doing too much.

**Where logic should live:**

| Logic Type               | Location                   | Example                                             |
|--------------------------|----------------------------|-----------------------------------------------------|
| Reusable state/behavior  | `composables/`             | `useSearch()`, `usePagination()`, `useFormErrors()` |
| Pure data transformation | `utils/`                   | `formatCurrency()`, `groupBy()`, `slugify()`        |
| API/data fetching logic  | `composables/`             | `useUsers()`, `useNotifications()`                  |
| One-off component state  | Component `<script setup>` | A local `ref` or `computed`                         |

- **Components should not exceed ~100 lines of script logic.** If a component's `<script setup>` is growing
  large, extract logic into a composable or utility.
- **If the same logic appears in 2+ components, extract it immediately** — into a composable (if
  stateful/reactive) or a utility function (if pure).
- **Components should not contain data transformation, formatting, or business logic.** Move these to
  `utils/` or `composables/`.
- **A component's script should primarily be:** props, emits, a few refs/computed, and event handlers that
  delegate to composables or utils.
- **Composable naming:** always prefix with `use` — `useSearch`, `useFilters`, `usePagination`.

### Frontend — shadcn-vue, Icons, UI, Routing, Notifications

- **shadcn-vue:** components live in `resources/js/components/ui/`. Add new ones via
  `pnpm dlx shadcn-vue@latest add <component>`. Theme variables are in `resources/css/base.css`.
- **Icons:** use Lucide — `import { Settings } from 'lucide-vue-next'`. Don't add another icon lib.
- **UI consistency:** all interactive elements **must have** `cursor-pointer` and proper hover/focus states;
  follow existing component patterns.
- **Routing:** Ziggy named routes — `route('posts.show', id)` in scripts, `$route()` in templates. Never
  hardcode URLs. Use Inertia `router` for navigation, Axios for background API calls.
- **Notifications:** use Sonner — `toast.success('Message')`.

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

**ui/** — Isolated, stateless, props-driven. **Must use `lang="ts"` with typed props.** No Inertia, routes,
auth, or business logic. shadcn primitives use the `*Base` suffix; enhanced versions wrap them.

**app/** — May use Inertia, auth state, route-specific behavior. For authenticated dashboard and management
interfaces. Compose from `ui/` primitives.

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

**Barrel imports (`index.ts`) are only used for `ui/` components.** For `app/` and `site/`, use direct file
imports — no barrel files.

```typescript
// ui/ — barrel imports (multiple primitives in one line)
import { Button, Card, DataTable } from '@/components/ui';

// app/ — direct imports (no barrel)
import AppLayout from '@/components/app/layout/AppLayout.vue';
import Sidebar from '@/components/app/navigation/Sidebar.vue';

// site/ — direct imports (no barrel)
import SiteLayout from '@/components/site/layout/SiteLayout.vue';
```

## Code documentation

Document the non-obvious — *why* a method exists, its contract, and which PRD requirement it satisfies.
Trivial controllers and accessors get nothing; a comment that restates the code is noise.

- **Complex Services/Actions get a PHPDoc block** (intent · contract · edge cases). Non-trivial
  composables/utils get TSDoc. Explain *why*, not *what*.
- **Cite the requirement** — when a method implements a `.ai/docs/PRD/` requirement, name its
  `R-<AREA>-<n>` in the doc-block, linking the code to its tested contract.
- **Keep it true** — a stale doc-block is worse than none; update it in the same change.

```php
✅ /**
    * Provisions a tenant database and seeds its owner (R-TENANT-2).
    * Idempotent: a re-run on a half-provisioned tenant resumes — never duplicates.
    * @throws ProvisioningException when the central connection is unreachable.
    */
   public function provision(Tenant $tenant): void
❌ // provision the tenant      ← restates the name; teaches nothing
```

## Directory Structure

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

`app/Actions/` (single-purpose operations) and `app/DataTransferObjects/` are conventional homes created on
first use — they follow PSR-4, so add them when you write the first one rather than expecting them to
pre-exist. Third-party API clients live under `app/Services/<Domain>/` or `app/Support/`.

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

`.ai/` holds the agent docs — `BRIEF`, `CODEMAP`, `docs/` + `tmp/` (git-ignored scratch). Do not restructure it.

## Build, Test & Run

Run before completing any task. All must pass.

```bash
pnpm check         # The standard gate: check:php + check:js run in parallel, then check:build
pnpm check:php     # PHP only — Pint, Larastan, Pest
pnpm check:js      # JS only — ESLint, Stylelint, tsc (typecheck), Vitest
pnpm check:build   # Client + SSR builds
pnpm check:tenancy # Pest against the tenancy suite (phpunit.tenancy.xml) — run when tenancy is enabled
pnpm check:all     # check + check:tenancy

pnpm dev           # local dev server (Herd serves the app)
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

**Who runs the app:** build to prove it compiles, then hand off — the **owner runs the UI** and provides
screenshots for visual sign-off. Don't self-run the app for visual verification unless explicitly asked.
"Compiles + wired" is not "done".

## Optional multi-tenancy

The template ships `stancl/tenancy` installed but **inert** (`TENANCY_ENABLED=false` by default). While
disabled, treat tenancy code as nonexistent — don't import `Tenant`/`Domain`, run `tenants:*` commands, or
add tenant routes/middleware. The full disabled-state contract, enabling, and usage are in
[`.ai/docs/guides/tenancy-usage.md`](.ai/docs/guides/tenancy-usage.md); adopting tenancy on a fork with
existing user data is in [`.ai/docs/guides/tenancy-migrations.md`](.ai/docs/guides/tenancy-migrations.md).

## Optional Docker / Deployment

An **optional** Coolify deploy setup lives in `docker/` + the root `Dockerfile` and `.dockerignore` (use it
only if you deploy via Docker/Coolify). A fork changes only the documented **knobs**; the managed core
tracks this template, and new Docker capabilities originate here — never in a fork. Setup, knobs, and the
full drift + versioning policy: [`docker/README.md`](docker/README.md).

Operational how-tos live in `.ai/docs/guides/` — [`logging.md`](.ai/docs/guides/logging.md) and
[`health-checks.md`](.ai/docs/guides/health-checks.md).

---

## Documentation Duties

Docs are your responsibility, not the user's — keep them true in the same task that changes reality.

**Before creating or editing any doc, read that home's `README.md` first.** It is the contract for that
home: what belongs there, how to write it, and any ID convention (`R-`, `L-`). Then copy its `TEMPLATE.md`
to start a new doc. Don't write into a home whose rules you haven't read.

- Restructured directories or moved files → update `.ai/CODEMAP.md`.
- Learned something that would have saved you time (a trap, a non-obvious constraint, and the fix) → add it
  to the relevant `.ai/docs/lessons/<area>.md`. Lessons are about *this codebase* only.
- Shipped a system whose behavior is now guaranteed → its `.ai/docs/design/` proposal graduates to a
  `.ai/docs/PRD/`, with every `R-` requirement mapped to a passing Pest test. **Behavior and its PRD change
  in the same commit — they never drift.**
- Implementing a `.ai/docs/PRD/` requirement in code → cite its `R-<AREA>-<n>` in the method's doc-block,
  so code ↔ contract ↔ test stay linked.
- Do not add rationale, history, or maintainer commentary to `.ai/` files — they address the next agent
  doing work, nothing else.
- Need a scratch file — a throwaway draft, a generated asset, experiment output? Put it in `.ai/tmp/`. It's
  git-ignored and stays local. Never keep durable knowledge there; that belongs in a `docs/` home.

## Definition of Done

A task is done when the change is **verified against its stated requirement** — never based on effort — and:

1. The project's checks pass: `pnpm check` (Pint, Larastan level 5, Pest, ESLint, Stylelint, tsc, Vitest,
   client + SSR build).
2. Every rule in this file held — the stack prohibitions, the architecture boundaries, and the doc duties.
   Concretely, verify:
   - Backend and frontend are in sync — props, routes, enums all match.
   - Validation is backend-only — no frontend validation logic; errors displayed from server responses.
   - Forms use Inertia `useForm` — no manual refs for form fields, errors, or processing state.
   - PHP files use `declare(strict_types=1)`, have return types, pass Pint/Larastan; validation uses Form Requests.
   - Controllers are thin; logic is in Services or Actions.
   - Vue components use `<script setup>` (with `lang="ts"` only for `ui/` components).
   - No unnecessary `watch`, `defineExpose`, `provide`/`inject`, or `defineOptions` for layouts.
   - No inline styles, no `console.log`, no `v-if` + `v-for` on the same element.
   - Interactive elements have `cursor-pointer` and hover/focus states.
   - No duplicate code — all new logic checked against the existing codebase.
   - Components are thin — complex logic extracted to `composables/` or `utils/`.
3. If the change guarantees new behavior, a `PRD/` requirement and its Pest test prove it.

**When creating task lists or plans, the final step is always:** _"Re-read `AGENTS.md` and verify Definition of Done."_
