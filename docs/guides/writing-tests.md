# Guide: Write and run tests

**When to use:** You added or changed a feature and need the tests it requires, or you want to run the suites before committing.
**Prerequisites:** Deps installed (`composer install`, `pnpm install`). PHP tests use Pest (`tests/`); JS/Vue tests use Vitest (`resources/js/tests/`).

## Decide first

What the change owes:

| Change | Required test |
|--------|----------------|
| New endpoint | Feature test for HTTP behavior |
| New Service | Unit test for business logic |
| New component | Component test for rendering + interaction |
| New composable | Unit test for reactive behavior |
| Bug fix | Regression test that fails before the fix, passes after |

Quality bars enforced alongside tests: Larastan **level 5** minimum, TypeScript must compile without errors, Pint must pass, ESLint + Stylelint must pass.

## Steps

### 1. Run the suites

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

### 2. Write a PHP test — Pest

Feature tests (HTTP endpoints and workflows) live in `tests/Feature/`:

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

Unit tests (services and isolated logic) live in `tests/Unit/`:

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

Build model state with factories (`User::factory()->has(Post::factory()->count(3))->create()`), never
with hand-written inserts.

### 3. Write a JS test — Vitest

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

## Verify

```bash
pnpm check                       # PHP suite, JS suite and the SSR build all green
git stash && ./vendor/bin/pest --filter='<your new test>'   # a regression test fails without the fix
git stash pop
```

## Pitfalls

| Symptom | Fix |
|---|---|
| A test passes whether or not the fix is present | It asserts the implementation, not the behavior. Revert the fix and watch it fail before you trust it. |
| Tests pass alone but fail in the suite | Shared or order-dependent state. Use factories and per-test setup; never rely on a prior test's rows. |
| A flaky test gets re-run until green | That is a defect in the test or the code. Fix the cause; a retry hides it. |
| A test breaks on a harmless refactor | It reaches into internals. Assert rendered output, emitted events and stored state. |
| A test needs production data to pass | Use factories. Production data is not reproducible and does not belong in the suite. |
