# Security Best Practices

## Mass Assignment Protection

Every model that accepts mass-assigned input needs a deliberate `$fillable` whitelist or `$guarded`
policy. Read-only models do not need ornamental declarations.

Incorrect:
```php
class User extends Model
{
    protected $guarded = []; // All fields are mass assignable
}
```

Correct:
```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
```

Never use `$guarded = []` on models that accept user input.

## Authorize Every Action

Use policies or gates in controllers. Never skip authorization.

Incorrect:
```php
public function update(UpdatePostRequest $request, Post $post)
{
    $post->update($request->validated());
}
```

Correct:
```php
public function update(UpdatePostRequest $request, Post $post)
{
    Gate::authorize('update', $post);

    $post->update($request->validated());
}
```

Or via Form Request:

```php
public function authorize(): bool
{
    return $this->user()->can('update', $this->route('post'));
}
```

A policy `before()` method must return `null` to continue to the named ability. Laravel does not call
`before()` when the policy has no method matching the checked ability, so do not use it to hide a
missing or misspelled policy method. Use `denyAsNotFound()` when confirming existence would leak
privileged information.

## Prevent SQL Injection

Always use parameter binding. Never interpolate user input into queries.

Incorrect:
```php
DB::select("SELECT * FROM users WHERE name = '{$request->name}'");
```

Correct:
```php
User::where('name', $request->name)->get();

// Raw expressions with bindings
User::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->get();
```

## Escape Output to Prevent XSS

Use `{{ }}` for HTML escaping. Only use `{!! !!}` for trusted, pre-sanitized content.

Incorrect:
```blade
{!! $user->bio !!}
```

Correct:
```blade
{{ $user->bio }}
```

## CSRF Protection

Include `@csrf` in all POST/PUT/PATCH/DELETE Blade forms. Inertia doesn't use `@csrf`; its HTTP client sends the `XSRF-TOKEN` cookie back as the `X-XSRF-TOKEN` header, which Laravel accepts in place of the `_token` field.

Incorrect:
```blade
<form method="POST" action="/posts">
    <input type="text" name="title">
</form>
```

Correct:
```blade
<form method="POST" action="/posts">
    @csrf
    <input type="text" name="title">
</form>
```

## Rate Limit Auth and API Routes

Apply `throttle` middleware to abuse-sensitive authentication and API routes. The values below are
an example, not a universal threshold.

```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

Route::post('/login', LoginController::class)->middleware('throttle:login');
```

## Validate File Uploads

Validate MIME type and size. Both `mimes` and `mimetypes` read the file's contents to guess its MIME type; `mimes` just expresses the allow-list as extensions. The `extensions` rule checks only the client-supplied filename, so never rely on it alone. Never trust client-provided filenames.

```php
public function rules(): array
{
    return [
        'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
    ];
}
```

Store with generated filenames:

```php
$path = $request->file('avatar')->store('avatars', 'public');
```

## Keep Secrets Out of Code

Never commit `.env`. Access secrets via `config()` only.

Incorrect:
```php
$key = env('API_KEY');
```

Correct:
```php
// config/services.php
'api_key' => env('API_KEY'),

// In application code
$key = config('services.api_key');
```

## Audit Dependencies

Run `composer audit` periodically to check for known vulnerabilities in dependencies. Automate this in CI to catch issues before deployment.

```bash
composer audit
```

## Encrypt Sensitive Database Fields

Use `encrypted` cast for API keys/tokens and mark the attribute as `hidden`.

Incorrect:
```php
class Integration extends Model
{
    protected function casts(): array
    {
        return [
            'api_key' => 'string',
        ];
    }
}
```

Correct:
```php
class Integration extends Model
{
    protected $hidden = ['api_key', 'api_secret'];

    protected function casts(): array
    {
        return [
            'api_key' => 'encrypted',
            'api_secret' => 'encrypted',
        ];
    }
}
```

Encryption, serialization hiding, and authorization solve different failures. Encryption protects
the stored value; `$hidden` prevents accidental array/JSON output; authorization controls who may
retrieve the decrypted value. Apply every boundary the data requires, and use a text-capable column
because encrypted value length is not predictable.

## Treat SVG and Active Content as Executable Input

Laravel's `image` validation rejects SVG by default because it may contain scriptable content. Do
not enable SVG only to satisfy an upload request.

Incorrect:

```php
'logo' => ['required', File::image(allowSvg: true)],
```

Correct — either keep SVG disabled or add an explicit sanitizer and safe delivery policy:

```php
'logo' => ['required', File::types(['png', 'jpg', 'webp'])->max('2mb')],
```

## Derive Rate Limits From the Boundary

Use named limiters for login and public-write routes, but do not paste one universal threshold.
Choose the key, response, and rate from abuse risk, legitimate traffic, proxy configuration, and
product behavior.

```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by(Str::lower($request->string('email')).'|'.$request->ip());
});
```
