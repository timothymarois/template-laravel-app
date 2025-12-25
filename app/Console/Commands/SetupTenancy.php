<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\warning;

class SetupTenancy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenancy:setup
                            {--force : Overwrite existing tenancy configuration}
                            {--skip-composer : Skip installing the tenancy package}
                            {--rollback : Remove tenancy and reset the application}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up multi-tenancy support for this application';

    /**
     * The filesystem instance.
     */
    protected Filesystem $files;

    /**
     * The stub directory path.
     */
    protected string $stubPath;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->stubPath = base_path('stubs/tenancy');
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Prevent running in production
        if (app()->isProduction()) {
            error('This command cannot be run in production!');

            return self::FAILURE;
        }

        // Handle rollback
        if ($this->option('rollback')) {
            return $this->handleRollback();
        }

        $this->newLine();
        info('Setting up multi-tenancy support...');
        $this->newLine();

        // Check if stubs directory exists
        if (! $this->files->isDirectory($this->stubPath)) {
            error('Tenancy stubs not found. Please ensure stubs/tenancy directory exists.');

            return self::FAILURE;
        }

        // Check if already configured
        if ($this->isAlreadyConfigured() && ! $this->option('force')) {
            warning('Tenancy appears to be already configured.');

            if (! confirm('Do you want to overwrite the existing configuration?', false)) {
                info('Setup cancelled.');

                return self::SUCCESS;
            }
        }

        // Run setup steps
        $steps = [
            'installPackage' => 'Installing stancl/tenancy package',
            'createDirectories' => 'Creating directories',
            'publishModels' => 'Publishing models',
            'publishServices' => 'Publishing services',
            'publishJobs' => 'Publishing jobs',
            'publishMigrations' => 'Publishing migrations',
            'publishProvider' => 'Publishing service provider',
            'publishConfig' => 'Publishing configuration',
            'publishMiddleware' => 'Publishing middleware',
            'publishRoutes' => 'Publishing routes',
            'publishPages' => 'Publishing Vue pages',
            'publishController' => 'Publishing TenantController',
            'updateBootstrapProviders' => 'Updating bootstrap providers',
            'updateDatabaseConfig' => 'Updating database configuration',
            'convertUserModel' => 'Converting User model to CentralUser',
            'updateEnvExample' => 'Updating .env.example',
        ];

        $currentStep = 0;
        $totalSteps = count($steps);

        foreach ($steps as $method => $description) {
            $currentStep++;
            $prefix = "[{$currentStep}/{$totalSteps}]";

            if ($method === 'installPackage' && $this->option('skip-composer')) {
                note("{$prefix} Skipping: {$description}");

                continue;
            }

            $result = spin(
                fn () => $this->{$method}(),
                "{$prefix} {$description}..."
            );

            if ($result === false) {
                error("Failed: {$description}");

                return self::FAILURE;
            }
        }

        $this->newLine();
        info('Multi-tenancy setup complete!');
        $this->newLine();

        $this->displayNextSteps();

        return self::SUCCESS;
    }

    /**
     * Check if tenancy is already configured.
     */
    protected function isAlreadyConfigured(): bool
    {
        return $this->files->exists(config_path('tenancy.php'))
            || $this->files->exists(app_path('Models/Tenant.php'));
    }

    /**
     * Install the tenancy package.
     */
    protected function installPackage(): bool
    {
        // Check if package is already installed
        $composerJson = json_decode($this->files->get(base_path('composer.json')), true);
        $packages = array_merge(
            $composerJson['require'] ?? [],
            $composerJson['require-dev'] ?? []
        );

        if (isset($packages['stancl/tenancy'])) {
            return true;
        }

        // Install the package
        $process = proc_open(
            'composer require stancl/tenancy',
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $pipes,
            base_path()
        );

        if (is_resource($process)) {
            fclose($pipes[0]);
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);

            return $exitCode === 0;
        }

        return false;
    }

    /**
     * Create necessary directories.
     */
    protected function createDirectories(): bool
    {
        $directories = [
            app_path('Models/Concerns'),
            app_path('Models/Tenant'),
            database_path('migrations/tenant'),
            resource_path('js/pages/tenant'),
            resource_path('js/pages/tenants'),
        ];

        foreach ($directories as $directory) {
            if (! $this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }
        }

        return true;
    }

    /**
     * Publish model files.
     */
    protected function publishModels(): bool
    {
        $models = [
            'concerns/CentralConnection.php.stub' => app_path('Models/Concerns/CentralConnection.php'),
            'models/Tenant.php.stub' => app_path('Models/Tenant.php'),
            'models/Domain.php.stub' => app_path('Models/Domain.php'),
            'models/TenantUser.php.stub' => app_path('Models/TenantUser.php'),
            'models/Tenant/User.php.stub' => app_path('Models/Tenant/User.php'),
        ];

        foreach ($models as $stub => $destination) {
            $this->publishStub($stub, $destination);
        }

        return true;
    }

    /**
     * Publish service files.
     */
    protected function publishServices(): bool
    {
        return $this->publishStub(
            'services/TenantService.php.stub',
            app_path('Services/TenantService.php')
        );
    }

    /**
     * Publish job files.
     */
    protected function publishJobs(): bool
    {
        return $this->publishStub(
            'jobs/MarkTenantReady.php.stub',
            app_path('Jobs/MarkTenantReady.php')
        );
    }

    /**
     * Publish migration files.
     */
    protected function publishMigrations(): bool
    {
        // Central migrations with timestamps
        $timestamp = now();

        $centralMigrations = [
            'migrations/central/create_tenants_table.php.stub' => 'create_tenants_table.php',
            'migrations/central/create_domains_table.php.stub' => 'create_domains_table.php',
            'migrations/central/create_tenant_user_table.php.stub' => 'create_tenant_user_table.php',
        ];

        foreach ($centralMigrations as $stub => $filename) {
            $migrationTimestamp = $timestamp->format('Y_m_d_His');
            $destination = database_path("migrations/{$migrationTimestamp}_{$filename}");
            $this->publishStub($stub, $destination);
            $timestamp = $timestamp->addSecond();
        }

        // Tenant migrations (keep original names for consistent ordering)
        $tenantMigrations = [
            'migrations/tenant/0001_01_01_000000_create_users_table.php.stub',
            'migrations/tenant/0001_01_01_000001_create_personal_access_tokens_table.php.stub',
            'migrations/tenant/0001_01_01_000002_create_cache_table.php.stub',
            'migrations/tenant/0001_01_01_000003_create_jobs_table.php.stub',
        ];

        foreach ($tenantMigrations as $stub) {
            $filename = str_replace('.stub', '', basename($stub));
            $destination = database_path("migrations/tenant/{$filename}");
            $this->publishStub($stub, $destination);
        }

        return true;
    }

    /**
     * Publish the service provider.
     */
    protected function publishProvider(): bool
    {
        return $this->publishStub(
            'providers/TenancyServiceProvider.php.stub',
            app_path('Providers/TenancyServiceProvider.php')
        );
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfig(): bool
    {
        return $this->publishStub(
            'config/tenancy.php.stub',
            config_path('tenancy.php')
        );
    }

    /**
     * Publish the middleware.
     */
    protected function publishMiddleware(): bool
    {
        return $this->publishStub(
            'middleware/TenantIsReady.php.stub',
            app_path('Http/Middleware/TenantIsReady.php')
        );
    }

    /**
     * Publish the tenant routes file.
     */
    protected function publishRoutes(): bool
    {
        return $this->publishStub(
            'routes/tenant.php.stub',
            base_path('routes/tenant.php')
        );
    }

    /**
     * Publish Vue pages for tenancy.
     */
    protected function publishPages(): bool
    {
        $pages = [
            'pages/tenant/Welcome.vue.stub' => resource_path('js/pages/tenant/Welcome.vue'),
            'pages/tenant/Dashboard.vue.stub' => resource_path('js/pages/tenant/Dashboard.vue'),
            'pages/tenant/Provisioning.vue.stub' => resource_path('js/pages/tenant/Provisioning.vue'),
            'pages/tenants/Index.vue.stub' => resource_path('js/pages/tenants/Index.vue'),
        ];

        foreach ($pages as $stub => $destination) {
            $this->publishStub($stub, $destination);
        }

        return true;
    }

    /**
     * Publish the TenantController.
     */
    protected function publishController(): bool
    {
        return $this->publishStub(
            'controllers/TenantController.php.stub',
            app_path('Http/Controllers/TenantController.php')
        );
    }

    /**
     * Update bootstrap/providers.php to conditionally load TenancyServiceProvider.
     */
    protected function updateBootstrapProviders(): bool
    {
        $path = base_path('bootstrap/providers.php');
        $content = $this->files->get($path);

        // Check if already modified
        if (Str::contains($content, 'TenancyServiceProvider')) {
            return true;
        }

        // Find the return statement and modify it
        $newContent = <<<'PHP'
<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
];

// Conditionally load TenancyServiceProvider when tenancy is configured
if (file_exists(config_path('tenancy.php'))) {
    $providers[] = App\Providers\TenancyServiceProvider::class;
}

return $providers;

PHP;

        $this->files->put($path, $newContent);

        return true;
    }

    /**
     * Update bootstrap/app.php to load tenant routes.
     */
    protected function updateBootstrapApp(): bool
    {
        $path = base_path('bootstrap/app.php');
        $content = $this->files->get($path);

        // Check if already modified
        if (Str::contains($content, 'routes/tenant.php')) {
            return true;
        }

        // Add the Route import if not present
        if (! Str::contains($content, 'use Illuminate\Support\Facades\Route;')) {
            $content = str_replace(
                'use Illuminate\Foundation\Application;',
                "use Illuminate\Foundation\Application;\nuse Illuminate\Support\Facades\Route;",
                $content
            );
        }

        // Find the withRouting section and add tenant routes
        $pattern = '/->withRouting\(\s*([^)]+)\s*\)/s';

        if (preg_match($pattern, $content, $matches)) {
            $routingContent = $matches[1];

            // Check if 'then:' callback already exists
            if (! Str::contains($routingContent, 'then:')) {
                // Add then callback for tenant routes
                $newRoutingContent = rtrim($routingContent, ", \n\t").",\n        then: function () {\n            // Load tenant routes when tenancy is configured\n            if (file_exists(config_path('tenancy.php')) && file_exists(base_path('routes/tenant.php'))) {\n                Route::middleware(['web'])->group(base_path('routes/tenant.php'));\n            }\n        },\n    ";

                $content = str_replace($routingContent, $newRoutingContent, $content);
            }
        }

        $this->files->put($path, $content);

        return true;
    }

    /**
     * Update config/database.php with tenant connection.
     */
    protected function updateDatabaseConfig(): bool
    {
        $path = config_path('database.php');
        $content = $this->files->get($path);

        // Check if tenant connection already exists
        if (Str::contains($content, "'tenant'")) {
            return true;
        }

        // Find the connections array and add tenant connection after mysql
        $tenantConnection = <<<'PHP'

        'tenant' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => null, // Set dynamically by tenancy
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DB_CHARSET', 'utf8mb4'),
            'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],
PHP;

        // Insert after mysql connection
        $content = preg_replace(
            "/('mysql'\s*=>\s*\[[^\]]+\]),/s",
            "$1,{$tenantConnection}",
            $content
        );

        $this->files->put($path, $content);

        return true;
    }

    /**
     * Convert User.php to extend CentralUser for tenant relationships.
     */
    protected function convertUserModel(): bool
    {
        $userPath = app_path('Models/User.php');
        $centralUserPath = app_path('Models/CentralUser.php');

        // If CentralUser already exists, skip creating it
        if (! $this->files->exists($centralUserPath)) {
            // Publish CentralUser from stub
            $this->publishStub('models/CentralUser.php.stub', $centralUserPath);
        }

        // Update User.php to extend CentralUser (simpler alias approach)
        $userContent = <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models;

/**
 * User model alias for CentralUser.
 *
 * This class extends CentralUser to maintain backwards compatibility
 * with existing code that references the User model directly.
 * For new code, prefer using CentralUser explicitly.
 *
 * @mixin CentralUser
 */
class User extends CentralUser
{
    // All functionality is inherited from CentralUser
}

PHP;

        $this->files->put($userPath, $userContent);

        return true;
    }

    /**
     * Update .env.example with tenancy variables.
     */
    protected function updateEnvExample(): bool
    {
        $path = base_path('.env.example');

        if (! $this->files->exists($path)) {
            return true;
        }

        $content = $this->files->get($path);

        // Check if already has tenancy variables
        if (Str::contains($content, 'TENANCY_ENABLED')) {
            return true;
        }

        $tenancyVars = <<<'ENV'

# Multi-Tenancy
TENANCY_ENABLED=true
APP_DOMAIN=localhost
TENANCY_DB_PREFIX=tenant_
TENANCY_QUEUE_CREATION=false
TENANCY_QUEUE_DELETION=false
ENV;

        $content .= $tenancyVars;
        $this->files->put($path, $content);

        return true;
    }

    /**
     * Publish a stub file to a destination.
     */
    protected function publishStub(string $stub, string $destination): bool
    {
        $stubPath = "{$this->stubPath}/{$stub}";

        if (! $this->files->exists($stubPath)) {
            return false;
        }

        $content = $this->files->get($stubPath);

        // Ensure directory exists
        $directory = dirname($destination);
        if (! $this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        $this->files->put($destination, $content);

        return true;
    }

    /**
     * Display next steps after setup.
     */
    protected function displayNextSteps(): void
    {
        note('Next steps:');
        $this->newLine();

        $this->line('  1. Configure your environment:');
        $this->line('     <comment>APP_DOMAIN=yourapp.com</comment>');
        $this->line('     <comment>TENANCY_DB_PREFIX=tenant_</comment>');
        $this->newLine();

        $this->line('  2. Run migrations:');
        $this->line('     <comment>php artisan migrate</comment>');
        $this->newLine();

        $this->line('  3. Create your first tenant:');
        $this->line('     <comment>$tenant = Tenant::create([</comment>');
        $this->line('         <comment>\'company_name\' => \'Acme Corp\',</comment>');
        $this->line('         <comment>\'owner_user_id\' => $user->id,</comment>');
        $this->line('     <comment>]);</comment>');
        $this->line('     <comment>$tenant->domains()->create([\'domain\' => \'acme\']);</comment>');
        $this->newLine();

        $this->line('  4. Read the documentation:');
        $this->line('     <comment>docs/Tenancy.md</comment>');
        $this->newLine();
    }

    /**
     * Handle the rollback operation.
     */
    protected function handleRollback(): int
    {
        $this->newLine();
        warning('Rolling back tenancy setup...');
        $this->newLine();

        if (! $this->isAlreadyConfigured()) {
            info('Tenancy is not configured. Nothing to rollback.');

            return self::SUCCESS;
        }

        // Confirm rollback (skip if --force is used)
        if (! $this->option('force')) {
            if (! confirm('This will remove all tenancy files and reset the database. Continue?', false)) {
                info('Rollback cancelled.');

                return self::SUCCESS;
            }
        }

        $steps = [
            'removeConfig' => 'Removing configuration',
            'removeProvider' => 'Removing service provider',
            'removeModels' => 'Removing models',
            'removeServices' => 'Removing services',
            'removeJobs' => 'Removing jobs',
            'removeMigrations' => 'Removing migrations',
            'removeMiddleware' => 'Removing middleware',
            'removeRoutes' => 'Removing routes',
            'removePages' => 'Removing Vue pages',
            'removeController' => 'Removing TenantController',
            'restoreBootstrapProviders' => 'Restoring bootstrap providers',
            'removeDatabaseConfig' => 'Removing tenant database config',
            'restoreUserModel' => 'Restoring User model',
            'removeEnvVariables' => 'Removing env variables',
            'resetDatabase' => 'Resetting database',
        ];

        $currentStep = 0;
        $totalSteps = count($steps);

        foreach ($steps as $method => $description) {
            $currentStep++;
            $prefix = "[{$currentStep}/{$totalSteps}]";

            $result = spin(
                fn () => $this->{$method}(),
                "{$prefix} {$description}..."
            );

            if ($result === false) {
                error("Failed: {$description}");

                return self::FAILURE;
            }
        }

        $this->newLine();
        info('Tenancy rollback complete! The application has been reset.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Remove the tenancy configuration file.
     */
    protected function removeConfig(): bool
    {
        $path = config_path('tenancy.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove the TenancyServiceProvider.
     */
    protected function removeProvider(): bool
    {
        $path = app_path('Providers/TenancyServiceProvider.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove tenancy model files.
     */
    protected function removeModels(): bool
    {
        $files = [
            app_path('Models/Tenant.php'),
            app_path('Models/Domain.php'),
            app_path('Models/TenantUser.php'),
            app_path('Models/CentralUser.php'),
            app_path('Models/Concerns/CentralConnection.php'),
        ];

        foreach ($files as $file) {
            if ($this->files->exists($file)) {
                $this->files->delete($file);
            }
        }

        // Remove Tenant directory
        $tenantDir = app_path('Models/Tenant');
        if ($this->files->isDirectory($tenantDir)) {
            $this->files->deleteDirectory($tenantDir);
        }

        // Remove Concerns directory if empty
        $concernsDir = app_path('Models/Concerns');
        if ($this->files->isDirectory($concernsDir) && count($this->files->files($concernsDir)) === 0) {
            $this->files->deleteDirectory($concernsDir);
        }

        return true;
    }

    /**
     * Remove TenantService.
     */
    protected function removeServices(): bool
    {
        $path = app_path('Services/TenantService.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove tenancy jobs.
     */
    protected function removeJobs(): bool
    {
        $path = app_path('Jobs/MarkTenantReady.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove tenancy migrations.
     */
    protected function removeMigrations(): bool
    {
        // Remove tenant migrations directory
        $tenantMigrationsDir = database_path('migrations/tenant');
        if ($this->files->isDirectory($tenantMigrationsDir)) {
            $this->files->deleteDirectory($tenantMigrationsDir);
        }

        // Remove central tenancy migrations
        $centralMigrations = $this->files->glob(database_path('migrations/*_create_tenants_table.php'));
        $centralMigrations = array_merge($centralMigrations, $this->files->glob(database_path('migrations/*_create_domains_table.php')));
        $centralMigrations = array_merge($centralMigrations, $this->files->glob(database_path('migrations/*_create_tenant_user_table.php')));

        foreach ($centralMigrations as $migration) {
            $this->files->delete($migration);
        }

        return true;
    }

    /**
     * Remove TenantIsReady middleware.
     */
    protected function removeMiddleware(): bool
    {
        $path = app_path('Http/Middleware/TenantIsReady.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove tenant routes file.
     */
    protected function removeRoutes(): bool
    {
        $path = base_path('routes/tenant.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Remove tenancy Vue pages.
     */
    protected function removePages(): bool
    {
        $directories = [
            resource_path('js/pages/tenant'),
            resource_path('js/pages/tenants'),
        ];

        foreach ($directories as $directory) {
            if ($this->files->isDirectory($directory)) {
                $this->files->deleteDirectory($directory);
            }
        }

        return true;
    }

    /**
     * Remove TenantController.
     */
    protected function removeController(): bool
    {
        $path = app_path('Http/Controllers/TenantController.php');
        if ($this->files->exists($path)) {
            $this->files->delete($path);
        }

        return true;
    }

    /**
     * Restore bootstrap/providers.php to original state.
     */
    protected function restoreBootstrapProviders(): bool
    {
        $path = base_path('bootstrap/providers.php');

        $originalContent = <<<'PHP'
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\HorizonServiceProvider::class,
];

PHP;

        $this->files->put($path, $originalContent);

        return true;
    }

    /**
     * Remove tenant connection from database config.
     */
    protected function removeDatabaseConfig(): bool
    {
        $path = config_path('database.php');
        $content = $this->files->get($path);

        // Remove the tenant connection block
        $pattern = "/\s*'tenant'\s*=>\s*\[[^\]]+\],/s";
        $content = preg_replace($pattern, '', $content);

        $this->files->put($path, $content);

        return true;
    }

    /**
     * Restore User model to standalone class.
     */
    protected function restoreUserModel(): bool
    {
        $userPath = app_path('Models/User.php');

        // Restore original User model from stub
        $stubPath = base_path('stubs/default/models/User.php.stub');

        if ($this->files->exists($stubPath)) {
            $this->files->copy($stubPath, $userPath);
        } else {
            // Create a basic User model
            $userContent = <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'is_active',
        'last_seen_at',
        'last_ip_address',
        'last_user_agent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user is currently online (seen within last 5 minutes).
     */
    public function isOnline(): bool
    {
        /** @var \Illuminate\Support\Carbon|null $lastSeen */
        $lastSeen = $this->last_seen_at;

        return $lastSeen !== null && $lastSeen->greaterThan(now()->subMinutes(5));
    }
}

PHP;

            $this->files->put($userPath, $userContent);
        }

        return true;
    }

    /**
     * Remove tenancy variables from .env.example.
     */
    protected function removeEnvVariables(): bool
    {
        $path = base_path('.env.example');

        if (! $this->files->exists($path)) {
            return true;
        }

        $content = $this->files->get($path);

        // Remove the tenancy section
        $content = preg_replace('/\n*# Multi-Tenancy\nTENANCY_ENABLED=.*\nAPP_DOMAIN=.*\nTENANCY_DB_PREFIX=.*\nTENANCY_QUEUE_CREATION=.*\nTENANCY_QUEUE_DELETION=.*/s', '', $content);

        $this->files->put($path, $content);

        return true;
    }

    /**
     * Reset the database using start:fresh.
     */
    protected function resetDatabase(): bool
    {
        $this->call('start:fresh', ['--non-interactive' => true]);

        return true;
    }
}
