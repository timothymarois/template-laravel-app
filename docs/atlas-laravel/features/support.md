# Support Helpers

Atlas Laravel ships with small utility classes under the `Atlas\Laravel\Support` namespace.

## PhoneNumber

Format and normalize US phone numbers. `PhoneNumber::format` returns `null` when the value can't be normalized.

```php
use Atlas\Laravel\Support\PhoneNumber;

PhoneNumber::format('1234567890'); // (123) 456-7890
PhoneNumber::normalize('+1 (123) 456-7890'); // 1234567890
PhoneNumber::format('123'); // null
```

## Caster

Cast simple data arrays using type definitions.

```php
use Atlas\Laravel\Support\Caster;

$data = ['count' => '1', 'active' => '1'];
$casts = ['count' => 'int', 'active' => 'bool'];

$cast = Caster::cast($data, $casts);
// ['count' => 1, 'active' => true]
```

Boolean casts recognize the strings `"true"`, `"false"`, `"1"`, `"0"`, `"yes"`, and `"no"` before falling back to PHP's default boolean casting.

These helpers are designed to be lightweight and framework agnostic, making them easy to reuse across projects.
