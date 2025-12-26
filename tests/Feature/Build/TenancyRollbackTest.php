<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class TenancyRollbackTest extends TestCase
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
        $this->forceCleanup();
    }

    protected function tearDown(): void
    {
        // Always cleanup after tests
        $this->forceCleanup();
        parent::tearDown();
    }

    protected function forceCleanup(): void
    {
        // Remove files that setup creates
        @unlink(app_path('Providers/TenancyServiceProvider.php'));
        @unlink(config_path('tenancy.php'));

        try {
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        } catch (\Throwable $e) {
            // Ignore artisan errors
        }

        // Always restore files using git as final cleanup
        // This handles cases where rollback succeeds but backups didn't exist
        $this->restoreFilesFromGit();
    }

    protected function restoreFilesFromGit(): void
    {
        // Restore modified files using git (must match files modified by Tenancy.php)
        exec('git restore app/Models/User.php app/Http/Controllers/Auth/RegisterController.php bootstrap/providers.php bootstrap/app.php config/database.php 2>/dev/null');

        // Remove backup directory if exists
        $backupPath = storage_path('tenancy-backups');
        if (is_dir($backupPath)) {
            $this->files->deleteDirectory($backupPath);
        }
    }

    public function test_it_removes_configuration_file(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(config_path('tenancy.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(config_path('tenancy.php'));
    }

    public function test_it_removes_service_provider(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Providers/TenancyServiceProvider.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Providers/TenancyServiceProvider.php'));
    }

    public function test_it_removes_tenant_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Models/Tenant.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Models/Tenant.php'));
    }

    public function test_it_removes_domain_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Models/Domain.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Models/Domain.php'));
    }

    public function test_it_removes_central_user_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Models/CentralUser.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Models/CentralUser.php'));
    }

    public function test_it_removes_tenant_user_model(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Models/TenantUser.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Models/TenantUser.php'));
    }

    public function test_it_removes_tenant_directory(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertDirectoryExists(app_path('Models/Tenant'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertDirectoryDoesNotExist(app_path('Models/Tenant'));
    }

    public function test_it_removes_tenant_service(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Services/TenantService.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Services/TenantService.php'));
    }

    public function test_it_removes_mark_tenant_ready_job(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Jobs/MarkTenantReady.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Jobs/MarkTenantReady.php'));
    }

    public function test_it_removes_tenant_is_ready_middleware(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Http/Middleware/TenantIsReady.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Http/Middleware/TenantIsReady.php'));
    }

    public function test_it_removes_tenant_controller(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(app_path('Http/Controllers/TenantController.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Http/Controllers/TenantController.php'));
    }

    public function test_it_removes_tenant_routes(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(base_path('routes/tenant.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(base_path('routes/tenant.php'));
    }

    public function test_it_removes_tenants_routes(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertFileExists(base_path('routes/tenants.php'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(base_path('routes/tenants.php'));
    }

    public function test_it_removes_tenant_vue_pages(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertDirectoryExists(resource_path('js/pages/tenant'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertDirectoryDoesNotExist(resource_path('js/pages/tenant'));
    }

    public function test_it_removes_tenants_vue_pages(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertDirectoryExists(resource_path('js/pages/tenants'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertDirectoryDoesNotExist(resource_path('js/pages/tenants'));
    }

    public function test_it_removes_central_migrations(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $migrations = $this->files->glob(database_path('migrations/*_create_tenants_table.php'));
        $this->assertNotEmpty($migrations);

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $migrations = $this->files->glob(database_path('migrations/*_create_tenants_table.php'));
        $this->assertEmpty($migrations);
    }

    public function test_it_removes_tenant_migrations_directory(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertDirectoryExists(database_path('migrations/tenant'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertDirectoryDoesNotExist(database_path('migrations/tenant'));
    }

    public function test_it_restores_original_user_model(): void
    {
        $originalContent = file_get_contents(app_path('Models/User.php'));

        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $restoredContent = file_get_contents(app_path('Models/User.php'));
        $this->assertEquals($originalContent, $restoredContent);
    }

    public function test_it_restores_bootstrap_providers(): void
    {
        $originalContent = file_get_contents(base_path('bootstrap/providers.php'));

        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $restoredContent = file_get_contents(base_path('bootstrap/providers.php'));
        $this->assertEquals($originalContent, $restoredContent);
    }

    public function test_it_restores_bootstrap_app(): void
    {
        $originalContent = file_get_contents(base_path('bootstrap/app.php'));

        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $restoredContent = file_get_contents(base_path('bootstrap/app.php'));
        $this->assertEquals($originalContent, $restoredContent);
    }

    public function test_it_restores_database_config(): void
    {
        $originalContent = file_get_contents(config_path('database.php'));

        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $restoredContent = file_get_contents(config_path('database.php'));
        $this->assertEquals($originalContent, $restoredContent);
    }

    public function test_it_cleans_up_backups(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
        $this->assertDirectoryExists(storage_path('tenancy-backups'));

        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);

        $this->assertDirectoryDoesNotExist(storage_path('tenancy-backups'));
    }

    public function test_it_handles_rollback_when_not_configured(): void
    {
        // Should not error when nothing to rollback
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true])
            ->assertSuccessful();
    }
}
