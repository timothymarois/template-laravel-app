+++
title = "Password reset"
subtitle = "the reset link, its lifetime, and the messages a reader sees"
status = "approved"
goals = false
intent = """
Password reset exists so that a person who has forgotten their password gets back in from their mailbox
without help, and so that the form never tells a stranger which addresses have accounts.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Request page", value = "/forgot-password", cite = "flow" },
]

[[infobox]]
group = "Limits"
rows = [
  { label = "Link lifetime", value = "60 minutes", cite = "expiry" },
  { label = "Resend wait", value = "60 seconds", cite = "expiry" },
  { label = "Requests", value = "5 a minute", note = "per connection, shared with registration", cite = "throttle" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Unknown address", value = "answered as sent", cite = "request" },
  { label = "Sign-in after reset", value = "not automatic", cite = "done" },
]
+++

A reader asks for a link on `/forgot-password`, follows it from their mailbox and sets a new
password.[^flow] What a password must contain is described on [Accounts](../accounts.md).

## Request

The form takes one `Email address` and always answers `We have emailed your password reset link.`, whether
or not an account holds that address and whether or not a link went out in the last 60 seconds.[^request]
A link stays valid for 60 minutes.[^expiry] The form allows 5 requests a minute from one connection, a
budget it shares with registration, and answers the sixth with `Too Many Attempts.`[^throttle]

## Reset

The link opens `/reset-password/{token}` with the address filled in, and the reader enters
`New password` and `Confirm new password`.[^view] A stale link, a used link, or a link paired with an
address that has no account is answered `This password reset token is invalid.`, one message for every
case.[^invalid] A successful reset sends the reader to the sign-in page with
`Your password has been reset.` and does not sign them in, so a deactivated account is still stopped at
sign-in.[^done]

[^flow]: `routes/web.php` — the routes `password.request`, `password.email`, `password.reset` and
    `password.store`; `app/Http/Controllers/Auth/PasswordResetController.php` — `requestView()`,
    `sendLink()`, `resetView()` and `update()`.
[^request]: `app/Http/Controllers/Auth/PasswordResetController.php` — `sendLink()` discards the broker's
    status and flashes `passwords.sent`; `app/Http/Requests/Auth/ForgotPasswordRequest.php` — `rules()`
    has no `exists` rule, and its comment says why;
    `vendor/laravel/framework/src/Illuminate/Translation/lang/en/passwords.php` — `sent`;
    `resources/js/pages/ForgotPassword.vue` — the field and the `Email password reset link` button.
[^expiry]: `config/auth.php` — `passwords.users.expire` is 60 minutes and `throttle` is 60 seconds, read
    by the password broker.
[^throttle]: `routes/web.php` — `POST auth/forgot-password` and `POST auth/reset-password` carry
    `throttle:auth`; `app/Providers/AppServiceProvider.php` — `configureRateLimiting()` gives `auth`
    `Limit::perMinute(5)` keyed by IP;
    `vendor/laravel/framework/src/Illuminate/Routing/Middleware/ThrottleRequests.php` —
    `buildException()` raises `Too Many Attempts.`.
[^view]: `app/Http/Controllers/Auth/PasswordResetController.php` — `resetView()` renders `ResetPassword`
    with `token` and `email`; `resources/js/pages/ResetPassword.vue` — the two password fields and the
    `Reset password` button.
[^invalid]: `app/Http/Controllers/Auth/PasswordResetController.php` — `update()` answers every status but
    `PASSWORD_RESET` with `passwords.token`, and its comment says why;
    `vendor/laravel/framework/src/Illuminate/Translation/lang/en/passwords.php` — `token`.
[^done]: `app/Http/Controllers/Auth/PasswordResetController.php` — `update()` redirects to `login` with
    `passwords.reset`, and its comment says it does not log the user in;
    `vendor/laravel/framework/src/Illuminate/Translation/lang/en/passwords.php` — `reset`.
