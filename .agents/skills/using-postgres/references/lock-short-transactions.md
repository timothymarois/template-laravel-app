## Keep Transactions Short to Reduce Lock Contention

Long-running transactions hold locks that block other queries. Keep transactions as short as possible.

**Incorrect (long transaction with external calls):**

```sql
begin;
select * from orders where id = 1 for update;  -- Lock acquired

-- Application makes HTTP call to payment API (2-5 seconds)
-- Other queries on this row are blocked!

update orders set status = 'paid' where id = 1;
commit;  -- Lock held for entire duration
```

**Correct (minimal transaction scope):**

```sql
-- Validate data and call APIs outside transaction
-- Application: response = await paymentAPI.charge(...)

-- Only hold lock for the actual update
begin;
update orders
set status = 'paid', payment_id = $1
where id = $2 and status = 'pending'
returning *;
commit;  -- Lock held for milliseconds
```

Use `statement_timeout` to prevent runaway transactions:

```sql
-- Abort statements running longer than 30 seconds, for the rest of this session
set statement_timeout = '30s';

-- Or only for the current transaction
set local statement_timeout = '5s';

-- A transaction left open while the application waits is caught by a different setting;
-- see conn-idle-timeout.md
set idle_in_transaction_session_timeout = '30s';
```

Reference: [Transaction Management](https://www.postgresql.org/docs/current/tutorial-transactions.html)
