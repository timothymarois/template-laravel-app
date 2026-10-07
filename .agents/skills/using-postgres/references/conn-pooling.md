## Use Connection Pooling for All Applications

Postgres connections are expensive (1-3MB RAM each). Without pooling, applications exhaust connections under load.

**Incorrect (new connection per request):**

```sql
-- Each request creates a new connection
-- Application code: db.connect() per request
-- Result: 500 concurrent users = 500 connections = crashed database

-- Check current connections
select count(*) from pg_stat_activity;  -- 487 connections!
```

**Correct (connection pooling):**

```sql
-- Use a pooler like PgBouncer between app and database
-- Application connects to pooler, pooler reuses a small pool to Postgres

-- Configure pool_size based on: (CPU cores * 2) + spindle_count
-- Example for 4 cores: pool_size = 10

-- Result: 500 concurrent users share 10 actual connections
select count(*) from pg_stat_activity;  -- 10 connections
```

PgBouncer pool modes:

- **Transaction mode**: connection returned after each transaction (best for most apps)
- **Session mode**: connection held for entire session (needed for prepared statements, temp tables)

Transaction mode is what makes pooling effective, but it breaks anything that assumes
session state survives between statements — named prepared statements, `SET` outside a
transaction, session-level advisory locks, `LISTEN`/`NOTIFY`, and temp tables. Use session
mode for those connections, or keep the state inside a single transaction.

Reference: [Number of database connections](https://wiki.postgresql.org/wiki/Number_Of_Database_Connections)
