## Optimize RLS Policies for Performance

Poorly written RLS policies can cause severe performance issues. Use subqueries and indexes strategically.

The current user's identity comes from a setting the application sets in each transaction (see
[security-rls-basics](security-rls-basics.md) for why it must be transaction-local). Reading it is a
function call, and where that call sits in the policy decides whether it runs once or once per row.

**Incorrect (function called for every row):**

```sql
create policy orders_policy on orders
  using (nullif(current_setting('app.current_user_id', true), '')::bigint = user_id);  -- called per row!

-- With 1M rows, current_setting() is called 1M times
```

**Correct (wrap functions in SELECT):**

```sql
create policy orders_policy on orders
  using ((select nullif(current_setting('app.current_user_id', true), '')::bigint) = user_id);  -- called once

-- 100x+ faster on large tables
```

The subquery is what does the work: it makes the call an InitPlan the planner evaluates once for
the whole statement, instead of an expression re-evaluated for every candidate row. This holds for
any stable function in a policy whose arguments do not reference the row. A subquery that passes a
column of the row — `(select f(team_id))` — is correlated, so it still runs per row.

Use security definer functions for complex checks:

`SECURITY DEFINER` functions run with the creator's privileges and bypass RLS on any tables they touch — which is what makes them useful for internal lookups, but also what makes them dangerous if misused. Always include an explicit check of the calling user's identity inside the function body, keep them in a non-exposed schema, and revoke `EXECUTE` from any role that shouldn't call them directly.

```sql
-- Create helper function in a schema the API does not expose
create schema if not exists private;
create or replace function private.is_team_member(team_id bigint)
returns boolean
language sql
security definer
set search_path = ''
as $$
  select exists (
    select 1 from public.team_members
    -- always check the calling user's identity inside the function
    where team_id = $1
      and user_id = (select nullif(current_setting('app.current_user_id', true), '')::bigint)
  );
$$;

-- Functions are executable by PUBLIC by default. Revoke that, then grant only the roles the policy
-- applies to: a policy expression runs with the privileges of the user running the query, so those
-- roles need USAGE on the schema and EXECUTE on the function, or every query fails with
-- "permission denied for function".
revoke execute on function private.is_team_member(bigint) from public;
grant usage on schema private to app_user;
grant execute on function private.is_team_member(bigint) to app_user;

-- Use in policy. It runs once per candidate row (it depends on team_id), so keep the lookup cheap:
-- index team_members (team_id, user_id).
create policy team_orders_policy on orders
  for all
  to app_user
  using (private.is_team_member(team_id));
```

Always add indexes on columns used in RLS policies:

```sql
create index orders_user_id_idx on orders (user_id);
create index team_members_team_user_idx on team_members (team_id, user_id);
```

Reference: [Row Security Policies](https://www.postgresql.org/docs/current/ddl-rowsecurity.html)
