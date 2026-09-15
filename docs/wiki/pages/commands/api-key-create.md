+++
title = "api-key:create"
subtitle = "Issue an API key for a user"
status = "approved"
goals = false
intent = """
api-key:create exists so that a key can be issued where there is no browser: seeding an environment,
provisioning a pipeline, scripting a deploy. The plaintext is printed once, because only its hash is
stored and there is no second chance to read it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "api-key:create", cite = "signature" },
  { label = "Use", value = "issues an API key where there is no browser, printing it once", cite = "output" },
  { label = "Options", value = "--user, --name, --ability, --days, --never-expires", cite = "signature" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Default ability", value = "api:read", cite = "abilities" },
  { label = "Default lifetime", value = "90 days", cite = "lifetime" },
  { label = "Longest lifetime", value = "365 days", note = "or none with --never-expires", cite = "lifetime" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Changes", value = "one key, only its hash stored", cite = "output" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "exit" },
  { label = "Refused", value = "1", cite = "exit" },
]
+++

`api-key:create` issues one key to an existing, active account and prints the plaintext once.[^signature]
What a key is, which abilities exist and how a key expires is described on [API keys](../api-keys.md);
the screen that issues one is described on [API keys](../admin/api-keys.md).

## Usage

The command asks for a missing address or name; every other option has a default.[^prompts]
```sh
api-key:create [--user[=USER]] [--name[=NAME]] [--ability[=ABILITY]]... [--days[=DAYS]] [--never-expires]
php artisan api-key:create --user=wiki-probe@example.test --name="Wiki probe" --ability=api:read --days=1
```

## Options

| Option | Meaning | Default |
|---|---|---|
| `--user` | the email address of the account the key belongs to[^signature] | asked for |
| `--name` | where the key is used, shown in the key list[^signature] | asked for, offering `CLI` |
| `--ability` | an ability to grant; given once per ability[^abilities] | `api:read` |
| `--days` | days until the key expires, 1 to 365[^lifetime] | 90 |
| `--never-expires` | a key with no expiry[^lifetime] | expires |

## Output

The command names the key, its owner, its abilities and its expiry, then prints the key on its own
line.[^output] From a real run:[^output]
```text
Issued “Wiki probe” for wiki-probe@example.test — api:read, expires Wed, Sep 16, 2026 2:39 AM.

Key (shown once, only its hash is stored):
10|apik_OkxO240kvLhgMO6g7GZP9qWZ9XDmWrdrAsA4W1uM909ba472
```

## Exit codes

| Code | Condition | Message |
|---|---|---|
| `0` | the key was issued[^exit] | the lines above |
| `1` | no account has that address[^unknown] | `No user with the email [nobody@example.test].` |
| `1` | the account is deactivated[^inactive] | `[ops@example.test] is deactivated; a key issued to them would be refused.` |
| `1` | an ability is not one the template knows[^ability] | `Unknown API ability [api:admin].` then `Valid abilities: api:read, api:write` |
| `1` | `--days` is outside 1 to 365[^lifetime] | `An API key lifetime must be 0 (never) or between 1 and 365 days.` |

[^signature]: `app/Console/Commands/CreateApiKey.php` — `$signature` declares `api-key:create` with
    `--user`, `--name`, `--ability=*`, `--days` and `--never-expires`; `handle()` looks the account up by
    email and issues the key through `ApiKeyService::issue()`.
[^prompts]: `app/Console/Commands/CreateApiKey.php` — `handle()` calls `ask('User email')` and
    `ask('Name', 'CLI')` when the options are empty.
[^abilities]: `app/Console/Commands/CreateApiKey.php` — `handle()` uses `[ApiAbility::Read->value]` when
    no `--ability` is given; `app/Enums/ApiAbility.php` — `Read` is `api:read` and `Write` is `api:write`.
[^lifetime]: `app/Console/Commands/CreateApiKey.php` — `handle()` passes `ApiKeyService::NEVER_EXPIRES`
    for `--never-expires`, the integer of `--days`, or null; `app/Services/ApiKeyService.php` — `issue()`
    uses `DEFAULT_LIFETIME_DAYS`, 90, for null and refuses a lifetime that is neither `NEVER_EXPIRES`,
    0, nor within 1 to `MAX_LIFETIME_DAYS`, 365.
[^output]: `app/Console/Commands/CreateApiKey.php` — `handle()` prints `Issued “%s” for %s — %s, expires %s.`,
    `Key (shown once, only its hash is stored):` and the plaintext; `app/Services/ApiKeyService.php` —
    `issue()` returns the plaintext once and persists only its SHA-256 hash.
[^unknown]: `app/Console/Commands/CreateApiKey.php` — `handle()` prints `No user with the email [{$email}].`
    when no account matches.
[^inactive]: `app/Console/Commands/CreateApiKey.php` — `handle()` prints
    `[{$email}] is deactivated; a key issued to them would be refused.` when the account is not active.
[^ability]: `app/Console/Commands/CreateApiKey.php` — `handle()` prints the service's message and
    `Valid abilities: ` with `ApiAbility::values()`; `app/Services/ApiKeyService.php` — `issue()` raises
    `Unknown API ability [{$ability}].`
[^exit]: `app/Console/Commands/CreateApiKey.php` — `handle()` returns `Command::FAILURE` on each refusal
    and `Command::SUCCESS` after printing the key.
