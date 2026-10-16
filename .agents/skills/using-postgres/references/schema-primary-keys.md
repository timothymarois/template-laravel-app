## Select Optimal Primary Key Strategy

Primary key choice affects insert performance, index size, and replication
efficiency.

**Incorrect (problematic PK choices):**

```sql
-- identity is the SQL-standard approach
create table users (
  id serial primary key  -- Works, but IDENTITY is recommended
);

-- Random UUIDs (v4) cause index fragmentation
create table orders (
  id uuid default gen_random_uuid() primary key  -- UUIDv4 = random = scattered inserts
);
```

**Correct (optimal PK strategies):**

```sql
-- Use IDENTITY for sequential IDs (SQL-standard, best for most cases)
create table users (
  id bigint generated always as identity primary key
);

-- For distributed systems needing UUIDs, use UUIDv7 (time-ordered)
-- PostgreSQL 18+: built in
create table orders (
  id uuid default uuidv7() primary key  -- Time-ordered, appends instead of scattering
);
-- PostgreSQL 17 and older: generate UUIDv7 in the application, or use an extension
-- such as pg_uuidv7 (uuid_generate_v7())
```

Guidelines:

- Single database: `bigint identity` (sequential, 8 bytes, SQL-standard)
- Distributed/exposed IDs: UUIDv7 (`uuidv7()` on 18+, otherwise application-generated or an
  extension) or ULID — both time-ordered
- `serial` works but `identity` is SQL-standard and preferred for new
  applications
- Avoid random UUIDs (v4) as primary keys on large tables (causes index
  fragmentation)

Reference:
[Identity Columns](https://www.postgresql.org/docs/current/sql-createtable.html#SQL-CREATETABLE-PARMS-GENERATED-IDENTITY)
