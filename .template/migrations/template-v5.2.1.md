# Migrating a fork to template v5.2.1

v5.2.1 adds the **Reverb WebSocket proxy** that `docker/config/nginx.conf` was
missing. The browser opens a Pusher-protocol websocket at `/app/{appKey}`
(`pusher-js`, which Laravel Echo/Reverb use); nginx must upgrade that request and
proxy it to the Reverb server on `127.0.0.1:8080`. Without the location block the
handshake falls through to `location /` → `index.php` and the socket fails to
connect. `Docker:` patch — nginx-only, no schema/API/app-runtime change.

> **Why:** server-side publishing already goes **direct** to `127.0.0.1:8080`
> (`/apps/{id}/events`), so it worked once `REVERB_HOST` was internal. But the
> **browser → Reverb** path runs through the public domain → Traefik → the
> container's nginx, which had no `/app/` rule — so every `wss://…/app/{key}`
> connection dropped. Real-time sync silently never reached any client.

## Prerequisites

- Fork is on template **v5.2.0**. Check: `jq -r .version template-manifest.json` → `5.2.0`.
- Only forks with `docker.requires.reverb: true` need this functionally. Others
  should still take it (a harmless no-op — nothing listens on 8080) to stay in sync.

---

## Part A — Add the websocket location (every fork)

In `docker/config/nginx.conf`, add the `location ^~ /app/` block immediately
**before** `location /`:

```diff
+    # Laravel Reverb WebSocket (public, client-facing). Upgrades the Pusher-protocol
+    # /app/{appKey} connection to the Reverb server on 127.0.0.1:8080. The ^~ /app/
+    # matcher catches the WebSocket path ONLY — never /apps/{id}/events (the
+    # server-side events API), which must stay off the public domain. No-op for
+    # forks that don't run Reverb (nothing listens on 8080).
+    location ^~ /app/ {
+        proxy_pass http://127.0.0.1:8080;
+        proxy_http_version 1.1;
+        proxy_set_header Host $host;
+        proxy_set_header X-Real-IP $remote_addr;
+        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
+        proxy_set_header X-Forwarded-Proto $scheme;
+        proxy_set_header Upgrade $http_upgrade;
+        proxy_set_header Connection "Upgrade";
+        proxy_read_timeout 3600s;
+        proxy_send_timeout 3600s;
+    }
+
     location / {
         try_files $uri $uri/ /index.php?$query_string;
     }
```

The `^~ /app/` prefix matches `/app/{key}` (the websocket) but **not** `/apps/…`
(the events API), so the server-side endpoint stays unreachable publicly.

---

## Reverb env reminder (forks running Reverb)

The two Reverb endpoints are independent and must not be conflated:

- **Server-side publish (PHP → Reverb), internal:**
  `REVERB_HOST=127.0.0.1`, `REVERB_PORT=8080`, `REVERB_SCHEME=http`.
  Reverb speaks plain HTTP and is reached over the in-container loopback — do
  **not** point this at the public domain or `:443` (the POST loops back through
  the proxy into the web app and fails with an HTML 404).
- **Client-side (browser → Reverb), public, build-time baked:**
  `VITE_REVERB_ENABLED=true`, `VITE_REVERB_HOST=<public domain>`,
  `VITE_REVERB_PORT=443`, `VITE_REVERB_SCHEME=https` — set in committed
  `.env.example` (Vite inlines them at build; runtime env can't reach the bundle).

---

## Verify

After redeploy, from the browser on the public domain (DevTools → Network → WS):
the `wss://<domain>/app/{key}` connection should show **101 Switching Protocols**
and stay open. A `broadcast(new ...)` then arrives in other tabs.

From inside the container, Reverb still answers directly:

```sh
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8080
```

## Finally

Bump the fork's `template-manifest.json` → `"version": "5.2.1"`. Don't copy this
changelog into the fork — the manifest `version` is the record (see the template's
[`.template/README.md`](../README.md)).
