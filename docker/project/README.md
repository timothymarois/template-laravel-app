# `docker/project/` — the fork's own nginx configuration

**This directory is yours.** The template never writes into it, so nothing here conflicts on an
upgrade. Everything else under `docker/` is managed core and tracks the template — see the drift
policy in [`../README.md`](../README.md).

It exists so a fork can add nginx configuration **without editing managed core**. Before it existed,
a fork that needed more upload capacity had exactly one move: raise `client_max_body_size` on the
server block. That is a trap, and it is the reason this directory ships.

## The trap it replaces

`client_max_body_size` on the server block applies to **every route**, and nginx buffers a request
body to temp disk **before PHP is ever reached**. A server-wide `110M` therefore hands every
endpoint — unauthenticated ones included — that much temp disk and inbound bandwidth per concurrent
request. The global stays low (`25M`); capacity is granted to the specific routes that need it.

## What goes where

| Directory | Baked to | nginx context | Use for |
|---|---|---|---|
| `nginx/http/*.conf` | `/etc/nginx/conf.d/` | `http` | `limit_req_zone`, `limit_conn_zone`, `map`, `upstream` — anything nginx rejects outside `http` |
| `nginx/server/*.conf` | `/etc/nginx/snippets/` | `server` | `location` blocks and directives scoped to them |

`../config/nginx.conf` includes `/etc/nginx/snippets/*.conf` **before** `location ~ \.php$`, so your
locations are matched first. The include is a wildcard: with no files here it does nothing, and the
image builds exactly as before.

Shared-memory zones must be declared in `http` and used from `server`, so a rate- or connection-limited
route is normally two files — the zone in `nginx/http/`, the `location` that uses it in `nginx/server/`.

## Two traps inside a `location` block

Both cost real debugging time. Neither is guessable, and neither announces itself — the config loads,
nginx starts, and the behaviour is silently wrong.

**1. Do not hand off with `try_files`.** It issues an *internal redirect*: nginx re-runs location
matching, `location ~ \.php$` wins, and every directive you set is replaced by that location's
**before the request body is read**. A block that raises a ceiling and then delegates via `try_files`
has raised nothing. Terminate in FastCGI inside the block itself:

```nginx
location ~ ^/admin/uploads$ {
    client_max_body_size 110M;

    fastcgi_pass 127.0.0.1:9000;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    fastcgi_param SCRIPT_NAME /index.php;
    # ... the remaining parameters, listed explicitly
}
```

**2. Do not `include fastcgi_params` in such a block.** It sets `SCRIPT_NAME` to
`$fastcgi_script_name`, which for these URIs is the request path rather than `/index.php`. Symfony
derives its base URL from `SCRIPT_NAME`, so Laravel routes `/` instead of your route. List the
parameters explicitly with `SCRIPT_NAME` pinned to `/index.php` — which also makes the block
independent of whatever the base image ships in `fastcgi_params`.

## If you raise an upload ceiling, do the temp-disk arithmetic

`fastcgi_request_buffering` is on, so nginx buffers the whole body to `client_body_temp_path` and PHP
then writes its own copy to `upload_tmp_dir` — **the two overlap**. One accepted upload occupies
roughly `client_max_body_size + upload_max_filesize` of local temp space. Multiply by the concurrency
you allow and confirm the container has that much free.

Bound the concurrency, or the arithmetic is a hope rather than a ceiling. `limit_conn` and `limit_req`
run in nginx's **preaccess** phase, so a refused request is answered before its body is read: it never
reaches temp disk and never consumes the inbound bandwidth of a large upload.

Give transport ceilings a little headroom over the application's own limit (a multipart envelope adds
headers and boundaries), so a legal request is refused by validation with a message rather than dying
as a bare `413`. And size `fastcgi_read_timeout` against what the request *does* — if the handler
streams the file onward to object storage, that transfer is what the timeout must cover, not the
upload.

## Per-route PHP limits

`php-fpm` applies `PHP_VALUE` per request, so a route can exceed the global in `../config/php.ini`
without changing it for anything else. Values are newline-separated, and nginx does not interpret
`\n` inside a quoted string — they must be real line breaks:

```nginx
fastcgi_param PHP_VALUE "upload_max_filesize=100M
post_max_size=110M
max_execution_time=300";
```

## Proving it

This repository asserts *agreement* between the files — `pnpm check:deploy` checks that the globals
were not quietly raised and that the snippets include still precedes the PHP location. It cannot
start nginx. Before trusting a change on a real deploy, run `nginx -t` and read the effective config
with `nginx -T` in the built image, then confirm an oversized body is refused on a default route and
accepted on the elevated one.
