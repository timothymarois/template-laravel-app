## Optimize RLS Policies for Performance

Poorly written RLS policies can cause severe performance issues. Use subqueries and indexes strategically.

The current user's identity comes from a session setting the application sets on the connection
before it runs anything — `set app.current_user_id = '...'`. Reading it is a function call, and
where that call sits in the policy decides whether it runs once or once per row.

**Incorrect (function called for every row):**

```sql
create policy orders_policy on orders
  using (current_setting('app.current_user_id', true)::bigint = user_id);  -- called per row!

-- With 1M rows, current_setting() is called 1M times
```

**Correct (wrap functions in SELECT):**

```sql
create policy orders_policy on orders
  using ((select current_setting('app.current_user_id', true)::bigint) = user_id);  -- called once

-- 100x+ faster on large tables
```

The subquery is what does the work: it makes the call an InitPlan the planner evaluates once for
the whole statement, instead of an expression re-evaluated for every candidate row. This holds for
any stable function in a policy, not just this one.

Use security definer functions for complex checks:

`SECURITY DEFINER` functions run with the creator's privileges and bypass RLS on any tables they touch — which is what makes them useful for internal lookups, but also what makes them dangerous if misused. Always include an explicit check of the calling user's identity inside the function body, keep them in a non-exposed schema, and revoke `EXECUTE` from any role that shouldn't call them directly.

```sql
-- Create helper function in a private schema
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
      and user_id = (select current_setting('app.current_user_id', true)::bigint)
  );
$$;

-- Revoke direct execution, then grant it back only where it is needed
revoke execute on function private.is_team_member(bigint) from PUBLIC, app_user, app_admin;

-- Use in policy (indexed lookup, not per-row check)
create policy team_orders_policy on orders
  using ((select private.is_team_member(team_id)));
```

Always add indexes on columns used in RLS policies:

```sql
create index orders_user_id_idx on orders (user_id);
```

Reference: [Row Security Policies](https://www.postgresql.org/docs/current/ddl-rowsecurity.html)
