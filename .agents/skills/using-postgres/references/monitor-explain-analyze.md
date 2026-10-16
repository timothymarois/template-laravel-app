## Use EXPLAIN ANALYZE to Diagnose Slow Queries

EXPLAIN ANALYZE executes the query and shows actual timings, revealing the true performance bottlenecks.

**Incorrect (guessing at performance issues):**

```sql
-- Query is slow, but why?
select * from orders where customer_id = 123 and status = 'pending';
-- "It must be missing an index" - but which one?
```

**Correct (use EXPLAIN ANALYZE):**

```sql
explain (analyze, buffers, format text)
select * from orders where customer_id = 123 and status = 'pending';

-- Output reveals the issue:
-- Seq Scan on orders (cost=0.00..25000.00 rows=50 width=100) (actual time=0.015..450.123 rows=50 loops=1)
--   Filter: ((customer_id = 123) AND (status = 'pending'::text))
--   Rows Removed by Filter: 999950
--   Buffers: shared hit=5000 read=15000
-- Planning Time: 0.150 ms
-- Execution Time: 450.500 ms
```

`analyze` executes the statement. Wrap an `insert`, `update`, or `delete` in `begin; … rollback;`
before explaining it, or it changes the data.

Key things to look for:

```sql
-- Seq Scan on a large table returning few rows = likely missing index
-- Rows Removed by Filter = poor selectivity or missing index
-- Buffers: read >> hit = data not cached (cold cache or working set larger than memory)
-- Nested Loop with high loops = consider different join strategy
-- Sort Method: external merge = work_mem too low
```

Reference: [Using EXPLAIN](https://www.postgresql.org/docs/current/using-explain.html)
