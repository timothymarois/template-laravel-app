# Logging Guidelines

How application logs work locally and in production, and how to centralize them when you deploy.

## The model

| Environment | `LOG_CHANNEL` | Where logs go | How you read them |
|-------------|---------------|---------------|-------------------|
| Local / dev | `daily` | `storage/logs/laravel-YYYY-MM-DD.log` | `tail -f storage/logs/laravel-*.log`, your IDE, or `php artisan pail` |
| Production (Docker/Coolify) | `stderr` | the container's stdout/stderr | a central log sink (this guide) |

In production every process — php-fpm, nginx, Horizon, the scheduler, Inertia SSR — writes to **stdout/stderr** (see `docker/config/supervisord.conf`). The container is **ephemeral and has no persistent disk**, so there are no log files to browse. This is the correct cloud-native pattern: the container emits a log stream, and a collector ships that stream to one central store you can search.

> There is no in-app log browser. Choose **one** external sink, point your collector at it, and read logs there.

## Choosing a sink

Pick whichever fits the project — you only wire up **one**. The app ships nothing sink-specific; this is an infrastructure choice per deployment.

| Sink | Hosting | Retention storage | Best when |
|------|---------|-------------------|-----------|
| **Loki + Grafana** (self-hosted) | You run it (one instance serves many apps) | S3 / DO Spaces (cheap) | You want full control and the lowest storage cost, and don't mind operating it |
| **Axiom** | Managed SaaS | Theirs | You want zero infra; native Coolify Log Drain support; generous free tier |
| **Better Stack / Grafana Cloud** | Managed SaaS | Theirs | You want zero infra and a hosted UI; ship via Fluent Bit/HTTP |

All of these accept logs from many apps and separate them by **labels** (e.g. `app="my-app"`), so a single Loki/account can serve every project — you do not run one per app.

## Forwarding mechanisms

Two ways to get the container's stdout into your chosen sink. They are independent of which sink you pick.

### Host collector — Grafana Alloy / Fluent Bit (recommended)

Run one collector container on the host (the Coolify server), mounting the Docker log directory and socket. It auto-discovers every container, attaches labels, and pushes to your sink. This captures **all** output (nginx, php-fpm, workers — not just Laravel app logs) and is decoupled from any platform feature.

- Grafana **Alloy** is the current collector (replaces the deprecated Promtail).
- One collector per host serves every app on that host.

### Coolify native Log Drain (fallback)

Coolify has a per-server **Log Drain** (Fluent Bit) under *Server → Log Drains*; enable it per resource under the resource's *Advanced* tab. It targets Axiom and New Relic natively, or any custom Fluent Bit destination.

> ⚠️ **Caveat:** Coolify currently ignores the log-drain definition for **Dockerfile build-pack** resources ([coollabsio/coolify#2915](https://github.com/coollabsio/coolify/issues/2915)) — and this template deploys as a Dockerfile build pack. Until that's fixed, prefer the host collector above.

## Structured (JSON) logs — on by default

In production the `stderr` channel emits **one-line JSON per record by default** (`config/logging.php` → `stderr.formatter` defaults to `Monolog\Formatter\JsonFormatter`). A collector ships those fields (level, message, context, timestamp) to your sink already parsed — no per-app setup. Local dev is unaffected (it uses the `daily` file channel).

To get **human-readable lines** instead (e.g. to eyeball the Coolify Logs tab), override per environment:

```dotenv
LOG_STDERR_FORMATTER=          # empty = Monolog's default LineFormatter
```

## Recommended default — Coolify → Loki + Grafana

> **A ready-to-deploy stack lives at [`docker-laravel-base/observability`](https://github.com/timothymarois/docker-laravel-base/tree/main/observability)** — a single `docker-compose.yaml` with the Loki, Alloy, and Grafana configs embedded. Paste it into a Coolify *Docker Compose* resource, set `GF_ADMIN_PASSWORD`, attach a domain to the `grafana` service. You don't need to hand-assemble the snippets below — they're here to explain what that stack does.

The default path for this template's deployments. The **app side needs nothing** beyond what ships: `LOG_CHANNEL=stderr` (set in Coolify) + JSON-by-default (above). Everything below is **one-time host infrastructure**, shared by every app on the server — you do not repeat it per fork.

**1. Object storage (retention).** Create an S3-compatible bucket — e.g. a DigitalOcean Space — and an access key/secret. This is where Loki keeps chunks + index cheaply.

**2. Loki** (single-binary, `-target=all`) as a Coolify *Docker Compose* resource. Point it at the bucket and enforce retention:

```yaml
schema_config:
  configs:
    - { from: 2024-01-01, store: tsdb, object_store: s3, schema: v13, index: { prefix: index_, period: 24h } }
common:
  storage:
    s3:
      endpoint: <region>.digitaloceanspaces.com   # region host only, no bucket
      bucketnames: <your-bucket>
      region: <region>
      access_key_id: ${LOKI_S3_KEY}
      secret_access_key: ${LOKI_S3_SECRET}
      s3forcepathstyle: false
compactor: { working_directory: /loki/compactor, retention_enabled: true, delete_request_store: s3 }
limits_config: { retention_period: 2160h }   # 90 days; compactor enforces it
```

**3. Grafana** as a Coolify resource behind its own domain (admin auth + SSL). Add a Loki datasource at `http://loki:3100`. This is the log UI.

**4. Grafana Alloy** (collector) — one container on the Coolify host, mounting the Docker socket. It auto-discovers every container, labels by app, and pushes to Loki. **This is the piece that connects your apps** — no app redeploy:

```hcl
discovery.docker "all" { host = "unix:///var/run/docker.sock" }
loki.source.docker "all" {
  host = "unix:///var/run/docker.sock"
  targets = discovery.docker.all.targets
  forward_to = [loki.write.default.receiver]
  relabel_rules = loki.relabel.coolify.rules
}
loki.relabel "coolify" {
  rule { source_labels = ["__meta_docker_container_label_coolify_serviceName"], target_label = "app" }
}
loki.write "default" { endpoint { url = "http://loki:3100/loki/api/v1/push" } }
```

In Grafana, query `{app="<your-app>"}` to see each app's logs. One Loki + one Grafana + one bucket serve **all** apps on the host — separated by the `app` label, never one stack per app.

> Run Loki + Grafana + Alloy as Coolify-managed resources (ideally one Compose stack) so the Coolify UI controls their lifecycle. A raw `docker run`/compose outside Coolify won't be tracked, and deleting it from the UI leaves the container running.

## Multiple servers

There is always **one** Loki and **one** bucket. **Never** run a second Loki against the same bucket — single-binary Loki instances don't coordinate, so two would corrupt each other's index and their compactors would delete each other's chunks. Instead, every additional server runs **only an Alloy agent** that pushes to the one central Loki:

```
Primary server:  Loki + Grafana + Alloy   ← the only Loki, the only bucket
Other server(s): Alloy agent  ───────────▶ pushes to the central Loki
```

Two steps to add a server:

1. **Expose the central Loki to it** — either give the `loki` service a domain (`https://loki.example.com`, port 3100) with **Basic Auth** (Loki has no auth of its own), or, if both servers share a private network (e.g. a DO VPC), push to the primary's private IP `http://<private-ip>:3100`.
2. **Deploy the Alloy agent on the other server** (collector only — no Loki/Grafana) pointed at that Loki URL, tagging logs with a `server` label so you can filter per host in Grafana: `{app="rundesk", server="server-b"}`.

A ready-made agent stack is at [`docker-laravel-base/observability/alloy-agent`](https://github.com/timothymarois/docker-laravel-base/tree/main/observability/alloy-agent) — paste it as a Coolify Docker Compose resource on the other server and set `LOKI_URL` + `SERVER_LABEL`. Apps on that server still just need `LOG_CHANNEL=stderr`; nothing app-side changes for multi-server.

## Errors vs. logs

Logging is for searchable, high-volume application output. For **error tracking and alerting** (grouped exceptions, release/commit correlation), the template ships Sentry — set `SENTRY_LARAVEL_DSN`. The two are complementary; use both.
