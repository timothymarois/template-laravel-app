+++
title = "Accounts"
subtitle = "registration, sign-in, roles, deactivation and the password rule"
status = "approved"
intent = """
Accounts exist so that a person registers, signs in and signs out with nothing beyond an email address and
a password, and so that an operator takes an account out of service in one act. A deactivated account
should be refused everywhere at once, and a failed sign-in should never reveal whether the address exists.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Roles", value = "admin, user", cite = "roles" },
  { label = "Sign-in page", value = "/login", cite = "login" },
]

[[infobox]]
group = "Limits"
rows = [
  { label = "Sign-in attempts", value = "5", note = "per address and connection, then a minute's lockout", cite = "lockout" },
  { label = "Registrations", value = "5 a minute", note = "per connection, shared with password reset", cite = "throttle" },
  { label = "Password length", value = "8 characters", note = "more is required in production", cite = "password" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Deactivated account", value = "refused at sign-in and on its next request", cite = "deactivated" },
  { label = "Admin area", value = "admin role only", cite = "roles" },
]
+++

An **account** is an email address, a name and a password, and it carries one of two roles.[^model] Signing
in gives the browser a session; presenting an API key instead is described on [API keys](api-keys.md),
and replacing a forgotten password on [Password reset](accounts/password-reset.md).

## Registration

A person registers on `/register` with a name, an email address, a password and its confirmation, and is
signed in at once.[^register] The address must not belong to another account, and is kept in lower
case.[^register-rules] A password is at least 8 characters long; in production it also needs a letter in
each case, a number and a symbol, and a reset is held to the same rule.[^password] Registration allows 5
attempts a minute from one connection, a budget shared with the password-reset forms, and the sixth is
answered `Too Many Attempts.`[^throttle] A new account gets the `user` role.[^default-role]

## Sign-in

`/login` asks for `Email address` and `Password`.[^login] A wrong pair is answered
`These credentials do not match our records.`, and the fifth wrong attempt for one address from one
connection locks that pair out for a minute with
`Too many login attempts. Please try again in :seconds seconds.`, the seconds filled in.[^lockout] The
password has no minimum length at sign-in, so a password set before the rule still works.[^login-rules]
`Logout` ends the browser session and returns the reader to the home page.[^logout] While an account is
signed in, the server records when it was last seen, from which address and browser, at most once every 5
minutes.[^seen]

## Roles

An account is an `admin` or a `user`, shown as `Administrator` and `User`.[^roles] An administrator reaches
the [admin area](admin.md) and manages every account; a user reaches nothing under `/admin`.[^roles] A
new account is a `user`, and the first administrator is created from the console, as
[user:create](commands/user-create.md) describes, or by the seed, which creates `admin@example.com` as an
administrator.[^default-role]

## Deactivation

A deactivated account is refused at sign-in with `Your account has been deactivated.`, and a session that
is deactivated while signed in is ended on its next request and sent to the sign-in page with the same
message.[^deactivated] An API key the account owns is refused with the same message.[^apikey-inactive]
An operator switches an account off through the `is_active` flag on the account itself.{missing}

[^model]: `app/Models/User.php` — `$fillable` and `casts()` hold `name`, `email`, `password`, `is_active`
    and `role`, cast to `UserRole`.
[^register]: `routes/web.php` — `GET register` and `POST auth/register` in the `guest` group;
    `app/Http/Controllers/Auth/RegisterController.php` — `store()` creates the user, logs them in,
    regenerates the session and redirects to `/`; `resources/js/pages/Register.vue` — the four fields and
    the `Create account` button.
[^register-rules]: `app/Http/Requests/Auth/RegisterRequest.php` — `rules()`: `name` required, `email`
    `lowercase` and `unique` among users, `password` `confirmed`.
[^password]: `app/Providers/AppServiceProvider.php` — `configurePasswordRules()` sets `Password::min(8)`
    and adds `letters()`, `mixedCase()`, `numbers()` and `symbols()` in production;
    `app/Http/Requests/Auth/ResetPasswordRequest.php` — `rules()` uses `Password::defaults()`.
[^throttle]: `routes/web.php` — `POST auth/register`, `POST auth/forgot-password` and
    `POST auth/reset-password` carry `throttle:auth`; `app/Providers/AppServiceProvider.php` —
    `configureRateLimiting()` gives `auth` `Limit::perMinute(5)` keyed by IP;
    `vendor/laravel/framework/src/Illuminate/Routing/Middleware/ThrottleRequests.php` —
    `buildException()` raises `Too Many Attempts.`.
[^default-role]: `app/Enums/UserRole.php` — `default()` returns `User`;
    `database/seeders/DatabaseSeeder.php` — `run()` creates `admin@example.com` with `SuperAdmin`.
[^login]: `routes/web.php` — `GET login` and `POST auth/login`;
    `app/Http/Controllers/Auth/SessionController.php` — `authenticate()` regenerates the session and
    redirects to the intended page or `/`; `resources/js/pages/Login.vue` — the two fields and the
    `Login` button.
[^lockout]: `app/Http/Requests/Auth/LoginRequest.php` — `authenticate()` answers `auth.failed` and counts
    the attempt; `ensureIsNotRateLimited()` refuses after 5 attempts on `throttleKey()`, the address plus
    the IP, with `auth.throttle`; `lang/en/auth.php` — the two messages;
    `vendor/laravel/framework/src/Illuminate/Cache/RateLimiter.php` — `hit()` decays after 60 seconds.
[^login-rules]: `app/Http/Requests/Auth/LoginRequest.php` — `rules()` gives `password` no minimum, and
    its comment says why.
[^logout]: `routes/web.php` — `POST logout`; `app/Http/Controllers/Auth/SessionController.php` —
    `destroy()` logs out the `web` guard, invalidates the session and redirects to `/`.
[^seen]: `app/Http/Middleware/TrackLastSeen.php` — `updateLastSeen()` writes `last_seen_at`,
    `last_ip_address` and `last_user_agent` once per `$cacheMinutes`, which is 5.
[^roles]: `app/Enums/UserRole.php` — cases `SuperAdmin = 'admin'` and `User = 'user'`, `label()`,
    `canAccessAdmin()` and `canManageAllUsers()`; `app/Http/Middleware/EnsureUserIsAdmin.php` —
    `handle()` aborts with 403 unless the role `canAccessAdmin()`.
[^deactivated]: `app/Http/Requests/Auth/LoginRequest.php` — `authenticate()` logs out and refuses an
    inactive user with `Your account has been deactivated.`; `app/Http/Middleware/EnsureUserIsActive.php`
    — `handle()` ends the session and redirects to `login` with the same message, or answers 403 to a
    JSON request.
[^apikey-inactive]: `app/Http/Middleware/EnsureApiKey.php` — `handle()` aborts with 403 and
    `Your account has been deactivated.` when the owner is not active.
