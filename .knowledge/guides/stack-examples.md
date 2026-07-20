# Stack examples — worked ✅/❌ patterns

The full code galleries for this stack, pulled out of `AGENTS.md` so the rulebook stays scannable.
The **rules** live in `AGENTS.md` (Best Practices); this is where you look when you are about to write
the code and want the shape. Read the rule first, then copy the pattern.

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

