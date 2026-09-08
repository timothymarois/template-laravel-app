# Modelling Sensitive Data

Credentials, PII, tenant isolation, permissions, audit trails, immutability, optimistic locking,
retention, and constraints that carry a business rule. These are modelling decisions — what the
tables are and what the database refuses to store — not query defence. For injection and parameter
binding see `using-sqlite`; for `GRANT`, roles, and row-level security enforced by the server see
`using-postgres` and `using-mysql`.

DDL is SQLite dialect; the shapes are portable and the spellings are not.

## Credentials

```sql
CREATE TABLE users (
    id                  INTEGER PRIMARY KEY,
    email               TEXT NOT NULL UNIQUE,
    password_hash       TEXT NOT NULL,       -- Argon2id or bcrypt, algorithm and cost embedded
    password_changed_at TEXT NOT NULL DEFAULT (datetime('now')),
    failed_attempts     INTEGER NOT NULL DEFAULT 0 CHECK (failed_attempts >= 0),
    locked_until        TEXT
);
```

- **The column is `password_hash`, and its absence of a plaintext sibling is the point.** A schema
  with a `password` column has already lost, whatever the application does with it.
- **Store the full modular hash string** (`$argon2id$v=19$m=...$...`), not the raw digest. It carries
  the algorithm, the cost parameters, and the salt, which is what lets you re-hash on login when you
  raise the cost later. A bare digest column cannot be migrated without every user's password.
- **Never a bare SHA or MD5, never an application-wide salt.** Fast hashes are the wrong tool; the
  slowness is the feature.
- **Lockout is a policy, and policy belongs in code.** A trigger that sets `locked_until` when
  `failed_attempts` crosses a threshold reads neatly and is a poor place for it: it fires on every
  update path including administrative ones, it is invisible to whoever debugs the login flow, and
  changing the threshold becomes a migration. Model the columns; decide in the authentication code.

Sessions and tokens are credentials too. Store a hash of the token, not the token — a leaked session
table should not be usable.

```sql
CREATE TABLE sessions (
    id           INTEGER PRIMARY KEY,
    user_id      INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    token_hash   TEXT NOT NULL UNIQUE,       -- SHA-256 of the bearer token is fine here
    expires_at   TEXT NOT NULL,
    created_at   TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX idx_sessions_expiry ON sessions(expires_at);
```

A random 256-bit token does not need a slow hash — there is no dictionary to attack. A user-chosen
password does.

## PII

Encrypting a column at the application layer costs you every query against it. Model that cost
explicitly rather than discovering it.

```sql
CREATE TABLE customers (
    id               INTEGER PRIMARY KEY,
    email_ciphertext BLOB NOT NULL,     -- encrypted by the application
    email_hash       TEXT NOT NULL,     -- deterministic HMAC, for equality lookup only
    name_ciphertext  BLOB NOT NULL,
    address_ciphertext BLOB,
    created_at       TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE UNIQUE INDEX idx_customers_email_hash ON customers(email_hash);
```

- **An encrypted column supports no range, prefix, or sort.** The lookup hash restores equality and
  nothing else. `WHERE email LIKE 'a%'` is gone, and so is ordering by name.
- **Use a keyed HMAC, not a plain hash, for the lookup column.** An unkeyed `SHA-256(email)` is
  trivially reversed for any address someone can guess.
- **The lookup hash leaks equality by design.** Two rows with the same hash have the same email —
  that is the feature, and it is also a correlation an attacker with the table gets for free.
- **The key does not live in the database.** A key stored beside the ciphertext protects against a
  stolen backup file and nothing else.

Not everything needs to be stored. Tokenization removes the question:

```sql
CREATE TABLE payment_methods (
    id           INTEGER PRIMARY KEY,
    user_id      INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    provider_token TEXT NOT NULL,      -- opaque, issued by the payment processor
    last_four    TEXT NOT NULL CHECK (length(last_four) = 4),
    brand        TEXT NOT NULL,
    expiry_month INTEGER NOT NULL CHECK (expiry_month BETWEEN 1 AND 12),
    expiry_year  INTEGER NOT NULL
);
-- Never stored, under any encryption: full card number, CVV, PIN, magnetic stripe data.
```

The columns above are what a user interface needs to say "Visa ending 4242". The card number is the
processor's problem, and keeping it out of the schema keeps it out of your backups, your logs, and
your compliance scope.

## Tenant Isolation

```sql
CREATE TABLE documents (
    id        INTEGER PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id),
    title     TEXT NOT NULL,
    content   TEXT
);

-- tenant_id leads every index, because it leads every query.
CREATE INDEX idx_documents_tenant ON documents(tenant_id, id DESC);
```

- **`tenant_id` is the first column of the primary access path**, not an afterthought bolted onto
  the end of an existing index.
- **Every unique constraint is scoped to the tenant.** `UNIQUE(slug)` is a bug in a multi-tenant
  schema — it lets one tenant's data collide with another's. `UNIQUE(tenant_id, slug)`.
- **Filtering in application code is one forgotten `WHERE` away from a cross-tenant leak.** The
  database can enforce this itself: PostgreSQL row-level security in `using-postgres`, and views
  or per-tenant credentials elsewhere. Application filtering is the weakest of the options, not the
  default.
- **A join can leak what the base table protects.** Filtering `documents` by tenant but joining an
  unfiltered `comments` returns other tenants' comments. Every table in the query needs the
  predicate, which is the argument for enforcing it below the query rather than in it.

## Permissions

```sql
CREATE TABLE roles (
    id   INTEGER PRIMARY KEY,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE permissions (
    id       INTEGER PRIMARY KEY,
    resource TEXT NOT NULL,
    action   TEXT NOT NULL,
    UNIQUE (resource, action)
);

CREATE TABLE role_permissions (
    role_id       INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id INTEGER NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, permission_id)
);

CREATE TABLE user_roles (
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (user_id, role_id)
);

SELECT EXISTS (
    SELECT 1
    FROM user_roles ur
    JOIN role_permissions rp ON rp.role_id = ur.role_id
    JOIN permissions p ON p.id = rp.permission_id
    WHERE ur.user_id = :user_id AND p.resource = :resource AND p.action = :action
);
```

`resource` and `action` as separate columns, rather than one `'documents:write'` string, because you
will want to ask "everything on documents" and string prefixes are not an access-control mechanism.

This models *what a role may do*, not *which rows*. Row-scoped permission — "may edit documents they
own" — is an ownership column plus a predicate, and combining the two is where these systems get
their complexity. Decide early which questions you need to answer.

## Audit Trails

An audit log answers "who changed this, and when". A version history answers "what did it say in
March". They are different tables and different questions; see the temporal patterns in
[advanced-patterns.md](advanced-patterns.md) for the second.

```sql
CREATE TABLE audit_log (
    id          INTEGER PRIMARY KEY,
    table_name  TEXT NOT NULL,
    row_id      INTEGER NOT NULL,
    action      TEXT NOT NULL CHECK (action IN ('INSERT', 'UPDATE', 'DELETE')),
    changed_columns TEXT,               -- JSON: {"price_cents": [1200, 1500]}
    actor_id    TEXT,                   -- user id, or a service name; never NULL for a real change
    occurred_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE INDEX idx_audit_row ON audit_log(table_name, row_id, occurred_at DESC);
CREATE INDEX idx_audit_actor ON audit_log(actor_id, occurred_at DESC);
```

Three decisions the shape encodes:

- **Changed columns, not whole rows.** Storing the full before and after JSON turns the audit log
  into a second copy of the table — including its sensitive columns, with none of its access
  controls, surviving every deletion from the table it shadows. Record the fields that changed.
- **The actor comes from the application.** A trigger sees the row but not the person; the database
  connection is usually a service account. Either pass the actor in (a session variable in
  PostgreSQL and MySQL, an application-set value in SQLite) or write the audit row from the
  application in the same transaction as the change.
- **Append-only, and enforced.** An audit log an application can update is not evidence.

Triggers are the durable way to catch every writer, including migrations and manual fixes:

```sql
CREATE TRIGGER audit_users_update AFTER UPDATE ON users
WHEN old.email <> new.email OR old.name <> new.name
BEGIN
    INSERT INTO audit_log (table_name, row_id, action, changed_columns, actor_id)
    VALUES ('users', new.id, 'UPDATE',
            json_object(
                'email', CASE WHEN old.email <> new.email
                              THEN json_array(old.email, new.email) END,
                'name',  CASE WHEN old.name <> new.name
                              THEN json_array(old.name, new.name) END
            ),
            (SELECT value FROM app_context WHERE key = 'actor_id'));
END;
```

The `WHEN` clause matters: without it every no-op `UPDATE` writes an audit row, and the log fills
with changes that changed nothing.

Note the trade the trigger makes — it cannot be bypassed, and it cannot see the actor without help.
Writing the audit row from the application sees the actor and is bypassed by anything that does not
go through the application. Pick per table, knowing which risk you took.

```sql
CREATE TABLE security_events (
    id          INTEGER PRIMARY KEY,
    event_type  TEXT NOT NULL,          -- login_failure, permission_denied, data_export, …
    severity    TEXT NOT NULL CHECK (severity IN ('info', 'warning', 'critical')),
    user_id     INTEGER REFERENCES users(id),
    description TEXT NOT NULL,
    occurred_at TEXT NOT NULL DEFAULT (datetime('now'))
);
CREATE INDEX idx_security_events_type ON security_events(event_type, occurred_at DESC);
```

Security events are separate from the change audit because they are read by different people, in
different circumstances, and usually retained for a different period.

## Immutability

```sql
CREATE TABLE ledger_entries (
    id         INTEGER PRIMARY KEY,
    account_id INTEGER NOT NULL REFERENCES accounts(id),
    amount_cents INTEGER NOT NULL,
    kind       TEXT NOT NULL,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TRIGGER ledger_no_update BEFORE UPDATE ON ledger_entries
BEGIN
    SELECT RAISE(ABORT, 'ledger entries are immutable');
END;

CREATE TRIGGER ledger_no_delete BEFORE DELETE ON ledger_entries
BEGIN
    SELECT RAISE(ABORT, 'ledger entries cannot be deleted');
END;
```

A mistake is corrected with a compensating entry, not an edit. That is what makes the balance a
function of the log rather than a number someone can adjust.

Money is `INTEGER` minor units, or the engine's exact decimal type. Never a float: `0.1 + 0.2` is
not `0.3`, and a ledger that does not sum is worse than no ledger.

These triggers stop the application. They do not stop someone with a direct connection, who can drop
the trigger. Real immutability is a permissions question — see the engine skills.

## Optimistic Locking

```sql
CREATE TABLE documents (
    id         INTEGER PRIMARY KEY,
    title      TEXT NOT NULL,
    content    TEXT,
    version    INTEGER NOT NULL DEFAULT 1,
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
```

```sql
UPDATE documents
SET title = :title, content = :content, version = version + 1, updated_at = datetime('now')
WHERE id = :id AND version = :version_the_client_loaded;
```

Zero rows affected means someone else wrote first. The application decides — refuse, merge, or
re-present — but it must check, and an `UPDATE` whose row count is ignored is the bug this pattern
exists to prevent.

- One integer, no locks held, and it works across a stateless request boundary where a
  `SELECT ... FOR UPDATE` cannot.
- The version must be part of the same statement as the write. Reading it, comparing it in code, and
  then updating is the race you were trying to close.
- A timestamp works in place of the counter only if it has enough resolution to distinguish two
  writes; a counter always does.

Pessimistic locking, isolation levels, and lock ordering are engine behaviour — `using-postgres`
and `using-mysql`.

## Retention and Erasure

```sql
CREATE TABLE messages (
    id          INTEGER PRIMARY KEY,
    content     TEXT NOT NULL,
    created_at  TEXT NOT NULL DEFAULT (datetime('now')),
    deleted_at  TEXT,                   -- soft delete: retired, still present
    purge_after TEXT                    -- hard delete: due for real removal
);

CREATE VIEW active_messages AS SELECT * FROM messages WHERE deleted_at IS NULL;
CREATE INDEX idx_messages_purge ON messages(purge_after) WHERE purge_after IS NOT NULL;
```

Two separate ideas in two columns: `deleted_at` retires a row from the application's view,
`purge_after` schedules its actual removal. Conflating them means either the data never leaves or
the user cannot undo.

- **A retention policy nobody runs is not a policy.** The purge job is part of the design.
- **Soft-deleted rows still hold their unique constraints.** A retired account keeps its email, and
  the next signup with that address fails with a constraint error nobody expects. Decide up front:
  release the value on delete, or scope uniqueness to the live rows
  (`CREATE UNIQUE INDEX ... WHERE deleted_at IS NULL`).

Erasure requests are the case a flag cannot satisfy:

```sql
UPDATE users
SET email = 'erased+' || id || '@invalid',
    name = 'Erased user',
    erased_at = datetime('now')
WHERE id = :id;
```

Anonymizing keeps the foreign keys intact where a hard delete would break every order, comment, and
ledger entry that references the row. Note what still has to be handled: the audit log, the backups,
and any denormalized copy of the name you created earlier. An erasure that leaves the address in
three other tables did not happen.

## Constraints as Business Rules

```sql
CREATE TABLE orders (
    id         INTEGER PRIMARY KEY,
    user_id    INTEGER NOT NULL REFERENCES users(id),
    status     TEXT NOT NULL DEFAULT 'pending'
               CHECK (status IN ('pending', 'paid', 'shipped', 'delivered', 'cancelled')),
    total_cents INTEGER NOT NULL CHECK (total_cents >= 0),
    paid_at    TEXT,
    shipped_at TEXT,

    -- The status and its timestamps cannot disagree.
    CHECK (
        (status = 'pending'                    AND paid_at IS NULL     AND shipped_at IS NULL) OR
        (status = 'paid'                       AND paid_at IS NOT NULL AND shipped_at IS NULL) OR
        (status IN ('shipped', 'delivered')    AND paid_at IS NOT NULL AND shipped_at IS NOT NULL) OR
        (status = 'cancelled')
    )
);
```

The rule is enforced against every writer — the application, a migration, a support script, a person
at a prompt. That is the whole argument for putting it here rather than only in code.

Two limits worth knowing before you lean on it. A `CHECK` cannot express a transition rule — it sees
the new row, not the old one, so "paid orders cannot go back to pending" needs a trigger or the
application. And a `CHECK` this size is a migration every time the workflow gains a state; encode
the rules that are genuinely invariant, and leave the ones that change often to code that is easier
to change.
