## Enable Row Level Security for Multi-Tenant Data

Row Level Security (RLS) enforces data access at the database level, ensuring users only see their own data.

**Incorrect (application-level filtering only):**

```sql
-- Relying only on application to filter
select * from orders where user_id = $current_user_id;

-- Bug or bypass means all data is exposed!
select * from orders;  -- Returns ALL orders
```

**Correct (database-enforced RLS):**

```sql
-- Enable RLS on the table
alter table orders enable row level security;

-- Force RLS even for the table owner, which otherwise bypasses every policy
alter table orders force row level security;

-- Scope the policy to the role the application connects as
create policy orders_user_policy on orders
  for all
  to app_user
  using (user_id = nullif(current_setting('app.current_user_id', true), '')::bigint);
```

`app_user` is an ordinary role you create; the application connects as it and sets
`app.current_user_id` inside each transaction, as shown below, before running any query.

The second argument to `current_setting` is `missing_ok`. With `true`, a connection that never
set the value gets NULL rather than an error. Once a transaction-local value has been discarded, the
same call returns an empty string instead, which `::bigint` rejects — `nullif(…, '')` turns both
cases into NULL, and NULL in `using` makes the row invisible: the connection sees nothing instead of
failing open.

**Set the value per transaction. Behind a transaction-mode pooler, a bare `SET` is a tenant leak.** `set app.current_user_id`
outside a transaction is session state, so the pooler hands that value to whatever client gets the
connection next — and every policy above then filters for the wrong user. Set it inside the
transaction that uses it, with `is_local` true, so it is discarded at commit:

```sql
begin;
select set_config('app.current_user_id', '123', true);  -- true = local to this transaction
select * from orders;
commit;
```

See [conn-pooling](conn-pooling.md) for which session state survives each pool mode.

Reference: [Row Security Policies](https://www.postgresql.org/docs/current/ddl-rowsecurity.html)
