<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class ElectronIdempotencyTest extends TestCase
{
    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
    }

    protected function tearDown(): void
    {
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        parent::tearDown();
    }

    public function test_running_setup_twice_with_force_succeeds(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        // Verify files still exist
        $this->assertFileExists(config_path('electron.php'));
        $this->assertDirectoryExists(base_path('electron'));
    }

    public function test_setup_rollback_setup_cycle_works(): void
    {
        // Setup
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();
        $this->assertFileExists(config_path('electron.php'));

        // Rollback
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true])
            ->assertSuccessful();
        $this->assertFileDoesNotExist(config_path('electron.php'));

        // Setup again
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();
        $this->assertFileExists(config_path('electron.php'));

        // Final cleanup
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
    }

    public function test_multiple_setup_rollback_cycles_work(): void
    {
        for ($i = 0; $i < 3; $i++) {
            // Setup
            $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
                ->assertSuccessful();
            $this->assertFileExists(config_path('electron.php'));
            $this->assertDirectoryExists(base_path('electron'));

            // Rollback
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true])
                ->assertSuccessful();
            $this->assertFileDoesNotExist(config_path('electron.php'));
            $this->assertDirectoryDoesNotExist(base_path('electron'));
        }
    }

    public function test_rollback_is_idempotent(): void
    {
        // Setup first
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        // Multiple rollbacks should all succeed
        for ($i = 0; $i < 3; $i++) {
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true])
                ->assertSuccessful();
        }
    }

    public function test_package_json_is_consistent_after_cycles(): void
    {
        $originalPackageJson = file_get_contents(base_path('package.json'));

        // Run multiple setup/rollback cycles
        for ($i = 0; $i < 2; $i++) {
            $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        }

        $finalPackageJson = file_get_contents(base_path('package.json'));
        $this->assertEquals($originalPackageJson, $finalPackageJson);
    }

    public function test_bootstrap_providers_is_consistent_after_cycles(): void
    {
        $originalProviders = file_get_contents(base_path('bootstrap/providers.php'));

        // Run multiple setup/rollback cycles
        for ($i = 0; $i < 2; $i++) {
            $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        }

        $finalProviders = file_get_contents(base_path('bootstrap/providers.php'));
        $this->assertEquals($originalProviders, $finalProviders);
    }
}
