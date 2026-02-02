# Laravel Guidelines

PHP and Laravel coding standards for the application.

## General PHP Standards

- All PHP files must use `declare(strict_types=1);`
- Follow **PSR-12** and run **Laravel Pint** before committing
- Use modern PHP syntax (enums, DTOs, readonly properties where appropriate)
- Avoid helper-based architecture; favor dependency-injected services
- Never use `use function` imports

## Controllers

Keep controllers thin. They should not contain business logic or data transformation beyond request handling.

```php
// Good: Controller delegates to Service
class UserController extends Controller
{
    public function __construct(
        protected ResponseFactory $inertia,
        protected UserService $userService,
    ) {}

    public function store(CreateUserRequest $request): Response
    {
        $user = $this->userService->create($request->validated());

        return $this->inertia->render('admin/users/Show', [
            'user' => $user,
        ]);
    }
}
```

**Rules:**
- Delegate all work to Services or Actions
- Use Form Requests for validation
- Return Inertia responses for page rendering

## Services & Actions

Place domain logic inside `app/Services`.

```php
class UserService extends ModelService
{
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
```

**Rules:**
- Services handle business processes
- Actions handle small, single-purpose operations
- Services should not directly touch HTTP layer concerns
- Prefer constructor injection for all dependencies

## Models & Database Layer

Keep models lightweight. Move heavy logic to Services.

```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
```

**Rules:**
- Use Query Builder or dedicated Service methods for complex queries
- Avoid N+1 queries; use eager loading
- Keep business logic in Services, not Models

## Migrations & Data

```php
public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();

        $table->index('email');
    });
}
```

**Rules:**
- Migrations must be idempotent and reversible
- Use proper indexes for frequently queried fields
- Avoid storing computed/transient data in the database

## Form Requests

Use Form Requests for validation:

```php
class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }
}
```

## Directory Structure

```
app/
├── Console/Commands/   # Artisan commands
├── Enums/              # PHP backed enums
├── Http/
│   ├── Concerns/       # Reusable controller traits
│   ├── Controllers/    # Thin controllers
│   └── Requests/       # Form Request validation
├── Models/             # Eloquent models
├── Integrations/       # Third-party APIs, external services
├── Services/           # Business logic
│   ├── Models/         # Per-model services (extend ModelService)
│   └── <Domain>/       # Feature services grouped by domain
└── Support/            # Small helpers, traits, utilities
```

## Code Quality

| Tool | Requirement |
|------|-------------|
| Laravel Pint | Must pass |
| Larastan | Level 5 minimum |
| Pest | Tests required for new features |

Run checks before committing:

```bash
pnpm check:php    # Pint + Larastan + Pest
```
