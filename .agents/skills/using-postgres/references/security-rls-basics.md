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

-- Create policy for users to see only their orders
create policy orders_user_policy on orders
  for all
  using (user_id = current_setting('app.current_user_id')::bigint);

-- Force RLS even for table owners
alter table orders force row level security;

-- Set user context and query
set app.current_user_id = '123';
select * from orders;  -- Only returns orders for user 123
```

Scope the policy to the roles that should be subject to it. `app_user` and `app_admin` here
are ordinary roles you create; the application connects as one of them and sets
`app.current_user_id` on the connection before running any query.

```sql
create policy orders_user_policy on orders
  for all
  to app_user
  using (user_id = current_setting('app.current_user_id', true)::bigint);
```

The second argument to `current_setting` is `missing_ok`. With `true`, a connection that never
set the value gets NULL rather than an error, and NULL in `using` makes the row invisible — the
connection sees nothing instead of failing open.

**Behind a transaction-mode pooler, a bare `SET` is a tenant leak.** `set app.current_user_id`
outside a transaction is session state, so the pooler hands that value to whatever client gets the
connection next — and every policy above then filters for the wrong user. Set it inside the
transaction that uses it, with `is_local` true, so it is discarded at commit:

```sql
begin;
select set_config('app.current_user_id', '123', true);  -- true = local to this transaction
select * from orders;
commit;
```

See `conn-pooling.md` for which session state survives each pool mode.

Reference: [Row Security Policies](https://www.postgresql.org/docs/current/ddl-rowsecurity.html)
