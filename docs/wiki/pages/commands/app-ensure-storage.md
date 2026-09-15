+++
title = "app:ensure-storage"
subtitle = "Create any missing Laravel storage directories and generate OAuth signing keys if absent. Idempotent; safe to run on every deploy."
status = "approved"
goals = false
intent = """
app:ensure-storage exists so that a container whose storage folder starts empty has every folder the
framework writes to before the first request arrives. Running it twice changes nothing, so every
container start runs it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Command", value = "app:ensure-storage", cite = "signature" },
  { label = "Use", value = "creates any missing storage folder, on every container start", cite = "entrypoint" },
  { label = "Options", value = "none", cite = "signature" },
  { label = "Run by", value = "docker/deploy/entrypoint.sh", note = "on every container start", cite = "entrypoint" },
]

[[infobox]]
group = "Values"
rows = [
  { label = "Folder mode", value = "0775", cite = "folders" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Changes", value = "9 folders", note = "under storage/, each only when missing", cite = "folders" },
]

[[infobox]]
group = "Exit codes"
rows = [
  { label = "Success", value = "0", cite = "exit" },
]
+++

`app:ensure-storage` creates each storage folder the framework needs when it is missing, with a
`.gitignore` inside it, and leaves an existing one alone.[^folders] The container's start script runs it
before anything else, which is described on [Deployment](../deployment.md).[^entrypoint]

## Usage

The command takes no option of its own.[^signature]
```sh
app:ensure-storage
php artisan app:ensure-storage
```

## Output

The command prints one line for each folder or `.gitignore` it creates, and `Storage ready.` at the
end.[^output] When Passport is installed and no signing key exists it also generates the OAuth keys, which
the template does not do because Passport is not installed.[^passport] From a real run on a machine where
every folder already existed:[^output]
```text
Storage ready.
```

The nine folders are `app`, `app/public`, `framework`, `framework/cache`, `framework/cache/data`,
`framework/sessions`, `framework/testing`, `framework/views` and `logs`, each created with mode
`0775`.[^folders]

## Exit codes

| Code | Condition | Message |
|---|---|---|
| `0` | every folder exists[^exit] | `Storage ready.` |

[^signature]: `app/Console/Commands/EnsureStorage.php` — `$signature` is `app:ensure-storage`, with no
    options or arguments.
[^folders]: `app/Console/Commands/EnsureStorage.php` — `handle()` walks `$directories`, nine paths under
    `storage_path()`, calling `mkdir($path, 0775, true)` and writing a `.gitignore` only where each is
    missing.
[^entrypoint]: `docker/deploy/entrypoint.sh` — runs `php artisan app:ensure-storage`.
[^output]: `app/Console/Commands/EnsureStorage.php` — `handle()` prints `  created  storage/{$dir}` and
    `  created  storage/{$dir}/.gitignore` for each creation, then `Storage ready.`
[^passport]: `app/Console/Commands/EnsureStorage.php` — `handle()` calls `passport:keys` only when
    `Laravel\Passport\PassportServiceProvider` exists and no `oauth-private.key` does; `composer.json` —
    `require` lists no `laravel/passport`.
[^exit]: `app/Console/Commands/EnsureStorage.php` — `handle()` returns `self::SUCCESS` and has no other
    return.
