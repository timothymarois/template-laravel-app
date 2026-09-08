# AGENTS

Rules for every agent working in this repository.

## Before you work

Load light; pull depth only when the task needs it.

1. **Read the docs first.** `docs/BRIEF.md` (what & why) and `docs/CODEMAP.md` (where things are),
   always; then the page for the area you enter — `docs/concepts/` (how a subsystem works and how it
   fails) or `docs/guides/` (one task each). `docs/README.md` indexes the rest.
2. **Load the skill for every area you touch, before you edit it.** A change spanning a controller,
   its Vue page and its migration is three skills. Reload as the work moves into a surface you haven't
   covered — mid-task, not just at the start. **Editing an area whose skill you never loaded is a
   failed task**, however green the checks. A skill changes *how* you work; it never widens scope or
   overrides this file.
3. **Read a file before editing it; search before writing new logic** — reuse or extend what is already
   here rather than duplicating it. Scratch files stay outside the repository.
4. **Make the smallest change that does the job.** Touch nothing adjacent to it, and never refactor,
   rename, or reformat what the task did not send you to. Improvements are **mentioned, not made**, and
   scope never widens unless directed — if the smallest correct change is a large one, say why first.
5. **Do not contradict the user without evidence.** Investigate, then answer with what you found — not a
   hunch, and not agreement you have not checked.

## Hard rules

The first five need explicit approval. The rest are not negotiable.

- **Persisted state — ask first.** Any change to schema, stored data, or migrations.
- **Dependencies — ask first.** Adding, removing, or version-bumping a Composer or pnpm package, or a
  pinned engine.
- **Deletions — ask first.** Any file outside the task's immediate scope.
- **Commits — ask first.** Never commit or push unless told to.
- **This file — ask first.** `CLAUDE.md` is a byte-identical copy; change both in the same commit.
  `pnpm check` fails when they drift, so a change to one is unfinished until the other matches.
- **Never touch `.env` or commit credentials.** Read env only through `config/` — never `env()` outside it.
- **Never ship debug output**, commented-out code, or disabled tests.
- **Never validate on the frontend.** Form Requests are the single source of truth; the frontend renders
  the errors the server returns.
- **Never let backend and frontend drift.** Names, props, enums, and routes match — rename both sides in
  the same task.
- **Never add a legacy fallback or polyfill** unless asked.

## Stack & architecture

- **Backend:** Laravel 13+ (PHP 8.4+), Redis via Horizon (queue + cache), Inertia server adapter,
  Sanctum (sessions + API tokens).
- **Frontend:** Vue 3 (`<script setup>`), Inertia.js, TailwindCSS 4, shadcn-vue (Radix Vue primitives),
  Lucide icons, Ziggy named routes, vue-sonner (toasts).

**The one rule that matters:** the HTTP layer is thin, business logic lives in services and actions, and
dependencies point **inward**, one-way.

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

## Directory structure

```
app/
├── Http/
│   ├── Controllers/    Thin — Form Request in, service call, response out
│   └── Requests/       Form Requests: validation + authorization
├── Services/           Business logic — Actions/ + DataTransferObjects/ on first use
├── Models/             Eloquent models, kept light
├── Jobs/               Queued work
├── Health/             /health checks + notification channel
└── Console/ · Enums/ · Notifications/ · Providers/ · Support/ · Http/{Concerns,Middleware}/

resources/js/
├── components/ui/      Stateless kit — no Inertia, auth or routes; lang="ts"; barrel imports
├── components/app/     Authenticated surfaces
├── components/site/    Public marketing pages
├── pages/              Inertia pages by route (Index.vue = home, admin/ = /admin/*)
├── composables/        Stateful logic, use-prefixed
└── utils/ · plugins/ · tests/ (Vitest)

routes/                 web · api · channels · components
database/               migrations/ · factories/ · seeders/
tests/                  Feature/ · Unit/ · scripts/ (shell contract tests)
docs/                   BRIEF · CODEMAP · concepts/ · guides/ — see docs/README.md
scripts/                Release commands — see docs/guides/releasing.md
docker/                 config/ + deploy/ = managed core · project/ = yours, never template-managed
.template/              Changelog + migration guides (hidden; pass --hidden to search)
```

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
✅ /** Issues an API key for the user (R-APIKEY-1). The plaintext is returned once and never stored;
    *  only its hash persists. @throws AbilityNotAllowedException when an ability is outside the enum. */
   public function issue(User $user, string $name, array $abilities): IssuedApiKey
❌ // issue an api key      ← restates the name; teaches nothing
```

## Build, test & run

```bash
pnpm check         # the gate: check:php + check:js + check:release + check:deploy, then check:build
pnpm dev           # local dev server (Herd serves the app)
```

`pnpm check` runs Pint, Larastan (level 5), Pest, ESLint, Stylelint, tsc, Vitest, the release and
deployment contract scripts, and the client + SSR builds. `check:deploy` asserts the production image's own
configuration — the application suite runs under a different ini and never executes the deploy scripts, so
nothing else checks what the container ships. Auto-fix: `pnpm lint:fix`, `pnpm lint:css:fix`.

**Who runs the app:** build to prove it compiles, then hand off — the **owner runs the UI** and provides
screenshots for visual sign-off. "Compiles + wired" is not "done".

## Optional stacks

- **Multi-tenancy.** Not shipped. The template is single-tenant; a fork that needs many workspaces adds
  `stancl/tenancy` itself, following `docs/guides/adding-tenancy.md`. Do not assume tenancy exists, and do
  not add tenant-aware code to this template.
- **Docker / deployment.** An optional Coolify setup lives in `docker/` + the root `Dockerfile`. A fork
  changes only the documented **knobs**; the managed core tracks this template and new Docker capabilities
  originate here, never in a fork. Setup, knobs, drift + versioning policy: `docker/README.md`.

## Documentation duties

Keep docs true in the same task that changes reality. Read a page's home `README.md` before adding to it,
and add the index row in the same change as the page.

- **One page owns a fact.** Cite it from elsewhere; never restate it.
- **Moved files, or a changed count** -> `docs/CODEMAP.md`. Count artifacts, not lines.
- **Friction goes in the page that owns the subsystem, under how it fails, the moment you find the
  workaround** — an env var you had to discover, a guard you had to satisfy, a command that only worked the
  second way. By the end of the task it feels too small to mention, which is how the next agent loses the
  same hour.
- **Requirement contracts** live in `docs/requirements/` under the closed schema; an ID is never reused or
  renumbered. None ship here.
- Nothing lints `docs/`. Correctness is a review concern.

## Delegation and review

Delegate to keep this context clear — not to avoid thinking. If you were delegated this task, execute
it; don't sub-delegate.

- **Delegate** a large, self-contained subtask — a broad search, an independent slice, a review pass.
  Brief it with the rules, the task, write access, and what done looks like. Keep the decisions and
  the distilled result here, never the raw dumps. Match the model to the work.
- **Don't delegate** skill selection, routine reading, or anything finished in a couple of steps.
- **Review scales with risk.** Mechanical or docs-only: none. Contained code: one reviewer. Shared,
  structural, security- or data-touching: several in parallel — correctness, duplication and test
  coverage are separate lenses. Reviewers are read-only and advisory; verify a finding against the
  code before acting on it, and say what you rejected.

## Definition of done

1. `pnpm check` passes (Pint, Larastan level 5, Pest, ESLint, Stylelint, tsc, Vitest, the release and
   deployment suites, client + SSR build).
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
