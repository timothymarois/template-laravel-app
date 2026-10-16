## Set Appropriate Connection Limits

Too many connections exhaust memory and degrade performance. Set limits based on available resources.

**Incorrect (unlimited or excessive connections):**

```sql
-- Default max_connections = 100, but often increased blindly
show max_connections;  -- 500 (way too high for 4GB RAM)

-- An idle backend costs a few MB; an active one can use work_mem several times over
-- (once per sort or hash node), so 500 busy connections can exhaust 4GB
-- Out of memory errors under load
```

**Correct (calculate based on resources):**

```sql
-- Size from memory, not from peak client count: put a pooler in front and keep
-- max_connections near what the CPUs can run concurrently
alter system set max_connections = 100;  -- needs a restart

-- work_mem is per sort/hash operation, so budget: max_connections * work_mem * a few
-- operations per query must fit beside shared_buffers
alter system set work_mem = '8MB';
```

Monitor connection usage:

```sql
select count(*), state from pg_stat_activity group by state;
```

Reference: [Connection Settings](https://www.postgresql.org/docs/current/runtime-config-connection.html)
