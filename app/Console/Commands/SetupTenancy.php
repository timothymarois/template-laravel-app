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
     * The backup directory path.
     */
    protected string $backupPath;

    /**
     * Create a new command instance.
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
        $this->stubPath = base_path('stubs/tenancy');
        $this->backupPath = storage_path('tenancy-backups');
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
            'backupOriginalFiles' => 'Backing up original files',
            'publishModels' => 'Publishing models',
            'publishServices' => 'Publishing services',
            'publishJobs' => 'Publishing jobs',
            'publishMigrations' => 'Publishing migrations',
            'publishProvider' => 'Publishing service provider',
            'publishConfig' => 'Publishing configuration',
            'publishMiddleware' => 'Publishing middleware',
            'publishRoutes' => 'Publishing tenant routes',
            'publishTenantsRoutes' => 'Publishing tenant selection routes',
            'publishPages' => 'Publishing Vue pages',
            'publishController' => 'Publishing TenantController',
            'updateBootstrapProviders' => 'Updating bootstrap providers',
            'updateBootstrapApp' => 'Updating bootstrap/app.php for routes',
            'updateDatabaseConfig' => 'Updating database configuration',
            'convertUserModel' => 'Converting User model to CentralUser',
            'updateRegisterController' => 'Updating RegisterController for tenancy',
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
            $this->backupPath,
        ];

        foreach ($directories as $directory) {
            if (! $this->files->isDirectory($directory)) {
                $this->files->makeDirectory($directory, 0755, true);
            }
        }

        return true;
    }

    /**
     * Backup original files before modification.
     */
    protected function backupOriginalFiles(): bool
    {
        $filesToBackup = [
            app_path('Models/User.php') => 'User.php.bak',
            app_path('Http/Controllers/Auth/RegisterController.php') => 'RegisterController.php.bak',
            base_path('bootstrap/providers.php') => 'providers.php.bak',
            base_path('bootstrap/app.php') => 'app.php.bak',
            config_path('database.php') => 'database.php.bak',
            base_path('.env.example') => 'env.example.bak',
        ];

        foreach ($filesToBackup as $source => $backupName) {
            if ($this->files->exists($source)) {
                $this->files->copy($source, "{$this->backupPath}/{$backupName}");
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
     * Publish the tenant routes file (for subdomain routes).
     */
    protected function publishRoutes(): bool
    {
        return $this->publishStub(
            'routes/tenant.php.stub',
            base_path('routes/tenant.php')
        );
    }

    /**
     * Publish the tenant selection routes file (for central domain).
     */
    protected function publishTenantsRoutes(): bool
    {
        return $this->publishStub(
            'routes/tenants.php.stub',
            base_path('routes/tenants.php')
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
     * Update bootstrap/app.php to load tenancy routes.
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
                // Add then callback for tenancy routes
                $thenCallback = <<<'CALLBACK'
,
        then: function () {
            // Load tenancy routes when tenancy is configured
            if (file_exists(config_path('tenancy.php'))) {
                // Central domain tenant selection routes
                if (file_exists(base_path('routes/tenants.php'))) {
                    require base_path('routes/tenants.php');
                }
                // Subdomain tenant routes
                if (file_exists(base_path('routes/tenant.php'))) {
                    Route::middleware(['web'])->group(base_path('routes/tenant.php'));
                }
            }
        },

CALLBACK;
                $newRoutingContent = rtrim($routingContent, ", \n\t").$thenCallback;

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
     * Update RegisterController for tenancy support.
     */
    protected function updateRegisterController(): bool
    {
        return $this->publishStub(
            'controllers/RegisterController.php.stub',
            app_path('Http/Controllers/Auth/RegisterController.php')
        );
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
            'restoreBootstrapApp' => 'Restoring bootstrap app',
            'removeDatabaseConfig' => 'Restoring database config',
            'restoreUserModel' => 'Restoring User model',
            'restoreRegisterController' => 'Restoring RegisterController',
            'removeEnvVariables' => 'Restoring env variables',
            'removeComposerPackage' => 'Removing tenancy package',
            'resetDatabase' => 'Resetting database',
            'cleanupBackups' => 'Cleaning up backup files',
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
     * Remove tenant routes files.
     */
    protected function removeRoutes(): bool
    {
        $files = [
            base_path('routes/tenant.php'),
            base_path('routes/tenants.php'),
        ];

        foreach ($files as $path) {
            if ($this->files->exists($path)) {
                $this->files->delete($path);
            }
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
     * Restore bootstrap/providers.php from backup.
     */
    protected function restoreBootstrapProviders(): bool
    {
        $path = base_path('bootstrap/providers.php');
        $backupPath = "{$this->backupPath}/providers.php.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Restore bootstrap/app.php from backup.
     */
    protected function restoreBootstrapApp(): bool
    {
        $path = base_path('bootstrap/app.php');
        $backupPath = "{$this->backupPath}/app.php.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Restore database config from backup.
     */
    protected function removeDatabaseConfig(): bool
    {
        $path = config_path('database.php');
        $backupPath = "{$this->backupPath}/database.php.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Restore User model from backup.
     */
    protected function restoreUserModel(): bool
    {
        $userPath = app_path('Models/User.php');
        $backupPath = "{$this->backupPath}/User.php.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $userPath);
        }

        return true;
    }

    /**
     * Restore .env.example from backup.
     */
    protected function removeEnvVariables(): bool
    {
        $path = base_path('.env.example');
        $backupPath = "{$this->backupPath}/env.example.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Restore the original RegisterController from backup.
     */
    protected function restoreRegisterController(): bool
    {
        $path = app_path('Http/Controllers/Auth/RegisterController.php');
        $backupPath = "{$this->backupPath}/RegisterController.php.bak";

        if ($this->files->exists($backupPath)) {
            $this->files->copy($backupPath, $path);
        }

        return true;
    }

    /**
     * Remove the tenancy composer package.
     */
    protected function removeComposerPackage(): bool
    {
        // Check if package is installed
        $composerJson = json_decode($this->files->get(base_path('composer.json')), true);
        $packages = array_merge(
            $composerJson['require'] ?? [],
            $composerJson['require-dev'] ?? []
        );

        if (! isset($packages['stancl/tenancy'])) {
            return true;
        }

        // Remove the package
        $process = proc_open(
            'composer remove stancl/tenancy',
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
     * Reset the database using start:fresh.
     */
    protected function resetDatabase(): bool
    {
        $this->call('start:fresh', ['--non-interactive' => true]);

        return true;
    }

    /**
     * Clean up backup files after successful rollback.
     */
    protected function cleanupBackups(): bool
    {
        if ($this->files->isDirectory($this->backupPath)) {
            $this->files->deleteDirectory($this->backupPath);
        }

        return true;
    }
}
