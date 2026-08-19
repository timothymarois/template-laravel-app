# Migrating a fork to template v5.6.0

v5.6.0 adds **`docker/project/`** — a supported place for a fork's own nginx and
PHP configuration, so needing a per-route body ceiling or a higher upload limit
no longer means editing managed core. `Docker:` minor — no schema, dependency,
API, or app-runtime change.

> **Why:** `docker/config/nginx.conf` and `docker/config/php.ini` are managed
> core. A fork that needed a 100 MB upload had exactly two options: edit them
> and drift, or raise `client_max_body_size` at server level and hand *every*
> endpoint — including unauthenticated ones — that much temp disk and inbound
> bandwidth per concurrent request (nginx buffers a body to
> `client_body_temp_path` before PHP ever sees it). This gives it a third.

## Prerequisites

- Fork is on template **v5.5.0**. Check: `jq -r .version template-manifest.json` → `5.5.0`.
- **Drop-in.** Every fork takes Parts A–C to stay in sync. Part D is optional and
  only for a fork that actually needs project configuration.

---

## Part A — Take the managed-core changes (every fork)

Copy these from a fresh clone of the template, unchanged:

- `docker/config/nginx-snippets/laravel-fastcgi.conf` *(new)*
- `docker/config/nginx-snippets/laravel-front-controller.conf` *(new)*
- `docker/config/nginx.conf` *(changed — three edits, below)*
- `Dockerfile` *(changed — the service-configuration COPY block)*

The `nginx.conf` edits, if you prefer to apply them by hand:

```diff
+# ── Project configuration: http context ──────────────────────────────────────
+# … (see the template for the full comment)
+include /etc/nginx/project/http/*.conf;
+
 server {
     listen 80 default_server;
```

```diff
     location ~ \.php$ {
-        fastcgi_pass 127.0.0.1:9000;
-        fastcgi_index index.php;
-        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
-        include fastcgi_params;
-        fastcgi_hide_header X-Powered-By;
-        fastcgi_read_timeout 65;
+        include /etc/nginx/snippets/laravel-fastcgi.conf;
     }
```

```diff
     location ~ /\.(?!well-known).* {
         deny all;
     }
+
+    # ── Project configuration: server context ────────────────────────────────
+    # … (see the template for the full comment)
+    include /etc/nginx/project/server/*.conf;
 }
```

**The server include goes last.** nginx tries regex locations in configuration
order, so everything the template ships wins a tie — a project regex can neither
shadow `location ~ \.php$` nor punch a hole in the dotfile `deny all`.

### If your fork asserts on `fastcgi_read_timeout`

Moving the FastCGI body into a snippet moves the directive with it. A fork whose
tests read `fastcgi_read_timeout` out of `docker/config/nginx.conf` — sizing a
long-running endpoint against it, for instance — starts failing on a directive
that is merely somewhere else. Read whichever managed-core file declares it:

```php
$files = array_merge(
    [base_path('docker/config/nginx.conf')],
    glob(base_path('docker/config/nginx-snippets/*.conf')) ?: [],
);
```

*(Found by running `aprillaneart-site`'s suite against this change — its
`ProductFeedCeilingsTest` sizes the feed's deadline against that timeout.)*

## Part B — Create the (empty) project directories (every fork)

Docker `COPY` fails on a missing source, so these must exist even when unused:

```bash
mkdir -p docker/project/nginx/http docker/project/nginx/server docker/project/php
touch docker/project/nginx/http/.gitkeep \
      docker/project/nginx/server/.gitkeep \
      docker/project/php/.gitkeep
```

`.dockerignore` already excludes `*.md`, so a README you drop in these
directories will not be copied into the image.

## Part C — Record the version (every fork)

```diff
-    "version": "5.5.0",
+    "version": "5.6.0",
```

Nothing else changes. With `docker/project/` empty the built image is
byte-identical in behavior: still `client_max_body_size 25M`, still the same PHP
limits. A wildcard include that matches nothing is not an nginx error.

---

## Part D — Use it (only forks that need project configuration)

See `docker/README.md` → **"Project configuration"** for the directory table and
a worked example. Three things are easy to get wrong:

1. **A project location must terminate in FastCGI itself.** Wrapping
   `location /` does not work — `try_files $uri /index.php` issues an internal
   redirect, nginx re-runs location matching, and the elevated
   `client_max_body_size` is lost to `location ~ \.php$` before the body is
   read. End the block with
   `include /etc/nginx/snippets/laravel-front-controller.conf;`.
2. **Prefix a project ini `zzz-`.** PHP scans `conf.d` in filename order and the
   template lands at `zz-app.ini`; an earlier name loses every directive it
   repeats.
3. **Guard an elevated route before the body is read.** `limit_conn` and
   `limit_req` run in nginx's preaccess phase, so a throttled request never
   reaches `client_body_temp_path`. Without them, one elevated route is an
   unmetered temp-disk and bandwidth sink. The zones must be declared in the
   **http**-context file — a shared-memory zone cannot be declared inside a
   `server` block.

## Verifying

If your fork runs the template's CI, `.github/workflows/docker-config.yml`
proves the wiring against the built image on every `docker/**` change. To check
by hand:

```bash
docker build --target runtime -t app:check .
docker run --rm --entrypoint sh app:check -c 'nginx -t'
docker run --rm --entrypoint sh app:check -c 'nginx -T' | grep -n 'project/'
docker run --rm --entrypoint sh app:check -c 'php -r "echo ini_get(\"post_max_size\");"'
```
