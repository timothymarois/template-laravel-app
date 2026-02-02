# Testing Guidelines

Testing requirements and best practices for the application.

## Overview

The application uses two testing frameworks:

| Framework | Stack | Location |
|-----------|-------|----------|
| [Pest](https://pestphp.com/) | PHP/Laravel | `tests/` |
| [Vitest](https://vitest.dev/) | JavaScript/Vue | `resources/js/tests/` |

## Running Tests

```bash
# All tests
pnpm check           # PHP + JS + SSR build

# PHP tests only
./vendor/bin/pest    # or: composer test
pnpm check:php       # Pint + Larastan + Pest

# JavaScript tests only
pnpm test            # Run once
pnpm test:watch      # Watch mode
```

## PHP Testing with Pest

### Feature Tests

Test HTTP endpoints and workflows:

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

### Unit Tests

Test Services and isolated logic:

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

### Factories

Use factories for model creation:

```php
test('user can have many posts', function () {
    $user = User::factory()
        ->has(Post::factory()->count(3))
        ->create();

    expect($user->posts)->toHaveCount(3);
});
```

### Test Structure

```
tests/
├── Feature/           # HTTP and workflow tests
│   ├── Auth/          # Authentication tests
│   └── Admin/         # Admin feature tests
├── Unit/              # Isolated unit tests
│   └── Services/      # Service tests
└── TestCase.php       # Base test case
```

## JavaScript Testing with Vitest

### Component Tests

Test Vue components in isolation:

```typescript
import { mount } from '@vue/test-utils'
import { describe, it, expect } from 'vitest'
import Button from '@/components/ui/Button.vue'

describe('Button', () => {
    it('renders with text', () => {
        const wrapper = mount(Button, {
            slots: {
                default: 'Click me',
            },
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

### Composable Tests

Test Vue composables:

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

### Test Structure

```
resources/js/tests/
├── components/        # Component tests
│   └── ui/            # UI component tests
├── composables/       # Composable tests
└── utils/             # Utility tests
```

## Test Requirements

### New Features

All new features require tests:

| Change | Required Tests |
|--------|----------------|
| New endpoint | Feature test for HTTP behavior |
| New Service | Unit test for business logic |
| New component | Component test for rendering and interaction |
| New composable | Unit test for reactive behavior |

### Bug Fixes

Bug fixes should include a regression test that:
1. Fails before the fix
2. Passes after the fix

## Code Quality Standards

| Layer | Tool | Requirement |
|-------|------|-------------|
| PHP | Pest | Tests required for new features |
| JS/Vue | Vitest | Tests required for new features |
| PHP | Larastan | Level 5 minimum |
| JS/Vue | TypeScript | Must compile without errors |

## Best Practices

### Do

- Write tests that describe behavior, not implementation
- Use descriptive test names
- Test edge cases and error conditions
- Keep tests focused and independent
- Use factories for test data

### Don't

- Don't test framework internals
- Don't write tests that depend on execution order
- Don't use production data in tests
- Don't ignore flaky tests — fix them
