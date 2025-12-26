<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class ElectronRollbackTest extends TestCase
{
    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem;
    }

    protected function tearDown(): void
    {
        // Always cleanup after tests
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        parent::tearDown();
    }

    public function test_it_preserves_electron_directory(): void
    {
        // Setup first
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertDirectoryExists(base_path('electron'));

        // Rollback
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        // The electron directory should still exist - it's part of the codebase
        $this->assertDirectoryExists(base_path('electron'));
    }

    public function test_it_removes_configuration_file(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertFileExists(config_path('electron.php'));

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(config_path('electron.php'));
    }

    public function test_it_restores_original_package_json(): void
    {
        $originalContent = file_get_contents(base_path('package.json'));

        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $restoredContent = file_get_contents(base_path('package.json'));
        $this->assertEquals($originalContent, $restoredContent);
    }

    public function test_it_restores_bootstrap_providers(): void
    {
        $originalContent = file_get_contents(base_path('bootstrap/providers.php'));

        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $restoredContent = file_get_contents(base_path('bootstrap/providers.php'));
        $this->assertEquals($originalContent, $restoredContent);
    }

    public function test_it_removes_service_provider(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertFileExists(app_path('Providers/ElectronServiceProvider.php'));

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(app_path('Providers/ElectronServiceProvider.php'));
    }

    public function test_it_removes_env_production(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertFileExists(base_path('.env.production'));

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(base_path('.env.production'));
    }

    public function test_it_removes_electron_builder_config(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertFileExists(base_path('electron-builder.json'));

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(base_path('electron-builder.json'));
    }

    public function test_it_removes_build_scripts(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertFileExists(base_path('scripts/setup-binaries.sh'));

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $this->assertFileDoesNotExist(base_path('scripts/setup-binaries.sh'));
        $this->assertFileDoesNotExist(base_path('scripts/setup-binaries.ps1'));
    }

    public function test_it_cleans_up_backups(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);
        $this->assertDirectoryExists(storage_path('electron-backups'));

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $this->assertDirectoryDoesNotExist(storage_path('electron-backups'));
    }

    public function test_it_handles_rollback_when_not_configured(): void
    {
        // Should not error when nothing to rollback
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true])
            ->assertSuccessful();
    }

    public function test_it_removes_electron_env_variables_from_env_example(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $envExample = file_get_contents(base_path('.env.example'));
        $this->assertStringContainsString('ELECTRON_APP_NAME', $envExample);

        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);

        $envExample = file_get_contents(base_path('.env.example'));
        $this->assertStringNotContainsString('ELECTRON_APP_NAME', $envExample);
    }
}
