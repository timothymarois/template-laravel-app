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

        // Skip if BUILD_TEST_ELECTRON is not enabled
        if (! env('BUILD_TEST_ELECTRON', false)) {
            $this->markTestSkipped('Electron build tests are disabled. Set BUILD_TEST_ELECTRON=true to enable.');
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
        // Remove files that setup creates
        @unlink(app_path('Providers/ElectronServiceProvider.php'));
        @unlink(config_path('electron.php'));
        @unlink(base_path('electron-builder.json'));
        @unlink(base_path('.env.production'));

        try {
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        } catch (\Throwable $e) {
            // If artisan fails, manually restore files from git
            $this->manualCleanup();
        }
    }

    protected function manualCleanup(): void
    {
        // Restore modified files using git
        exec('git restore .env.example bootstrap/providers.php package.json 2>/dev/null');

        // Remove backup directory if exists
        $backupPath = storage_path('electron-backups');
        if (is_dir($backupPath)) {
            $this->files->deleteDirectory($backupPath);
        }
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
            // electron/ directory is always present - it's part of the codebase
            $this->assertDirectoryExists(base_path('electron'));

            // Rollback
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true])
                ->assertSuccessful();
            $this->assertFileDoesNotExist(config_path('electron.php'));
            // electron/ directory should still exist after rollback
            $this->assertDirectoryExists(base_path('electron'));
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
