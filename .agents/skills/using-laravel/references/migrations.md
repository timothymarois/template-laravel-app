# Migration Best Practices

## Generate Migrations with Artisan

Always use `php artisan make:migration` for consistent naming and timestamps.

Incorrect (manually created file):
```php
// database/migrations/posts_migration.php  ← wrong naming, no timestamp
```

Correct (Artisan-generated):
```bash
php artisan make:migration create_posts_table
php artisan make:migration add_slug_to_posts_table
```

## Use `constrained()` for Foreign Keys

Automatic naming and referential integrity.

```php
$table->foreignId('user_id')->constrained()->cascadeOnDelete();

// Non-standard names
$table->foreignId('author_id')->constrained('users');
```

## Never Modify Deployed Migrations

Once a migration has run in production, treat it as immutable. Create a new migration to change the table.

Incorrect (editing a deployed migration):
```php
// 2024_01_01_create_posts_table.php — already in production
$table->string('slug')->unique(); // ← added after deployment
```

Correct (new migration to alter):
```php
// 2024_03_15_add_slug_to_posts_table.php
Schema::table('posts', function (Blueprint $table) {
    $table->string('slug')->unique()->after('title');
});
```

## Add Proven Indexes in the Migration

Declare an approved index with the schema change when its representative query plan needs it. Do not
index every filtered or joined column by checklist; indexes add write and storage cost, and foreign
key conventions or the database may already supply one.

Incorrect:
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('status');
    $table->timestamps();
});
```

Illustrative candidates — keep only those supported by actual access paths:
```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->index()->constrained();
    $table->string('status')->index();
    $table->timestamp('shipped_at')->nullable()->index();
    $table->timestamps();
});
```

## Mirror Defaults in Model `$attributes`

When unsaved model instances must expose a database default, mirror it in the model. Otherwise keep
one database-owned default instead of two values that can drift.

```php
// Migration
$table->string('status')->default('pending');

// Model
protected $attributes = [
    'status' => 'pending',
];
```

## Write Reversible `down()` Methods by Default

Implement `down()` for schema changes that can be safely reversed so `migrate:rollback` works in CI and failed deployments.

```php
public function down(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->dropColumn('slug');
    });
}
```

For intentionally irreversible migrations (e.g., destructive data backfills), leave a clear comment and require a forward fix migration instead of pretending rollback is supported.

## Keep Migrations Focused

Keep each migration coherent and safely deployable. Split DDL and data movement when their locking,
transaction, duration, resumability, or rollback paths differ; do not split them only by category
when one atomic, bounded migration is safer.

Risky when the engine cannot keep both operations atomic:
```php
public function up(): void
{
    Schema::create('settings', function (Blueprint $table) { ... });
    DB::table('settings')->insert(['key' => 'version', 'value' => '1.0']);
}
```

Often safer (separate migrations):
```php
// Migration 1: create_settings_table
Schema::create('settings', function (Blueprint $table) { ... });

// Migration 2: seed_default_settings
DB::table('settings')->insert(['key' => 'version', 'value' => '1.0']);
```

## Review Schema Changes as Production Operations

Before changing a populated table, inspect the database engine's locking, online-DDL support,
duration, rollback path, and release sequence.

Incorrect — assuming a concise migration is operationally cheap:

```php
Schema::table('orders', function (Blueprint $table) {
    $table->index(['account_id', 'created_at']);
});
```

Correct — first prove the index matches the representative query plan and choose the engine's safe
online operation when the table size requires it. Generate the migration with Artisan so its name
and timestamp follow project tooling.

## Keep Defaults and Rollbacks Honest

Mirror a database default in model `$attributes` only when unsaved model instances must expose the
same value. Otherwise the application has two defaults that can drift.

Do not write a `down()` method that pretends deleted or transformed user data can be restored:

```php
public function down(): void
{
    throw new RuntimeException('This data migration is intentionally irreversible.');
}
```

Use the repository's irreversible-migration convention when it has one. For safely reversible
schema changes, keep a real `down()` path.
