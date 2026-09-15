+++
title = "user:create"
subtitle = "Create a user, optionally an admin"
status = "approved"
goals = false
intent = """
user:create exists so that a fresh install, or a fork that has gated its admin area on a role, gets
its first operator from the terminal without editing the database by hand. A password never has to be
typed on a command line: the command makes one and prints it once.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "user:create", cite = "signature" },
  { label = "Use", value = "creates an account from the terminal, as an operator with --admin", cite = "signature" },
  { label = "Options", value = "--name, --email, --password, --admin", cite = "signature" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Generated password", value = "16 characters", cite = "generated" },
  { label = "Default role", value = "user", cite = "role" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Changes", value = "one account", cite = "exit" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "exit" },
  { label = "Refused", value = "1", cite = "exit" },
]
+++

`user:create` creates one account and prints the result; with `--admin` the account is an operator who
can open the [admin area](../admin.md).[^signature] Without `--admin` the account gets the default role,
`user`, and cannot reach `/admin`.[^role] What an account is and how it signs in is described on
[Accounts](../accounts.md).

## Usage

Every option is optional; the command asks for a missing name or email address, and makes a password when
none is given.[^prompts]
```sh
user:create [--name[=NAME]] [--email[=EMAIL]] [--password[=PASSWORD]] [--admin]
php artisan user:create --name="Wiki Probe" --email=wiki-probe@example.test --admin
```

## Options

| Option | Meaning | Default |
|---|---|---|
| `--name` | the account's name, up to 255 characters[^rules] | asked for |
| `--email` | the address, lower case, not already registered[^rules] | asked for |
| `--password` | the password, at least 8 characters, with letters, mixed case, numbers and symbols in production[^password] | 16 random characters, printed once[^generated] |
| `--admin` | makes the account an operator[^role] | a plain `user` |

## Output

The command prints the account it created, the generated password when it made one, and a reminder when
the account cannot reach `/admin`.[^output] From a real run:[^output]
```text
Created Wiki Probe <wiki-probe@example.test> as Administrator.

Generated password (shown once): ,xbv)l6Ie.<uEG}p
```

Every validation failure is printed on its own line, and nothing is created.[^errors] The same address
again, with a short password:[^errors]
```text
The email has already been taken.
The password field must be at least 8 characters.
```

## Exit codes

| Code | Condition | Message |
|---|---|---|
| `0` | the account was created[^exit] | `Created Wiki Probe <wiki-probe@example.test> as Administrator.` |
| `1` | a name, address or password was refused[^exit] | one line for each failure, such as `The email has already been taken.` |

[^signature]: `app/Console/Commands/CreateUser.php` — `$signature` declares `user:create` with `--name`,
    `--email`, `--password` and `--admin`; `--admin` makes the role `UserRole::SuperAdmin`.
[^role]: `app/Console/Commands/CreateUser.php` — `handle()` uses `UserRole::default()` without `--admin`
    and prints `This user cannot reach /admin. Pass --admin to create an operator.`; `app/Enums/UserRole.php`
    — `default()` returns `User`, whose value is `user`, and `canAccessAdmin()` is true only for `SuperAdmin`.
[^prompts]: `app/Console/Commands/CreateUser.php` — `handle()` calls `ask('Name')` and `ask('Email address')`
    when the options are empty.
[^rules]: `app/Console/Commands/CreateUser.php` — `handle()` validates `name` as `required|string|max:255`
    and `email` as `required|string|lowercase|email|max:255|unique:users,email`.
[^password]: `app/Providers/AppServiceProvider.php` — `configurePasswordRules()` sets `Password::min(8)`,
    adding `letters()`, `mixedCase()`, `numbers()` and `symbols()` in production; `CreateUser::handle()`
    validates the password with `Password::defaults()`.
[^generated]: `app/Console/Commands/CreateUser.php` — `handle()` makes `str()->password(16)` when
    `--password` is absent and prints `Generated password (shown once): ` with it.
[^output]: `app/Console/Commands/CreateUser.php` — `handle()` prints `Created %s <%s> as %s.` with the
    role's label, then the generated password, then the `/admin` reminder for a plain user.
[^errors]: `app/Console/Commands/CreateUser.php` — `handle()` prints each entry of `errors()->all()` and
    returns before creating anything.
[^exit]: `app/Console/Commands/CreateUser.php` — `handle()` returns `Command::FAILURE` after validation
    errors and `Command::SUCCESS` after creating the account.
