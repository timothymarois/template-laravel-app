# Guide: Stand up centralized logging (Coolify → Loki + Grafana + DO Spaces)

**When to use:** You are deploying to a Coolify server and need production logs somewhere searchable.
The container is ephemeral with no log file to browse — see [`concepts/logging.md`](../concepts/logging.md)
for why, and for what the app itself emits.

**Prerequisites:** A Coolify server you administer, a DigitalOcean account, and a domain you can point
at Grafana. Nothing is installed in the app — an app only needs `LOG_CHANNEL=stderr`, which it already
sets.

## Decide first

**This is host infrastructure, built once and shared by every app on the server.** You do not repeat it
per fork, and you never run a second Loki against the same bucket — two single-binary instances do not
coordinate, so their compactors delete each other's chunks. One Loki, one bucket, forever; extra servers
get an Alloy agent only (step 6).

| Piece | Runs | Does |
|---|---|---|
| DO Space | DigitalOcean | Stores chunks + index. Cheap, no egress fee inside the region |
| Loki | Coolify resource | Ingests, indexes, enforces retention |
| Grafana | Coolify resource, own domain | The UI you query logs in |
| Alloy | Coolify resource, one per host | Reads the Docker socket, labels every container, pushes to Loki |

## Steps

### 1. Create the Space and its key

In DigitalOcean → **Spaces Object Storage** → *Create*, in the **same region as the server** (e.g.
`nyc3`). Keep it **private** and leave CDN off. Then **API → Spaces Keys → Generate New Key**, scoped to
that bucket.

Record four values — bucket name, region slug, key, secret. The endpoint is the region host **without**
the bucket: `nyc3.digitaloceanspaces.com`.

*Check:* `s3cmd`/`rclone`, or the DO console, lists the empty bucket.

### 2. Add the log stack as one Coolify resource

Coolify → your server → **+ New → Docker Compose**. Paste this, set the domain on the `grafana` service,
and add `LOKI_S3_*` + `GRAFANA_ADMIN_PASSWORD` under the resource's **Environment Variables**.

```yaml
services:
  loki:
    image: grafana/loki:3.5.0
    command: -config.file=/etc/loki/loki.yaml
    volumes:
      - loki-data:/loki
    configs:
      - source: loki_config
        target: /etc/loki/loki.yaml
  grafana:
    image: grafana/grafana:12.0.0
    environment:
      GF_SECURITY_ADMIN_PASSWORD: ${GRAFANA_ADMIN_PASSWORD}
      GF_USERS_ALLOW_SIGN_UP: "false"
    volumes:
      - grafana-data:/var/lib/grafana
  alloy:
    image: grafana/alloy:v1.8.0
    command: run --server.http.listen-addr=0.0.0.0:12345 /etc/alloy/config.alloy
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - alloy-data:/var/lib/alloy/data
    configs:
      - source: alloy_config
        target: /etc/alloy/config.alloy
volumes:
  loki-data:
  grafana-data:
  alloy-data:
configs:
  loki_config:
    content: |
      auth_enabled: false
      server: { http_listen_port: 3100 }
      common:
        instance_addr: 127.0.0.1
        path_prefix: /loki
        replication_factor: 1
        ring: { kvstore: { store: inmemory } }
        storage:
          s3:
            endpoint: ${LOKI_S3_REGION}.digitaloceanspaces.com
            bucketnames: ${LOKI_S3_BUCKET}
            region: ${LOKI_S3_REGION}
            access_key_id: ${LOKI_S3_KEY}
            secret_access_key: ${LOKI_S3_SECRET}
            s3forcepathstyle: false
      schema_config:
        configs:
          - from: 2024-01-01
            store: tsdb
            object_store: s3
            schema: v13
            index: { prefix: index_, period: 24h }
      compactor:
        working_directory: /loki/compactor
        retention_enabled: true
        delete_request_store: s3
      limits_config:
        retention_period: 2160h        # 90 days — the compactor enforces this
        reject_old_samples: false
  alloy_config:
    content: |
      discovery.docker "all" { host = "unix:///var/run/docker.sock" }
      loki.source.docker "all" {
        host          = "unix:///var/run/docker.sock"
        targets       = discovery.docker.all.targets
        forward_to    = [loki.write.default.receiver]
        relabel_rules = loki.relabel.coolify.rules
      }
      loki.relabel "coolify" {
        forward_to = []
        rule { source_labels = ["__meta_docker_container_name"], regex = "/(.+)", target_label = "app" }
        rule { source_labels = ["__meta_docker_container_label_coolify_name"], regex = "(.+)", target_label = "app" }
        rule { source_labels = ["__meta_docker_container_label_coolify_serviceName"], regex = "(.+)", target_label = "app" }
        rule { target_label = "server", replacement = "primary" }
      }
      loki.write "default" { endpoint { url = "http://loki:3100/loki/api/v1/push" } }
```

*Check:* all three containers stay up; the Loki log says `Loki started`, not an S3 auth error.

### 3. Point Grafana at Loki

Open Grafana on its domain, log in as `admin`, and change the password. **Connections → Data sources →
Add → Loki**, URL `http://loki:3100`, *Save & test*.

*Check:* the test reports "Data source successfully connected".

### 4. Confirm the app is emitting

In each app's Coolify resource, `LOG_CHANNEL=stderr` (the template's default) is all that is required —
no redeploy for logging, no per-app collector.

*Check:* the app's Coolify **Logs** tab shows one-line JSON per record.

### 5. Query it

Grafana → **Explore** → Loki → `{app="<your-app>"}`. Filter on parsed fields with
`{app="<your-app>"} | json | level="error"`.

*Check:* exercising the app produces a new line within a few seconds.

### 6. Add a second server (only when you have one)

The second server runs **Alloy alone**, pushing to the one Loki. Reach it either over a shared private
network (`http://<primary-private-ip>:3100`) or by giving the `loki` service a domain **with Basic Auth**
— Loki has no authentication of its own. Change `replacement = "primary"` to that host's name so
`{app="my-app", server="server-b"}` filters per host.

## Verify

- `{app="<your-app>"}` in Grafana returns live lines, with `level`, `message` and `context` parsed.
- The DO Space shows objects under `index_*` and `fake/` within ~15 minutes of first ingest.
- `{app="<your-app>"} | json | event="job.failed"` returns rows after a queued job fails — the structured
  background-failure line described in [`concepts/logging.md`](../concepts/logging.md).

## Pitfalls

| Symptom | Cause and fix |
|---|---|
| Loki exits with `SignatureDoesNotMatch` or `InvalidRegion` | The `region` and `endpoint` disagree, or the bucket name was appended to the endpoint. Endpoint is the region host only. If DO still rejects the signature, set `region: us-east-1` and keep the DO endpoint |
| Every container shows up as `app=""` | A relabel rule with `regex = "(.*)"` matches the empty string and blanks the label. Use `(.+)` so a missing Coolify label leaves the previous rule's value standing |
| `configs: content:` rejected on deploy | The host's Compose predates inline configs. Move both blocks to Coolify's per-resource **File mounts** and mount them at the same paths |
| Logs stop after a Loki restart | `/loki` was not persisted. The WAL and compactor working directory are local even though chunks live in S3 — keep the `loki-data` volume |
| Old logs never disappear | Retention is the compactor's job, not the bucket's. Keep `retention_enabled: true`; do **not** add a DO lifecycle rule as well, or it deletes chunks the index still references |
| Loki reachable from the internet | `auth_enabled: false` means no auth at all. Never give the `loki` service a bare domain — private network, or Basic Auth in front |
| Nothing arrives from a Coolify app | Alloy discovers containers on **its own host** only. One Alloy per server, and it needs the Docker socket mounted read-only |

## What this does not cover

Metrics and traces, alerting rules, and error tracking. Logs are searchable volume; grouped exceptions
and alerts are Sentry's job (`SENTRY_LARAVEL_DSN`) — the two are complementary, not alternatives.
