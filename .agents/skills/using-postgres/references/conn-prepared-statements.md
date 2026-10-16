## Use Prepared Statements Correctly with Pooling

Prepared statements are tied to individual database connections. In transaction-mode pooling, connections are shared, so a statement prepared on one server connection is missing on the next — unless the pooler tracks prepared statements itself. PgBouncer 1.21+ does this for protocol-level prepared statements when `max_prepared_statements` is set above 0; SQL-level `PREPARE`/`EXECUTE` is still not tracked.

**Incorrect (named prepared statements with transaction pooling):**

```sql
-- Named prepared statement
prepare get_user as select * from users where id = $1;

-- In transaction mode pooling, next request may get different connection
execute get_user(123);
-- ERROR: prepared statement "get_user" does not exist
```

**Correct (pooler support, unnamed statements, or session mode):**

```ini
# Option 1: PgBouncer 1.21+ in transaction mode tracks protocol-level prepared statements
max_prepared_statements = 200
```

```sql
-- Option 2: Use unnamed protocol-level statements, which are parsed and executed together
-- and never reused across transactions

-- Option 3: Deallocate inside the same transaction
prepare get_user as select * from users where id = $1;
execute get_user(123);
deallocate get_user;

-- Option 4: Use session mode pooling (PgBouncer: pool_mode = session)
-- Connection is held for entire session, prepared statements persist
```

Check your driver settings:

```sql
-- Many drivers use named prepared statements by default
-- postgres.js (`postgres` package): { prepare: false } to disable
-- node-postgres (`pg`): statements are named only when the query config passes a `name`
-- JDBC: prepareThreshold=0 to disable
```

Reference: [PREPARE](https://www.postgresql.org/docs/current/sql-prepare.html)
