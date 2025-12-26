<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class TenancyConfigurationTest extends TestCase
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
        $this->forceCleanup();
        parent::tearDown();
    }

    protected function forceCleanup(): void
    {
        @unlink(app_path('Providers/TenancyServiceProvider.php'));
        @unlink(config_path('tenancy.php'));

        try {
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        } catch (\Throwable $e) {
            // Ignore errors during cleanup
        }
    }

    public function test_it_writes_correct_tenant_model_configuration(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('tenancy.php');

        $this->assertArrayHasKey('tenant_model', $config);
        $this->assertEquals('App\Models\Tenant', $config['tenant_model']);
    }

    public function test_it_writes_correct_domain_model_configuration(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('tenancy.php');

        $this->assertArrayHasKey('domain_model', $config);
        $this->assertEquals('App\Models\Domain', $config['domain_model']);
    }

    public function test_it_writes_correct_database_configuration(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('tenancy.php');

        $this->assertArrayHasKey('database', $config);
        $this->assertArrayHasKey('central_connection', $config['database']);
        $this->assertArrayHasKey('prefix', $config['database']);
        $this->assertArrayHasKey('managers', $config['database']);
    }

    public function test_it_writes_correct_bootstrappers_configuration(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('tenancy.php');

        $this->assertArrayHasKey('bootstrappers', $config);
        $this->assertIsArray($config['bootstrappers']);
        $this->assertNotEmpty($config['bootstrappers']);
    }

    public function test_it_writes_correct_central_domains_configuration(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('tenancy.php');

        $this->assertArrayHasKey('central_domains', $config);
        $this->assertIsArray($config['central_domains']);
    }

    public function test_it_writes_correct_migration_parameters(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('tenancy.php');

        $this->assertArrayHasKey('migration_parameters', $config);
        $this->assertArrayHasKey('--force', $config['migration_parameters']);
        $this->assertArrayHasKey('--path', $config['migration_parameters']);
    }

    public function test_database_config_has_tenant_connection(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('database.php');

        $this->assertArrayHasKey('connections', $config);
        $this->assertArrayHasKey('tenant', $config['connections']);
        $this->assertEquals('mysql', $config['connections']['tenant']['driver']);
    }

    public function test_tenant_connection_has_null_database(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $config = require config_path('database.php');

        // Tenant database is set dynamically by tenancy package
        $this->assertNull($config['connections']['tenant']['database']);
    }

    public function test_central_user_model_has_tenant_relationship(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $content = file_get_contents(app_path('Models/CentralUser.php'));

        $this->assertStringContainsString('tenants()', $content);
        $this->assertStringContainsString('belongsToMany', $content);
    }

    public function test_tenant_model_has_required_relationships(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $content = file_get_contents(app_path('Models/Tenant.php'));

        // Uses HasDomains trait from tenancy package
        $this->assertStringContainsString('HasDomains', $content);
        $this->assertStringContainsString('ownerUser()', $content);
        $this->assertStringContainsString('users()', $content);
    }

    public function test_tenant_service_has_creation_method(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $content = file_get_contents(app_path('Services/TenantService.php'));

        $this->assertStringContainsString('createForUser', $content);
        $this->assertStringContainsString('function create(', $content);
    }

    public function test_tenancy_service_provider_is_valid(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        $content = file_get_contents(app_path('Providers/TenancyServiceProvider.php'));

        $this->assertStringContainsString('namespace App\Providers', $content);
        $this->assertStringContainsString('class TenancyServiceProvider', $content);
        $this->assertStringContainsString('function boot', $content);
    }
}
