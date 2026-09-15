+++
title = "Central store"
subtitle = "Loki, Grafana and Alloy as Coolify resources, with a DigitalOcean Space behind them"
status = "approved"
goals = false
intent = """
The central store exists so that production logs from every container on a host can be searched in one
place after the container that wrote them is gone. One store serves every application on the server, and
an application needs nothing beyond logging to standard error to be found in it.
"""

[[infobox]]
group = "Identity"
rows = [
  { label = "Store", value = "Grafana Loki", cite = "loki-storage" },
  { label = "Collector", value = "Grafana Alloy", cite = "alloy-docker" },
  { label = "Viewer", value = "Grafana", cite = "grafana-loki" },
  { label = "Object storage", value = "a DigitalOcean Space", cite = "spaces" },
]

[[infobox]]
group = "Rules"
rows = [
  { label = "Retention", value = "90 days, enforced by the compactor", cite = "retention" },
  { label = "Loki per bucket", value = "one, ever", missing = true },
  { label = "Loki authentication", value = "none of its own", cite = "loki-auth" },
]
+++

The central store is one Loki, one Grafana and one Alloy on the Coolify server, with a DigitalOcean
Space holding the chunks and index; it is host infrastructure built once and shared by every application
on that host.{missing} What each application emits is described on [Logging](../logging.md).

## Pieces

| Piece | Runs as | Does |
|---|---|---|
| Space | DigitalOcean object storage | holds Loki's chunks and index through an S3-compatible interface[^spaces] |
| Loki | a Coolify Docker Compose resource | ingests, indexes and expires the lines[^loki-storage][^retention] |
| Grafana | the same resource, on its own domain | the interface the lines are searched in[^grafana-loki] |
| Alloy | the same resource, one per host | reads the Docker socket, labels every container and pushes to Loki[^alloy-docker][^alloy-source] |

## Setup

1. Create a private Space in the server's region, and a Spaces key scoped to it.[^spaces-create][^spaces-keys]
2. Add the Compose file below as one Coolify resource, give the `grafana` service its domain, and enter
   `LOKI_S3_REGION`, `LOKI_S3_BUCKET`, `LOKI_S3_KEY`, `LOKI_S3_SECRET` and `GRAFANA_ADMIN_PASSWORD` as
   the resource's environment variables.[^coolify-compose] Loki's endpoint is the region host alone,
   such as `nyc3.digitaloceanspaces.com`, never the bucket-prefixed address.[^spaces]
3. In Grafana, add a Loki data source at `http://loki:3100`.[^grafana-loki]
4. Set nothing in the application: `LOG_CHANNEL=stderr` is already its production setting.[^stderr]
5. Search with `{app="APP"}` in Grafana's Explore view, and narrow to parsed fields with
   `{app="APP"} | json | level="error"`.[^grafana-loki]

A second server runs Alloy alone, pushing to that Loki over a private network or a domain with
authentication in front, and changes `replacement = "primary"` to its own name.{missing}

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
        retention_period: 2160h
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

## Retention

Lines are kept for 90 days, and the compactor deletes older ones; a lifecycle rule on the Space is not
needed, and one shorter than the retention period deletes chunks the index still names.[^retention]

## Pitfalls

| Symptom | Cause |
|---|---|
| Loki exits with `SignatureDoesNotMatch` or `InvalidRegion` | the endpoint carries the bucket name, or the region and endpoint disagree{missing} |
| Every container shows as `app=""` | a relabel rule written `(.*)` matches an empty label and blanks the value{missing} |
| `configs: content:` is rejected on deploy | the host's Compose predates inline configs; the two files go in Coolify's file mounts instead{missing} |
| Lines stop after Loki restarts | the `/loki` volume was dropped; the write-ahead log and compactor directory live there{missing} |
| Loki reachable from the internet | Loki has no authentication of its own, so it belongs on a private network or behind an authenticating proxy[^loki-auth] |
| Nothing arrives from a second host | Alloy discovers only the containers of the Docker daemon it is given[^alloy-docker] |

[^spaces]: DigitalOcean Docs — [Spaces API reference](https://docs.digitalocean.com/products/spaces/reference/s3-compatibility/):
    Spaces provides an S3-compatible API, addressed as `BUCKET.REGION.digitaloceanspaces.com`.
[^spaces-create]: DigitalOcean Docs — [Create a Space](https://docs.digitalocean.com/products/spaces/how-to/create/):
    a Space is created from the control panel with a region and a name.
[^spaces-keys]: DigitalOcean Docs — [Manage access](https://docs.digitalocean.com/products/spaces/how-to/manage-access/):
    a Spaces access key is generated under API and can be limited to chosen buckets.
[^loki-storage]: Grafana Docs — [Loki storage](https://grafana.com/docs/loki/latest/configure/storage/):
    Loki stores chunks and the index in an object store configured under `storage`, including an
    S3-compatible one.
[^retention]: Grafana Docs — [Log retention](https://grafana.com/docs/loki/latest/operations/storage/retention/):
    retention is achieved through the Compactor, `retention_enabled` must be true and
    `delete_request_store` set, the period is `retention_period` under `limits_config`, an object store
    lifecycle policy is not required, and one added as a safety net must expire later than the retention
    period; the Compose file above sets `2160h`, which is 90 days.
[^loki-auth]: Grafana Docs — [Authentication](https://grafana.com/docs/loki/latest/operations/authentication/):
    Loki ships no authentication layer, and an authenticating reverse proxy must run in front of it.
[^alloy-docker]: Grafana Docs — [discovery.docker](https://grafana.com/docs/alloy/latest/reference/components/discovery/discovery.docker/):
    the component discovers the containers of the Docker daemon at `host` and exposes
    `__meta_docker_container_name` and `__meta_docker_container_label_…` labels.
[^alloy-source]: Grafana Docs — [loki.source.docker](https://grafana.com/docs/alloy/latest/reference/components/loki/loki.source.docker/):
    the component reads log entries from the containers in `targets`, applies `relabel_rules` and
    sends them to `forward_to`.
[^grafana-loki]: Grafana Docs — [Loki data source](https://grafana.com/docs/grafana/latest/datasources/loki/):
    a Loki data source is added by its URL, and its lines are queried with LogQL in Explore.
[^coolify-compose]: Coolify Docs — [Docker Compose](https://coolify.io/docs/knowledge-base/docker/compose):
    a Compose file is added as a resource from New Resource, and `${VARIABLE}` in it becomes an
    environment variable edited in Coolify.
[^stderr]: `.env.example` — the comment above `LOG_STDERR_FORMATTER` names `LOG_CHANNEL=stderr` as the
    production setting.
