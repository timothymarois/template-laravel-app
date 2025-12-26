<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class TenancySetupTest extends TestCase
{
    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip if BUILD_TEST_TENANCY is not enabled
        if (! env('BUILD_TEST_TENANCY', false)) {
            $this->markTestSkipped('Tenancy build tests are disabled. Set BUILD_TEST_TENANCY=true to enable.');
        }

        $this->files = new Filesystem;

        // Ensure clean state before each test
        $this->cleanupTenancy();
    }

    protected function tearDown(): void
    {
        // Always rollback after tests
        $this->cleanupTenancy();
        parent::tearDown();
    }

    protected function cleanupTenancy(): void
    {
        // Silently run rollback to clean up
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
    }

    public function test_it_publishes_configuration_file(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(config_path('tenancy.php'));

        // Verify config structure
        $config = require config_path('tenancy.php');
        $this->assertIsArray($config);
    }

    public function test_it_publishes_service_provider(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Providers/TenancyServiceProvider.php'));
    }

    public function test_it_creates_tenant_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Models/Tenant.php'));
    }

    public function test_it_creates_domain_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Models/Domain.php'));
    }

    public function test_it_creates_tenant_user_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Models/TenantUser.php'));
    }

    public function test_it_creates_central_user_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Models/CentralUser.php'));
    }

    public function test_it_creates_tenant_user_model_in_tenant_namespace(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Models/Tenant/User.php'));
    }

    public function test_it_creates_central_connection_concern(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Models/Concerns/CentralConnection.php'));
    }

    public function test_it_creates_tenant_service(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Services/TenantService.php'));
    }

    public function test_it_creates_mark_tenant_ready_job(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Jobs/MarkTenantReady.php'));
    }

    public function test_it_creates_tenant_is_ready_middleware(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Http/Middleware/TenantIsReady.php'));
    }

    public function test_it_creates_tenant_controller(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Http/Controllers/TenantController.php'));
    }

    public function test_it_creates_tenant_routes(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(base_path('routes/tenant.php'));
    }

    public function test_it_creates_tenants_routes(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(base_path('routes/tenants.php'));
    }

    public function test_it_creates_tenant_vue_pages(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(resource_path('js/pages/tenant/Welcome.vue'));
        $this->assertFileExists(resource_path('js/pages/tenant/Dashboard.vue'));
        $this->assertFileExists(resource_path('js/pages/tenant/Provisioning.vue'));
    }

    public function test_it_creates_tenants_vue_pages(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(resource_path('js/pages/tenants/Index.vue'));
    }

    public function test_it_creates_central_migrations(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        // Check for migrations with pattern matching (timestamps vary)
        $migrations = $this->files->glob(database_path('migrations/*_create_tenants_table.php'));
        $this->assertNotEmpty($migrations, 'Tenants table migration should exist');

        $migrations = $this->files->glob(database_path('migrations/*_create_domains_table.php'));
        $this->assertNotEmpty($migrations, 'Domains table migration should exist');

        $migrations = $this->files->glob(database_path('migrations/*_create_tenant_user_table.php'));
        $this->assertNotEmpty($migrations, 'Tenant user table migration should exist');
    }

    public function test_it_creates_tenant_migrations(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertDirectoryExists(database_path('migrations/tenant'));
        $this->assertFileExists(database_path('migrations/tenant/0001_01_01_000000_create_users_table.php'));
        $this->assertFileExists(database_path('migrations/tenant/0001_01_01_000001_create_personal_access_tokens_table.php'));
        $this->assertFileExists(database_path('migrations/tenant/0001_01_01_000002_create_cache_table.php'));
        $this->assertFileExists(database_path('migrations/tenant/0001_01_01_000003_create_jobs_table.php'));
    }

    public function test_it_updates_bootstrap_providers(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $providers = file_get_contents(base_path('bootstrap/providers.php'));
        $this->assertStringContainsString('TenancyServiceProvider', $providers);
    }

    public function test_it_updates_bootstrap_app(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $app = file_get_contents(base_path('bootstrap/app.php'));
        $this->assertStringContainsString('routes/tenant.php', $app);
    }

    public function test_it_updates_database_config(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $config = file_get_contents(config_path('database.php'));
        $this->assertStringContainsString("'tenant'", $config);
    }

    public function test_it_converts_user_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $userContent = file_get_contents(app_path('Models/User.php'));
        $this->assertStringContainsString('extends CentralUser', $userContent);
    }

    public function test_it_creates_backup_of_user_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(storage_path('tenancy-backups/User.php.bak'));
    }

    public function test_it_creates_backup_of_bootstrap_providers(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(storage_path('tenancy-backups/providers.php.bak'));
    }

    public function test_it_creates_backup_of_database_config(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->assertFileExists(storage_path('tenancy-backups/database.php.bak'));
    }
}
