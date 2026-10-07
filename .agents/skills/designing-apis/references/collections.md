# Collections

## Bound every collection from the first release

An unbounded list endpoint is a resource-exhaustion path: one caller asking for everything can
consume the memory, bandwidth, and database time of every other caller. Ship a default page size and
a maximum, and clamp rather than error when a caller asks for more.

Bound them before the table is large. Retrofitting pagination onto a published unpaginated endpoint
is a breaking change, because clients that read the whole array will silently start missing rows.

## Prefer keyset pagination over offsets

Offset pagination is easy to implement and wrong under writes: rows inserted or deleted while a
client iterates shift the window, so page two silently skips or repeats. The cost also rises with
depth, because the skipped rows still have to be produced.

```http
# Bad: page 2 changes meaning whenever a row is inserted.
GET /invoices?offset=100&limit=25

# Good: an opaque cursor anchored to a stable sort.
GET /invoices?limit=25&cursor=eyJpZCI6MTAwfQ
```

Keyset pagination requires a total ordering. Sort by the column the client actually wants plus a
unique tiebreaker — usually the identifier — or rows sharing a sort value fall through the cracks.

Make the cursor opaque. A cursor whose structure clients can read is a contract you did not intend
to publish, and you will want to change how it works.

Offsets remain reasonable for small, stable, or admin-facing collections, and for a UI that must
show numbered pages. Choose deliberately and say which you chose.

A total count is a separate decision from pagination: counting a large filtered set can cost more
than the page itself. Offer it only where it is needed, and consider an estimate or a
"has more" flag instead.

## Keep filtering, sorting, and sparse fields consistent

One convention across every endpoint. The specific spelling matters less than the fact that clients
learn it once.

- Filters name the field they filter on: `?status=active&created_after=2026-01-01`.
- Sorting names the field and the direction in one parameter: `?sort=-created_at`.
- Field selection lists what to include: `?fields=id,number,total`.

Two rules protect the server. Only allow filtering and sorting on fields you have decided to
support — an arbitrary field opens a query the schema cannot serve, and an arbitrary sort direction
on an unindexed column is a scan per request. And validate the values: a filter parameter
interpolated into a query is an injection, no different from any other untrusted input.

Empty results are `200` with an empty list, never `404`. The collection exists; it currently has no
matching members.
