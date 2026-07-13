# Guide: Write and run tests

**When to use:** You added or changed a feature and need the tests it requires, or you want to run the suites before committing.
**Prerequisites:** Deps installed (`composer install`, `pnpm install`). PHP tests use Pest (`tests/`); JS/Vue tests use Vitest (`resources/js/tests/`).

## What requires a test

| Change | Required test |
|--------|----------------|
| New endpoint | Feature test for HTTP behavior |
| New Service | Unit test for business logic |
| New component | Component test for rendering + interaction |
| New composable | Unit test for reactive behavior |
| Bug fix | Regression test that fails before the fix, passes after |

Quality bars enforced alongside tests: Larastan **level 5** minimum, TypeScript must compile without errors, Pint must pass, ESLint + Stylelint must pass.

## Steps

### Run the suites

```bash
pnpm check           # everything: PHP + JS + SSR build (run before committing)

# PHP only
./vendor/bin/pest    # or: composer test
pnpm check:php       # Pint + Larastan + Pest

# JS only
pnpm test            # run once
pnpm test:watch      # watch mode
pnpm check:js        # ESLint + Stylelint + Vitest + build
```

### PHP — Pest

Feature test (HTTP endpoints and workflows) live in `tests/Feature/`:

```php
test('users can be created', function () {
    $response = $this->post('/admin/users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/admin/users');
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});
```

Unit test (Services and isolated logic) live in `tests/Unit/`:

```php
test('user service creates user with hashed password', function () {
    $service = app(UserService::class);

    $user = $service->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
    ]);

    expect($user->password)->not->toBe('password');
    expect(Hash::check('password', $user->password))->toBeTrue();
});
```

Use factories for model creation:

```php
test('user can have many posts', function () {
    $user = User::factory()
        ->has(Post::factory()->count(3))
        ->create();

    expect($user->posts)->toHaveCount(3);
});
```

PHP test layout:

```
tests/
├── Feature/           # HTTP and workflow tests
│   ├── Auth/          # Authentication tests
│   └── Admin/         # Admin feature tests
├── Unit/              # Isolated unit tests
│   └── Services/      # Service tests
└── TestCase.php       # Base test case
```

### JavaScript — Vitest

Component test (Vue components in isolation):

```typescript
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import Button from '@/components/ui/Button.vue'

describe('Button', () => {
    it('renders with text', () => {
        const wrapper = mount(Button, {
            slots: { default: 'Click me' },
        })

        expect(wrapper.text()).toContain('Click me')
    })

    it('emits click event', async () => {
        const wrapper = mount(Button)

        await wrapper.trigger('click')

        expect(wrapper.emitted('click')).toBeTruthy()
    })
})
```

Composable test:

```typescript
import { describe, it, expect } from 'vitest'
import { useCounter } from '@/composables/useCounter'

describe('useCounter', () => {
    it('increments count', () => {
        const { count, increment } = useCounter()

        expect(count.value).toBe(0)
        increment()
        expect(count.value).toBe(1)
    })
})
```

JS test layout:

```
resources/js/tests/
├── components/        # Component tests
│   └── ui/            # UI component tests
├── composables/       # Composable tests
└── utils/             # Utility tests
```

## Verify

- `pnpm check` exits green — PHP suite, JS suite, and the SSR build all pass.
- A new bug-fix test fails on the pre-fix code and passes on the fixed code.

## Pitfalls

- Don't test framework internals.
- Don't write tests that depend on execution order — keep them focused and independent.
- Don't use production data in tests; use factories.
- Don't ignore flaky tests — fix them.
- Write tests that describe behavior, not implementation, with descriptive names covering edge cases and error conditions.
