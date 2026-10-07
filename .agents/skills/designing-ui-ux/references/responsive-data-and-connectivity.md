# Responsive data and connectivity

Use these patterns for dense information on narrow screens or work that continues across slow,
interrupted, or offline connections.

## Recompose dense data by comparison task

Good: remove genuinely unnecessary columns; contain horizontal scrolling for numerical comparisons;
stack directory-like records with a label for each value; route to full detail when a summary cannot
preserve the task.

Bad: squeeze every column into unreadable text, convert data into unlabeled cards, or silently hide
critical columns based only on viewport width.

Preserve native table semantics and header relationships. Keep sorting, row identity, selection, and
actions understandable after the layout changes. Required row actions stay visible when hover is
unavailable.

## Treat connectivity as state, not an exception

Good: deliver a fast core task; show whether work is loading, offline, queued, synchronized, or
failed; preserve user input; state what remains available; and offer an honest retry or fallback.

Bad: prefetch everything, block the task behind an indefinite spinner, clear a draft, or treat a
network transition as proof that the user's action failed or succeeded.

Queue or synchronize only when the data contract permits it. Distinguish device connectivity from
service failure. Respect an explicit reduced-data preference when supported, but treat network and
device capability APIs as optional hints with fallbacks, not truth.

## Recover after lifecycle suspension

Good: when a page becomes visible again or returns from the back-forward cache, revalidate incomplete
or freshness-sensitive state from its canonical source. Coalesce retries and preserve local work.

Bad: assume a deferred request, timer, or realtime stream remained complete while the browser
suspended a background tab; leave a restored skeleton or stale live view indefinitely.

Events are invalidation hints, not a durable event log, unless the product explicitly provides one.
Use visibility return and persisted `pageshow` as reconciliation boundaries, then expose a terminal
retry or failure state instead of an endless watchdog loop.

## Prove data and network behavior

When present, verify sorting, selection, row actions, labels, and header relationships after the data
recomposes. Exercise slow and interrupted requests, background and foreground transitions,
back-forward restoration, offline entry, repeat activation, retry, and reconnection. Record what was
queued, persisted, synchronized, or discarded; never infer success from connectivity alone.
