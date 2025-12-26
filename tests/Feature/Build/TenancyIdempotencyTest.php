<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class TenancyIdempotencyTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        parent::tearDown();
    }

    public function test_running_setup_twice_with_force_succeeds(): void
    {
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();

        // Verify files still exist
        $this->assertFileExists(config_path('tenancy.php'));
        $this->assertFileExists(app_path('Models/Tenant.php'));
    }

    public function test_setup_rollback_setup_cycle_works(): void
    {
        // Setup
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();
        $this->assertFileExists(config_path('tenancy.php'));

        // Rollback
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true])
            ->assertSuccessful();
        $this->assertFileDoesNotExist(config_path('tenancy.php'));

        // Setup again
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
            ->assertSuccessful();
        $this->assertFileExists(config_path('tenancy.php'));

        // Final cleanup
        $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
    }

    public function test_multiple_setup_rollback_cycles_work(): void
    {
        for ($i = 0; $i < 3; $i++) {
            // Setup
            $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true])
                ->assertSuccessful();
            $this->assertFileExists(config_path('tenancy.php'));
            $this->assertFileExists(app_path('Models/Tenant.php'));

            // Rollback
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true])
                ->assertSuccessful();
            $this->assertFileDoesNotExist(config_path('tenancy.php'));
            $this->assertFileDoesNotExist(app_path('Models/Tenant.php'));
        }
    }

    public function test_rollback_is_idempotent(): void
    {
        // Setup first
        $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);

        // Multiple rollbacks should all succeed
        for ($i = 0; $i < 3; $i++) {
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true])
                ->assertSuccessful();
        }
    }

    public function test_user_model_is_consistent_after_cycles(): void
    {
        $originalUserContent = file_get_contents(app_path('Models/User.php'));

        // Run multiple setup/rollback cycles
        for ($i = 0; $i < 2; $i++) {
            $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        }

        $finalUserContent = file_get_contents(app_path('Models/User.php'));
        $this->assertEquals($originalUserContent, $finalUserContent);
    }

    public function test_bootstrap_providers_is_consistent_after_cycles(): void
    {
        $originalProviders = file_get_contents(base_path('bootstrap/providers.php'));

        // Run multiple setup/rollback cycles
        for ($i = 0; $i < 2; $i++) {
            $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        }

        $finalProviders = file_get_contents(base_path('bootstrap/providers.php'));
        $this->assertEquals($originalProviders, $finalProviders);
    }

    public function test_bootstrap_app_is_consistent_after_cycles(): void
    {
        $originalApp = file_get_contents(base_path('bootstrap/app.php'));

        // Run multiple setup/rollback cycles
        for ($i = 0; $i < 2; $i++) {
            $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        }

        $finalApp = file_get_contents(base_path('bootstrap/app.php'));
        $this->assertEquals($originalApp, $finalApp);
    }

    public function test_database_config_is_consistent_after_cycles(): void
    {
        $originalConfig = file_get_contents(config_path('database.php'));

        // Run multiple setup/rollback cycles
        for ($i = 0; $i < 2; $i++) {
            $this->artisan('build:tenancy', ['--force' => true, '--skip-composer' => true]);
            $this->artisan('build:tenancy', ['--rollback' => true, '--force' => true]);
        }

        $finalConfig = file_get_contents(config_path('database.php'));
        $this->assertEquals($originalConfig, $finalConfig);
    }
}
