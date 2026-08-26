# AGENTS

Rules for every agent working in this repository. These rules are law; where they conflict with your general
habits, this file wins.

This is a **Laravel 13 + Inertia/Vue 3 starter template** (PHP 8.4, TailwindCSS 4, shadcn-vue, Redis/Horizon,
optional `stancl/tenancy`): a thin HTTP layer over service/action business logic, with a Vue 3 SPA rendered
through Inertia. The *what & why* lives in `docs/BRIEF.md`; the documentation index is `docs/README.md`.
This file defines how you build here.

## Before you work

Load light; pull depth only when the task needs it.

1. **Always read first:** `docs/BRIEF.md` (what & why), `docs/CODEMAP.md` (where things are).
   `docs/README.md` indexes the rest.
2. **On demand, when the task enters an area:** `docs/concepts/` (how a subsystem works and how it
   fails — `deployment-endpoints.md` before touching a deploy), `docs/guides/` (one task each).
3. **How work flows:** `research/` -> `prd-drafts/` -> `prd/`; a `prd/` contract never cites a draft. New
   guaranteed behavior is a `prd/` row backed by a test — cite its `R-<AREA>-<n>` in the code. Follow a doc's
   guide before writing or modifying it, and keep docs true in the same task. Run
   Keep scratch files outside the repository.
4. Read every file before editing it; search before writing new logic — reuse, extend, refactor.
5. When the user raises a concern, investigate before contradicting — evidence, not a hunch.

> **Searching the repo:** the template's changelog + migration guides live in the hidden `.template/`
> directory, which default code search skips. Pass `--hidden`, use `grep -r` / `find`, or read the path
> directly when you need them.

## Hard gates — require explicit approval

- **Migrations / persisted state.** Any change to database schema, user data, or stored state is confirmed first.
- **Dependencies.** Do not add, remove, or version-bump a Composer or pnpm package (or a pinned engine) without approval.
- **Deletions.** Do not delete files outside the task's immediate scope without approval.
- **Commits.** Do not commit or push unless told to.
- **This file.** Never modify `AGENTS.md` without approval; when approved, follow
  raise it with the user rather than working around it.

## Never

- Never touch `.env` or commit credentials. Access env only through `config/` — never call `env()` outside `config/`.
- Never leave debug output (`dd()`, `dump()`, `console.log`), commented-out code, or disabled tests in completed work.
- Never validate on the frontend — Laravel Form Requests are the single source of truth; the frontend only
  displays server-returned errors.
- Never let backend and frontend drift — names, props, enums, and routes match between Laravel and Vue; rename
  one side, rename the other in the same task.
- Never add legacy fallbacks or polyfills unless asked — use the current, modern approach.

## Tech stack

- **Backend:** Laravel 13+ (PHP 8.4+), Redis via Horizon (queue + cache), Inertia server adapter, optional
  `stancl/tenancy` (inert by default).
- **Frontend:** Vue 3 (`<script setup>`), Inertia.js, TailwindCSS 4, shadcn-vue (Radix Vue primitives),
  Lucide icons, Ziggy named routes, vue-sonner (toasts).

## Architecture — the one rule that matters

The HTTP layer is thin; business logic lives in services and actions. Dependencies point **inward**, one-way.

| Layer | Owns | May depend on | Must not |
|---|---|---|---|
| Controllers | Receive a Form Request, delegate, return a response | Services, Actions | Hold business logic, validate inline, authorize inline |
| Form Requests | Validation + authorization (via Policies) | Policies | — |
| Services / Actions | Business processes / single-purpose ops | Models, other services (constructor-injected) | Touch the HTTP layer; use facades/helpers |
| Vue `ui/` -> `app/` -> `site/` | Stateless kit -> auth surfaces -> public pages | inner tiers only | `ui/` may not be app-aware (no Inertia/auth/routes) |

- **Placement:** no app logic -> `ui/`; needs Inertia/auth -> `app/`; public marketing -> `site/`. Search
  the component showcase (`resources/js/pages/admin/components/`) before building a new one — it likely exists.
- **shadcn primitives use the `*Base` suffix**; enhanced `ui/` versions wrap them. `ui/` requires `lang="ts"`
  with typed props. Barrel imports (`index.ts`) are `ui/`-only; `app/` and `site/` use direct file imports.

## Best practices — do / don't

The enforceable conventions of this stack. The rules are here; the worked `✅`/`❌` galleries for both
languages are the appendix at the end of this file — read the rule first, then copy the pattern.

### Backend

- **Do** `declare(strict_types=1)`, return types on every method, constructor injection, modern PHP (enums,
  DTOs, `readonly`), and run **Pint** (PSR-12). **Don't** use facades/helpers inside services, or
  `use function` imports.
- **Do** keep controllers to: Form Request in, Service/Action call, response out — logic in Services
  (processes) or Actions (single-purpose ops). **Don't** put logic, inline validation, or role checks there.
- **Every state-changing route is authorized — no exceptions.** With input, authorize in the Form Request
  via a Policy. Without input (toggle, delete, action), call `$this->authorize()` in the controller against
  the same Policy. **A route under an `admin` prefix is not authorization** — that group carries
  `auth:sanctum` only, so an unauthorized action there is reachable by any logged-in user.
- **Do** keep models lightweight (heavy logic -> Services), avoid N+1 with eager loading, and write
  migrations idempotent and reversible with indexes. **Do** cite the `R-<AREA>-<n>` a method implements in
  its doc-block, so code ↔ contract ↔ test stay linked.

```php
✅ public function store(StoreUserRequest $request): RedirectResponse {
       $this->userService->create($request->validated());
       return redirect()->route('admin.users.index');
   }
❌ public function store(Request $request) {                       // inline validation + logic + auth
       if (! $request->user()->isAdmin()) abort(403);
       $request->validate(['name' => 'required']);
       User::create([...]);
   }
```

### Frontend

- **Do** put `<template>` above `<script setup>`, PascalCase components, 4-space indent, `lang="ts"` only for
  reusable `ui/` components. **`useForm` for anything with fields; `router.<verb>(route(...))` for an
  input-less action.** **Don't** hand-roll refs for fields/errors/processing, or reach for fetch/axios.
- **Do** prefer `computed` over watchers, props-down/events-up (avoid `provide`/`inject` and `defineExpose`),
  and `async`/`await`. **Do** `:key` every `v-for`; never `v-if` + `v-for` on one element. **Don't** use
  inline `:style` (Tailwind classes only) or leave a `console.log`.
- **Do** keep a component's `<script setup>` under ~100 lines and free of data transformation or business
  logic — extract to `composables/` (stateful, `use`-prefixed) or `utils/` (pure). **Don't** let the same
  logic live in 2+ components.
- **Do** `route()` in scripts / `$route()` in templates, Lucide icons, `cursor-pointer` + hover/focus on
  interactive elements, Sonner for toasts. **Don't** hardcode URLs or add another icon library. **Route names
  come from `routes/*.php`** — never hand-edit the generated, git-ignored `resources/js/ziggy.js`; if `route()`
  can't find a route you just added, regenerate (`php artisan ziggy:generate`) or restart `pnpm dev`. See
  [`docs/concepts/ziggy-routes.md`](docs/concepts/ziggy-routes.md).

```vue
✅ <script setup> const form = useForm({ name: '' }); function submit(){ form.post(route('admin.users.store')); } </script>
❌ <script setup> const name = ref(''); const errors = ref({}); /* manual + client validation */ </script>

✅ <Button class="cursor-pointer" @click="router.patch(route('admin.users.suspend', user.id))">Suspend</Button>
❌ <Button @click="router.patch(`/admin/users/${user.id}/suspend`)">Suspend</Button>   <!-- no cursor, hardcoded URL -->
```

## Code documentation

Document the non-obvious — *why* a method exists, its contract, which requirement it satisfies. Trivial
controllers and accessors get nothing; a comment that restates the code is noise.

- **Complex Services/Actions get a PHPDoc block** (intent · contract · edge cases); non-trivial
  composables/utils get TSDoc. Explain *why*, not *what*.
- **Cite the requirement** — a method implementing a `docs/requirements/` requirement names its `R-<NS>-<n>`
  in the doc-block. **Keep it true** — update a stale doc-block in the same change.

```php
✅ /** Provisions a tenant database and seeds its owner (R-TENANT-2). Idempotent: a re-run on a
    *  half-provisioned tenant resumes, never duplicates. @throws ProvisioningException on unreachable central. */
   public function provision(Tenant $tenant): void
❌ // provision the tenant      ← restates the name; teaches nothing
```

## Directory structure

```
app/
├── Console/Commands/  Enums/  Jobs/  Models/  Notifications/  Providers/  Support/
├── Http/{Controllers,Requests,Middleware,Concerns}/   # thin controllers, Form Request validation
├── Services/{Models,<Domain>}/                         # business logic; Actions/ + DataTransferObjects/ on first use
resources/js/
├── components/{ui,app,site}/   pages/   composables/   utils/   tests/
├── pages/                      # Inertia pages by route (Index.vue = public home, admin/ = /admin/*)
routes/{web,api,tenant,channels}.php
database/migrations/            # central; tenant/ = per-tenant when tenancy is enabled
docs/                           # BRIEF, CODEMAP, concepts/, guides/ — see docs/README.md
scripts/                        # release commands — see docs/guides/releasing.md
```

## Build, test & run

```bash
pnpm check         # the gate: check:php + check:js (parallel), then check:build
pnpm check:tenancy # Pest against the tenancy suite (phpunit.tenancy.xml) — when tenancy is enabled
pnpm check:all     # check + check:tenancy
pnpm dev           # local dev server (Herd serves the app)
```

`pnpm check` runs Pint, Larastan (level 5), Pest, ESLint, Stylelint, tsc, Vitest, the release-script
linter, and the client + SSR builds. Auto-fix: `pnpm lint:fix`, `pnpm lint:css:fix`. **Who runs the app:**
build to prove it compiles, then hand off — the **owner runs the UI** and provides screenshots for visual
sign-off. "Compiles + wired" is not "done".

## Optional stacks

- **Multi-tenancy.** `stancl/tenancy` ships installed but **inert** (`TENANCY_ENABLED=false`). While
  disabled, treat tenancy code as nonexistent — don't import `Tenant`/`Domain`, run `tenants:*`, or add
  tenant routes. Usage and the disabled-state contract: `docs/guides/tenancy-using.md`; adopting it on
  a fork with existing data: `docs/guides/tenancy-migrating.md`.
- **Docker / deployment.** An optional Coolify setup lives in `docker/` + the root `Dockerfile`. A fork
  changes only the documented **knobs**; the managed core tracks this template and new Docker capabilities
  originate here, never in a fork. Setup, knobs, drift + versioning policy: `docker/README.md`.

## Documentation duties

Keep docs true in the same task that changes reality. Before creating or editing a page, read its home
`README.md` — and add the index row in the same change as the page, never afterwards.

- Moved/restructured files, or a changed count -> update `docs/CODEMAP.md`. Count artifacts, not lines.
- A page is the source of truth for its subsystem. Never state the same fact in two pages; cite one.
- Hit friction — **anything that cost you a failed attempt**: an env var or flag you had to discover, a guard
  you had to satisfy, a command that only worked the second way -> write it into the page that owns that
  subsystem, under how it fails, the moment you find the workaround. By the end of the task it will feel too
  small to mention, which is exactly how the next agent loses the same hour.
- No requirement contracts ship here. When a fork writes its first, it goes in `docs/requirements/` under
  the closed schema; a requirement ID is never reused or renumbered.
- Scratch stays outside the repository.
- There is no automated documentation check. `docs/` correctness is a review concern.

## Definition of done

1. `pnpm check` passes (Pint, Larastan level 5, Pest, ESLint, Stylelint, tsc, Vitest, the release suites, client + SSR build).
2. Every rule here held — thin controllers, validation server-side only, no backend/frontend drift.
3. New guaranteed behavior is proven by a test, and the page that owns it says so.
4. **Friction you hit is written into the page that owns the subsystem, not only into your reply** — the next
   agent reads the file, not this conversation. Hit none? Say that in your reply, and write nothing: a page
   records traps, never their absence.

---

# Appendix — worked ✅/❌ galleries

The code shapes for the rules in *Best practices* above. Read the rule first, then copy the pattern.

## PHP — backend


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

## Vue — frontend


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

