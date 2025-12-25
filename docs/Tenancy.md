# Multi-Tenancy Documentation

This starter kit includes optional multi-tenancy support using the [stancl/tenancy](https://tenancyforlaravel.com/) package. This guide explains how to enable, configure, and use multi-tenancy in your application.

## Table of Contents

- [Overview](#overview)
- [Architecture](#architecture)
- [Setup](#setup)
- [Configuration](#configuration)
- [Creating Tenants](#creating-tenants)
- [User Management](#user-management)
- [Routes](#routes)
- [Migrations](#migrations)
- [Testing](#testing)
- [Production Considerations](#production-considerations)

## Overview

Multi-tenancy allows a single application to serve multiple customers (tenants) with isolated data. Each tenant gets:

- **Separate Database**: Complete data isolation per tenant
- **Unique Domain**: Access via subdomain (e.g., `acme.yourapp.com`) or custom domain
- **Isolated Users**: Tenant-specific users with roles and permissions

## Architecture

### Two-Tier User System

The system uses a two-tier user architecture:

```
Central Database                    Tenant Databases (per tenant)
├── users (CentralUser)             ├── users (Tenant User)
│   └── Can access multiple         │   └── central_user_id (FK)
│       tenants                     │   └── role (owner/admin/member)
├── tenants                         ├── ... (tenant data)
├── domains                         └── ...
└── tenant_user (pivot)
```

**CentralUser**: Exists in the central database. Can belong to multiple tenants. Used for authentication and tenant selection.

**Tenant User**: Exists in each tenant's database. Linked to a CentralUser via `central_user_id`. Contains tenant-specific data like roles.

### User Flow

1. User logs in at `yourapp.com` (central domain)
2. User sees tenant selector with available tenants
3. User selects tenant → redirected to `tenant.yourapp.com`
4. User works within tenant context as a Tenant User

## Setup

### Enabling Multi-Tenancy

Run the setup command:

```bash
php artisan tenancy:setup
```

This command will:
1. Install the `stancl/tenancy` package via Composer
2. Create necessary models (Tenant, Domain, CentralUser, etc.)
3. Set up migrations for central and tenant databases
4. Configure routes and middleware
5. Update your User model to CentralUser

### Post-Setup Steps

1. **Configure Environment**

   Add to your `.env`:
   ```env
   APP_DOMAIN=yourapp.com
   TENANCY_ENABLED=true
   TENANCY_DB_PREFIX=tenant_
   ```

2. **Run Migrations**

   ```bash
   php artisan migrate
   ```

3. **Configure Database User Permissions**

   Ensure your database user has `CREATE DATABASE` privileges for creating tenant databases.

## Configuration

Configuration is stored in `config/tenancy.php`:

### Central Domains

```php
'central_domains' => [
    env('APP_DOMAIN', 'localhost'),
],
```

### Database Settings

```php
'database' => [
    'central_connection' => env('DB_CONNECTION', 'mysql'),
    'prefix' => env('TENANCY_DB_PREFIX', 'tenant_'),
    'suffix' => '',
],
```

### Bootstrappers

Bootstrappers set up the tenant environment:

```php
'bootstrappers' => [
    DatabaseTenancyBootstrapper::class,    // Switches database
    CacheTenancyBootstrapper::class,       // Isolates cache
    FilesystemTenancyBootstrapper::class,  // Separates storage
    QueueTenancyBootstrapper::class,       // Tenant-aware queues
],
```

## Creating Tenants

### Basic Tenant Creation

```php
use App\Models\Tenant;
use App\Models\CentralUser;

// Get or create the owner
$owner = CentralUser::find(1);

// Create the tenant
$tenant = Tenant::create([
    'company_name' => 'Acme Corporation',
    'owner_user_id' => $owner->id,
    'timezone' => 'America/New_York',
]);

// Create a domain for the tenant
$tenant->domains()->create([
    'domain' => 'acme', // Results in acme.yourapp.com
]);

// Attach the owner to the tenant
$owner->tenants()->attach($tenant->id, ['is_active' => true]);
```

### Custom Domains

```php
// Add a custom domain
$tenant->domains()->create([
    'domain' => 'acme.com', // Full custom domain
]);
```

### Programmatic Tenant Access

```php
// Run code in tenant context
$tenant->run(function () {
    // This code runs with the tenant's database
    $users = \App\Models\Tenant\User::all();
});
```

## User Management

### Adding Users to a Tenant

```php
use App\Models\CentralUser;
use App\Models\Tenant;

$centralUser = CentralUser::find(1);
$tenant = Tenant::find('tenant-uuid');

// Attach central user to tenant
$centralUser->tenants()->attach($tenant->id, ['is_active' => true]);

// Create tenant user within tenant context
$tenant->run(function () use ($centralUser) {
    \App\Models\Tenant\User::create([
        'central_user_id' => $centralUser->id,
        'name' => $centralUser->name,
        'email' => $centralUser->email,
        'password' => $centralUser->password,
        'role' => 'member',
    ]);
});
```

### User Roles

Tenant users have a `role` field. Common roles:

- `owner` - Full access, created the tenant
- `admin` - Administrative access
- `member` - Standard access

```php
// Check roles
if ($user->isOwner()) { ... }
if ($user->isAdmin()) { ... }
if ($user->hasRole('member')) { ... }
```

## Routes

### Central Routes (`routes/web.php`)

These routes are accessible on your central domain:

```php
// Landing page, registration, login
Route::get('/', [PageController::class, 'home']);
Route::get('/login', [SessionController::class, 'create']);

// Tenant selection (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/tenants', [TenantSelectorController::class, 'index']);
});
```

### Tenant Routes (`routes/tenant.php`)

These routes are accessible on tenant domains:

```php
Route::middleware([
    'web',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    // Tenant dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Tenant-specific resources
    Route::resource('projects', ProjectController::class);
});
```

## Migrations

### Central Migrations

Located in `database/migrations/`:
- User authentication tables
- `tenants` table
- `domains` table
- `tenant_user` pivot table

### Tenant Migrations

Located in `database/migrations/tenant/`:
- Tenant-specific `users` table (with `central_user_id`)
- Tenant-specific `personal_access_tokens`
- Any tenant-specific data tables

### Creating Tenant Migrations

```bash
# Create a new tenant migration
php artisan make:migration create_projects_table --path=database/migrations/tenant
```

### Running Tenant Migrations

```bash
# Migrate all tenants
php artisan tenants:migrate

# Migrate specific tenant
php artisan tenants:migrate --tenants=tenant-uuid

# Rollback tenant migrations
php artisan tenants:migrate:rollback
```

## Testing

### Testing Central Features

```php
public function test_user_can_select_tenant(): void
{
    $user = CentralUser::factory()->create();
    $tenant = Tenant::factory()->create(['owner_user_id' => $user->id]);
    $user->tenants()->attach($tenant);

    $this->actingAs($user)
        ->get('/tenants')
        ->assertSee($tenant->company_name);
}
```

### Testing Tenant Features

```php
public function test_tenant_dashboard(): void
{
    $tenant = Tenant::factory()->create();

    $tenant->run(function () {
        $user = \App\Models\Tenant\User::factory()->create();

        $this->actingAs($user, 'tenant')
            ->get('/dashboard')
            ->assertOk();
    });
}
```

## Production Considerations

### Database Permissions

Ensure your database user can create databases:

```sql
-- MySQL
GRANT CREATE, ALTER, DROP ON *.* TO 'your_user'@'localhost';

-- PostgreSQL
ALTER USER your_user CREATEDB;
```

### Queue Workers

Configure queue workers to handle tenant context:

```bash
# The tenancy package automatically handles tenant context in queued jobs
php artisan queue:work
```

### DNS Configuration

For subdomain tenancy, configure a wildcard DNS record:

```
*.yourapp.com → your-server-ip
```

### SSL Certificates

Use a wildcard SSL certificate for subdomains:

```
*.yourapp.com
```

For custom domains, consider using Let's Encrypt with automatic certificate provisioning.

### Caching

The `CacheTenancyBootstrapper` automatically prefixes cache keys per tenant. No additional configuration needed.

### File Storage

The `FilesystemTenancyBootstrapper` creates tenant-specific storage directories:

```
storage/tenant_{tenant_id}/
```

## Disabling Tenancy

To temporarily disable tenancy without removing configuration:

```env
TENANCY_ENABLED=false
```

To completely remove tenancy:

1. Remove `config/tenancy.php`
2. Remove tenant-related models and migrations
3. Rename `CentralUser.php` back to `User.php`

## Resources

- [Tenancy for Laravel Documentation](https://tenancyforlaravel.com/docs)
- [GitHub Repository](https://github.com/stancl/tenancy)
