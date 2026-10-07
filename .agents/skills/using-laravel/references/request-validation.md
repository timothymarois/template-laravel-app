# Validation & Forms Best Practices

## Use Form Request Classes

Extract validation from controllers into dedicated Form Request classes.

Do this when rules, authorization, or reuse would distract from the controller. A tiny one-off
validation call may remain inline when that is the application's established pattern.

Incorrect:
```php
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|max:255',
        'body' => 'required',
    ]);
}
```

Correct:
```php
public function store(StorePostRequest $request)
{
    Post::create($request->validated());
}
```

## Array vs. String Notation for Rules

Array syntax is more readable and composes cleanly with `Rule::` objects. Prefer it in new code, but check existing Form Requests first and match whatever notation the project already uses.

```php
// Preferred for new code
'email' => ['required', 'email', Rule::unique('users')],

// Follow existing convention if the project uses string notation
'email' => 'required|email|unique:users',
```

## Always Use `validated()`

Get only validated data. Never use `$request->all()` for mass operations.

Incorrect:
```php
Post::create($request->all());
```

Correct:
```php
Post::create($request->validated());
```

## Use `Rule::when()` for Conditional Validation

```php
'company_name' => [
    Rule::when($this->account_type === 'business', ['required', 'string', 'max:255']),
],
```

## Use the `after()` Method for Custom Validation

Use `after()` instead of `withValidator()` for custom validation logic that depends on multiple fields.

```php
public function after(): array
{
    return [
        function (Validator $validator) {
            if ($this->quantity > Product::find($this->product_id)?->stock) {
                $validator->errors()->add('quantity', 'Not enough stock.');
            }
        },
    ];
}
```

## Constrain Validated Array Keys

Validating that a value is an array does not by itself exclude unexpected nested keys from returned
validated data.

Incorrect:

```php
'user' => ['required', 'array'],
'user.name' => ['required', 'string'],
```

Correct:

```php
'user' => ['required', 'array:name,username'],
'user.name' => ['required', 'string'],
'user.username' => ['required', 'string'],
```

## Never Pass Request Input to `unique()->ignore()`

Incorrect — Laravel documents user-controlled ignored values as an SQL-injection risk:

```php
Rule::unique('users')->ignore($request->input('id'));
```

Correct — pass the route-resolved model or its trusted system key:

```php
Rule::unique('users')->ignore($user);
```

Validate uploads with size and MIME/content rules, not only their user-assigned extension. Read
[Security](security.md) before allowing SVG or other active formats.
