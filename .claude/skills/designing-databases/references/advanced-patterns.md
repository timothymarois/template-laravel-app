# Data Modelling Advanced Patterns

Polymorphism, hierarchies, temporal history, JSON, denormalization, pagination, and safe schema
change. DDL is written in SQLite dialect because it is the smallest — the shapes are portable, the
spellings are not. `datetime('now')` is `now()` in PostgreSQL and `NOW()` in MySQL; `TEXT`
timestamps are `timestamptz` and `DATETIME`; `INTEGER PRIMARY KEY` is `bigserial` and
`BIGINT UNSIGNED AUTO_INCREMENT`.

Index syntax and planner behaviour belong to `using-postgres`, `using-mysql`, and
`using-sqlite`. The indexes shown here exist to say *which access path the model requires*, not
to teach index tuning.

## Polymorphic Associations

One relationship pointing at several possible types. All three shapes below work; they differ in
where the nulls go and how much integrity the database can enforce for you.

### Single table

Every type in one table, with the type-specific columns nullable.

```sql
CREATE TABLE notifications (
    id           INTEGER PRIMARY KEY,
    type         TEXT NOT NULL CHECK (type IN ('email', 'sms', 'push')),

    recipient_id INTEGER NOT NULL REFERENCES users(id),
    message      TEXT NOT NULL,
    sent_at      TEXT,

    email_subject TEXT,          -- email only
    email_html    TEXT,
    phone_number  TEXT,          -- sms only
    device_token  TEXT,          -- push only
    badge_count   INTEGER,

    -- Without this, nothing stops an SMS row carrying an email subject.
    CHECK (
        (type = 'email' AND email_subject IS NOT NULL AND phone_number IS NULL
                        AND device_token IS NULL) OR
        (type = 'sms'   AND phone_number IS NOT NULL AND email_subject IS NULL
                        AND device_token IS NULL) OR
        (type = 'push'  AND device_token IS NOT NULL AND email_subject IS NULL
                        AND phone_number IS NULL)
    )
);

CREATE INDEX idx_notifications_recipient ON notifications(recipient_id, sent_at DESC);
```

Cheapest to query — one table, no joins. The cost is the null density and a `CHECK` that grows
quadratically with the number of types. Good for three types that mostly share their columns; bad by
the time the type-specific columns outnumber the shared ones.

### Class table

A base table for what everything shares, one table per type for what it does not.

```sql
CREATE TABLE vehicles (
    id    INTEGER PRIMARY KEY,
    type  TEXT NOT NULL CHECK (type IN ('car', 'motorcycle', 'truck')),
    make  TEXT NOT NULL,
    model TEXT NOT NULL,
    year  INTEGER NOT NULL CHECK (year BETWEEN 1885 AND 2200)
);

CREATE TABLE cars (
    vehicle_id INTEGER PRIMARY KEY REFERENCES vehicles(id) ON DELETE CASCADE,
    doors      INTEGER NOT NULL CHECK (doors BETWEEN 1 AND 6),
    trunk_litres REAL
);

CREATE TABLE motorcycles (
    vehicle_id  INTEGER PRIMARY KEY REFERENCES vehicles(id) ON DELETE CASCADE,
    engine_cc   INTEGER NOT NULL,
    has_sidecar INTEGER NOT NULL DEFAULT 0 CHECK (has_sidecar IN (0, 1))
);

SELECT v.*, c.doors, c.trunk_litres
FROM vehicles v JOIN cars c ON c.vehicle_id = v.id
WHERE v.type = 'car';
```

Every column is meaningful and every constraint is real — `doors` can be `NOT NULL` here, which it
never could in the single-table version. The costs: a join to read a whole object, and nothing in
the database guarantees that a row with `type = 'car'` actually has a `cars` row. Enforce that in
the write path, in one place.

### Separate nullable foreign keys

When the *target* varies rather than the subject — a comment on an article, a photo, or a video.

```sql
CREATE TABLE comments (
    id         INTEGER PRIMARY KEY,
    body       TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),

    article_id INTEGER REFERENCES articles(id) ON DELETE CASCADE,
    photo_id   INTEGER REFERENCES photos(id)   ON DELETE CASCADE,
    video_id   INTEGER REFERENCES videos(id)   ON DELETE CASCADE,

    -- Exactly one target.
    CHECK (
        (article_id IS NOT NULL) + (photo_id IS NOT NULL) + (video_id IS NOT NULL) = 1
    )
);

CREATE INDEX idx_comments_article ON comments(article_id) WHERE article_id IS NOT NULL;
CREATE INDEX idx_comments_photo   ON comments(photo_id)   WHERE photo_id IS NOT NULL;
CREATE INDEX idx_comments_video   ON comments(video_id)   WHERE video_id IS NOT NULL;
```

This is the only shape with real referential integrity in both directions, and it is why it beats
the `(commentable_type, commentable_id)` pair that ORMs default to — that pair cannot have a foreign
key at all, so nothing stops a comment pointing at a deleted photo.

It stops scaling around the fifth type: each new one is a column, an index, and an edit to the
`CHECK`. At that point either the targets share a table, or you accept the untyped pair and enforce
the integrity in application code with your eyes open.

`(a IS NOT NULL) + (b IS NOT NULL) = 1` works because these engines evaluate a boolean as 0 or 1.
PostgreSQL spells it `num_nonnulls(article_id, photo_id, video_id) = 1`.

## Hierarchies

The same tree, three storages. The choice follows from which query you run most.

### Adjacency list

```sql
CREATE TABLE categories (
    id        INTEGER PRIMARY KEY,
    parent_id INTEGER REFERENCES categories(id) ON DELETE CASCADE,
    name      TEXT NOT NULL
);
CREATE INDEX idx_categories_parent ON categories(parent_id);

WITH RECURSIVE tree AS (
    SELECT id, parent_id, name, 0 AS depth, name AS path
    FROM categories WHERE parent_id IS NULL

    UNION ALL

    SELECT c.id, c.parent_id, c.name, t.depth + 1, t.path || '/' || c.name
    FROM categories c JOIN tree t ON c.parent_id = t.id
    WHERE t.depth < 32                      -- nothing here prevents a cycle
)
SELECT * FROM tree ORDER BY path;
```

Writes are trivial: moving a subtree is one `UPDATE` of one `parent_id`. Reads cost a recursive
query. The depth guard is not decoration — a cycle (A's parent is B, B's parent is A) is reachable
with two ordinary updates, and the recursion will not terminate without it.

### Materialized path

```sql
CREATE TABLE categories (
    id    INTEGER PRIMARY KEY,
    name  TEXT NOT NULL,
    path  TEXT NOT NULL,      -- '/1/5/12/', always with both delimiters
    -- Counts the path components, so a root node is 1 and '/1/5/12/' is 3. Derived from `path`,
    -- so it cannot disagree with it.
    depth INTEGER GENERATED ALWAYS AS
          (length(path) - length(replace(path, '/', '')) - 1) STORED
);
CREATE INDEX idx_categories_path ON categories(path);

-- Descendants: one index range scan.
SELECT * FROM categories WHERE path LIKE '/1/5/%';

-- Ancestors: no index, but the row count is the depth.
SELECT * FROM categories WHERE '/1/5/12/' LIKE path || '%' ORDER BY depth;
```

Descendant queries become a prefix match, which every engine indexes well. The cost is on moves: a
subtree move rewrites `path` on every descendant, and the paths are a denormalization that nothing
but your code keeps consistent. Good for trees that are read constantly and restructured rarely.

The `LIKE '/1/5/%'` prefix only uses the index if the pattern is left-anchored and the collation
cooperates — see `using-postgres`, `using-mysql`, or `using-sqlite` for what your engine
requires.

### Closure table

```sql
CREATE TABLE categories (
    id   INTEGER PRIMARY KEY,
    name TEXT NOT NULL
);

-- One row per ancestor/descendant pair, including each node's self-reference at depth 0.
CREATE TABLE category_closure (
    ancestor_id   INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    descendant_id INTEGER NOT NULL REFERENCES categories(id) ON DELETE CASCADE,
    depth         INTEGER NOT NULL,
    PRIMARY KEY (ancestor_id, descendant_id)
);
CREATE INDEX idx_closure_descendant ON category_closure(descendant_id, depth);

-- Descendants of 5, and ancestors of 12: both are index seeks, no recursion.
SELECT c.* FROM categories c
JOIN category_closure cc ON cc.descendant_id = c.id
WHERE cc.ancestor_id = 5 AND cc.depth > 0;

SELECT c.* FROM categories c
JOIN category_closure cc ON cc.ancestor_id = c.id
WHERE cc.descendant_id = 12 AND cc.depth > 0
ORDER BY cc.depth DESC;
```

Inserting a node under a parent means self-reference plus one row per ancestor:

```sql
-- New node :id under :parent_id
INSERT INTO category_closure (ancestor_id, descendant_id, depth)
SELECT ancestor_id, :id, depth + 1 FROM category_closure WHERE descendant_id = :parent_id
UNION ALL
SELECT :id, :id, 0;
```

Every hierarchy query is a plain indexed join, at the cost of O(depth) rows per insert and a
delete-and-reinsert of the affected pairs on every move. Do not maintain a closure table with an
insert trigger that only writes the self-reference — that is the common half-implementation, and it
makes every ancestor query silently return nothing.

## Temporal Data

### Versioned rows (SCD Type 2)

```sql
CREATE TABLE product_versions (
    id         INTEGER PRIMARY KEY,
    product_id INTEGER NOT NULL,        -- business key, stable across versions
    name       TEXT NOT NULL,
    price_cents INTEGER NOT NULL CHECK (price_cents >= 0),
    valid_from TEXT NOT NULL,
    valid_to   TEXT,                    -- NULL means current
    CHECK (valid_to IS NULL OR valid_to > valid_from)
);

-- Exactly one current version per product. A plain UNIQUE(product_id, valid_to) does NOT do
-- this: nulls do not collide, so it permits any number of open rows.
CREATE UNIQUE INDEX idx_products_current
    ON product_versions(product_id) WHERE valid_to IS NULL;

-- Current.
SELECT * FROM product_versions WHERE product_id = :id AND valid_to IS NULL;

-- As of a date.
SELECT * FROM product_versions
WHERE product_id = :id
  AND valid_from <= :as_of
  AND (valid_to IS NULL OR valid_to > :as_of);
```

A change closes the open row and opens a new one, in one transaction:

```sql
UPDATE product_versions SET valid_to = :now
WHERE product_id = :id AND valid_to IS NULL;

INSERT INTO product_versions (product_id, name, price_cents, valid_from)
VALUES (:id, :name, :price_cents, :now);
```

Half-open intervals — `valid_from` inclusive, `valid_to` exclusive — so consecutive versions can
share a boundary instant without overlapping. Note what this table is not: it records what the value
was, never who changed it. That is the audit log, in
[security-examples.md](security-examples.md).

PostgreSQL and SQLite both take the `WHERE` clause shown. MySQL has no partial indexes: the
equivalent is a generated column that holds the business key only while the row is open and is
`NULL` otherwise, with a `UNIQUE` on it — nulls do not collide, so closed rows do not compete. See
`using-mysql` for the generated-column and index syntax.

### Bitemporal

Two independent time axes: when a fact was true in the world, and when you recorded it.

```sql
CREATE TABLE contract_versions (
    id           INTEGER PRIMARY KEY,
    contract_id  INTEGER NOT NULL,
    amount_cents INTEGER NOT NULL,

    valid_from   TEXT NOT NULL,          -- true in the world from
    valid_to     TEXT,

    recorded_at  TEXT NOT NULL DEFAULT (datetime('now')),   -- known to us from
    superseded_at TEXT
);

-- What we believe now about what is true now.
SELECT * FROM contract_versions WHERE superseded_at IS NULL AND valid_to IS NULL;

-- What we believed on 2026-01-01 about what was true on 2026-01-01 — the query that answers
-- "what did the report say at the time", including corrections entered afterwards.
SELECT * FROM contract_versions
WHERE recorded_at <= :as_of AND (superseded_at IS NULL OR superseded_at > :as_of)
  AND valid_from  <= :as_of AND (valid_to      IS NULL OR valid_to      > :as_of);
```

Every read becomes four predicates and every write becomes two rows. Take this on only when
retroactive corrections have to be auditable — finance, insurance, payroll. Elsewhere it is cost
without a question to answer.

## JSON Columns

```sql
CREATE TABLE events (
    id         INTEGER PRIMARY KEY,
    type       TEXT NOT NULL,                    -- a column: every row has it, you filter on it
    occurred_at TEXT NOT NULL,                   -- a column
    data       TEXT NOT NULL CHECK (json_valid(data)),   -- genuinely varies by type
    UNIQUE (type, occurred_at, id)
);

SELECT id, json_extract(data, '$.amount_cents') AS amount_cents
FROM events
WHERE type = 'purchase' AND json_extract(data, '$.amount_cents') > 10000;

-- An expression index makes that predicate seekable. The expression must match the query's
-- exactly, character for character, or it will not be used.
CREATE INDEX idx_events_amount ON events(json_extract(data, '$.amount_cents'));
```

The line is: a field that every row has and that you filter on is a **column** — with a type, a
`NOT NULL`, and a constraint. JSON is for the shape that genuinely varies. `json_valid` in a `CHECK`
is the minimum; without it the column accepts anything and the first bad write is discovered by a
reader.

Nothing constrains what is *inside* the document. There is no `NOT NULL` on `$.amount_cents`, no
foreign key from `$.user_id`, and no type. Every read has to cope with absent and wrong-typed
fields. PostgreSQL's `jsonb` and MySQL's `JSON` add typed storage and richer indexing — see those
skills.

## Denormalization

Only after a measurement. Each of these adds a second source of truth and the code to keep it true.

### Duplicated column

```sql
CREATE TABLE posts (
    id        INTEGER PRIMARY KEY,
    user_id   INTEGER NOT NULL REFERENCES users(id),
    user_name TEXT NOT NULL,             -- copy of users.name
    content   TEXT NOT NULL
);

CREATE TRIGGER sync_post_user_name AFTER UPDATE OF name ON users
WHEN old.name <> new.name
BEGIN
    UPDATE posts SET user_name = new.name WHERE user_id = new.id;
END;
```

The trigger is now part of the schema's correctness. It also turns one profile edit into an update
of every post that user ever wrote — a cost that lands on the write, out of sight of the read you
were optimizing.

A duplicated value is sometimes not a denormalization at all: an invoice's `customer_name` should
record what the name *was* when the invoice was issued, and must not follow later edits. Know which
of the two you are building, because they need opposite code.

### Summary table

```sql
CREATE TABLE product_sales_summary (
    product_id     INTEGER PRIMARY KEY REFERENCES products(id) ON DELETE CASCADE,
    total_quantity INTEGER NOT NULL DEFAULT 0,
    total_cents    INTEGER NOT NULL DEFAULT 0,
    updated_at     TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TRIGGER bump_sales_summary AFTER INSERT ON order_items
BEGIN
    INSERT INTO product_sales_summary (product_id, total_quantity, total_cents, updated_at)
    VALUES (new.product_id, new.quantity, new.quantity * new.unit_cents, datetime('now'))
    ON CONFLICT(product_id) DO UPDATE SET
        total_quantity = total_quantity + new.quantity,
        total_cents    = total_cents + (new.quantity * new.unit_cents),
        updated_at     = datetime('now');
END;
```

Incremental maintenance drifts — a missed `UPDATE` trigger, a `DELETE` nobody handled, a bulk load
that bypassed the trigger. Keep a recomputation query, run it on a schedule, and compare. A summary
nobody can rebuild is a number nobody can trust. PostgreSQL's materialized views do this properly;
see `using-postgres`.

## Pagination

```sql
-- Offset: correct, and progressively slower. OFFSET 10000 produces and discards 10 000 rows.
SELECT * FROM posts ORDER BY created_at DESC LIMIT 20 OFFSET 10000;

-- Keyset with a non-unique sort column: broken. Rows sharing the boundary timestamp are
-- skipped or repeated across the page boundary.
SELECT * FROM posts WHERE created_at < :last_seen ORDER BY created_at DESC LIMIT 20;

-- Keyset, total order: the cursor carries both columns.
SELECT * FROM posts
WHERE (created_at, id) < (:last_created_at, :last_id)
ORDER BY created_at DESC, id DESC
LIMIT 20;

CREATE INDEX idx_posts_pagination ON posts(created_at DESC, id DESC);
```

Row-value comparison — `(a, b) < (x, y)` — is supported by PostgreSQL, MySQL, and SQLite. Where it
is not, the expansion is
`created_at < :c OR (created_at = :c AND id < :i)`, which needs the same index.

Keyset pagination cannot jump to page 50, because the cursor is the previous page's last row. That
is the trade: it buys constant-time paging and gives up random access. Offset is fine for a list
that is bounded and small.

## Schema Change

The mechanics of applying a change without downtime are engine-specific — `using-mysql` for
online DDL, `using-sqlite` for the table rebuild, `using-postgres` for which DDL takes which
lock. What the *model* owes is a sequence in which every intermediate state is valid for the code
running against it.

### Adding a required column

Never in one step. Three deploys:

```sql
-- 1. Add it nullable. Old code ignores it; new code writes it.
ALTER TABLE users ADD COLUMN phone TEXT;

-- 2. Backfill in batches, then deploy code that always writes it.
UPDATE users SET phone = '' WHERE phone IS NULL AND id BETWEEN :lo AND :hi;

-- 3. Only once no null remains and no writer omits it, tighten the constraint.
```

Adding `NOT NULL` in the same migration that adds the column requires a default for every existing
row and a total rewrite on some engines. Splitting it means each step is reversible on its own.

### Renaming

A rename is not one migration, because it breaks every reader at the instant it applies. Add the new
column, dual-write, backfill, move readers, then drop the old one — four deploys, each safe alone.

The same applies to a table: `ALTER TABLE articles RENAME TO posts` is atomic in the database and
catastrophic for whatever is still querying `articles`.

### Deleting

Stop writing it, stop reading it, wait long enough to roll back, then drop. A column dropped in the
same release that stopped using it has no rollback — the data is gone and the previous version of
the code expects it.
