## Change a Live Schema Without Blocking It

Most `ALTER TABLE` forms take an `ACCESS EXCLUSIVE` lock, which blocks reads as well as writes. The
lock itself is often brief; the damage comes from waiting for it. A migration queued behind one long
transaction holds its place in the lock queue, and every query that arrives after it queues too.

**Incorrect (blocking forms on a busy table):**

```sql
-- Blocks writes for the whole build
create index orders_status_idx on orders (status);

-- Scans the whole table while holding ACCESS EXCLUSIVE
alter table orders add constraint orders_total_positive check (total >= 0);
alter table orders alter column customer_id set not null;

-- Waits indefinitely for the lock, stalling every query queued behind it
alter table orders add column note text;
```

**Correct:**

```sql
-- Bound every lock wait; on timeout, retry the migration later instead of stalling traffic
set lock_timeout = '5s';

-- Build indexes without blocking writes. CONCURRENTLY cannot run inside a transaction block,
-- and a failed build leaves an INVALID index: drop it and run the build again.
create index concurrently orders_status_idx on orders (status);
drop index concurrently if exists orders_old_idx;

-- Unique constraint: build the index concurrently, then attach it (brief lock only)
create unique index concurrently orders_ref_key on orders (ref);
alter table orders add constraint orders_ref_key unique using index orders_ref_key;

-- CHECK and FOREIGN KEY: add NOT VALID (brief lock, no scan), validate separately
-- (VALIDATE scans without blocking reads or writes)
alter table orders add constraint orders_total_positive check (total >= 0) not valid;
alter table orders validate constraint orders_total_positive;

alter table orders add constraint orders_customer_fk
  foreign key (customer_id) references customers (id) not valid;
alter table orders validate constraint orders_customer_fk;

-- SET NOT NULL scans the table, unless a validated CHECK already proves it (PostgreSQL 12+)
alter table orders add constraint orders_customer_id_not_null
  check (customer_id is not null) not valid;
alter table orders validate constraint orders_customer_id_not_null;
alter table orders alter column customer_id set not null;   -- no scan
alter table orders drop constraint orders_customer_id_not_null;
```

What still rewrites the whole table under `ACCESS EXCLUSIVE`:

- Adding a column with a **volatile** default such as `clock_timestamp()`. A constant or other
  non-volatile default is metadata-only from PostgreSQL 11.
- Changing a column's type, except binary-compatible changes such as widening `varchar(n)` or
  `varchar(n)` to `text`.

For those, add a new column, backfill it in batches, switch readers and writers, then drop the old
one.

Prove a migration on a copy at production volume first, and watch `pg_locks` joined to
`pg_stat_activity` while it runs.

Reference: [ALTER TABLE](https://www.postgresql.org/docs/current/sql-altertable.html),
[CREATE INDEX](https://www.postgresql.org/docs/current/sql-createindex.html#SQL-CREATEINDEX-CONCURRENTLY)
