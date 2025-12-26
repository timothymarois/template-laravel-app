<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class ElectronSetupTest extends TestCase
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
        // Always cleanup after tests
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

    public function test_electron_directory_structure_already_exists(): void
    {
        // The electron directory is part of the codebase, not created by build:electron
        $this->assertDirectoryExists(base_path('electron/main'));
        $this->assertDirectoryExists(base_path('electron/preload'));
        $this->assertDirectoryExists(base_path('electron/renderer'));
        $this->assertDirectoryExists(base_path('electron/resources'));
        $this->assertDirectoryExists(base_path('electron/main/services'));
    }

    public function test_it_publishes_configuration_file(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $this->assertFileExists(config_path('electron.php'));

        // Verify config structure
        $config = require config_path('electron.php');
        $this->assertArrayHasKey('app', $config);
        $this->assertArrayHasKey('services', $config);
        $this->assertArrayHasKey('ports', $config);
    }

    public function test_it_publishes_service_provider(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Providers/ElectronServiceProvider.php'));
    }

    public function test_it_updates_bootstrap_providers(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $providers = file_get_contents(base_path('bootstrap/providers.php'));
        $this->assertStringContainsString('ElectronServiceProvider', $providers);
    }

    public function test_it_creates_env_production(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $this->assertFileExists(base_path('.env.production'));
    }

    public function test_it_creates_electron_builder_config(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $this->assertFileExists(base_path('electron-builder.json'));

        // Verify it's valid JSON
        $config = json_decode(file_get_contents(base_path('electron-builder.json')), true);
        $this->assertNotNull($config);
        $this->assertArrayHasKey('appId', $config);
    }

    public function test_typescript_files_already_exist(): void
    {
        // TypeScript files are part of the codebase, not created by build:electron
        $this->assertFileExists(base_path('electron/main/index.ts'));
        $this->assertFileExists(base_path('electron/main/boot-manager.ts'));
        $this->assertFileExists(base_path('electron/main/windows.ts'));
        $this->assertFileExists(base_path('electron/main/services/config-service.ts'));
        $this->assertFileExists(base_path('electron/main/services/laravel-service.ts'));
        $this->assertFileExists(base_path('electron/main/services/health-service.ts'));
    }

    public function test_it_creates_backup_of_package_json(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $this->assertFileExists(storage_path('electron-backups/package.json.bak'));
    }

    public function test_preload_scripts_already_exist(): void
    {
        // Preload scripts are part of the codebase, not created by build:electron
        $this->assertFileExists(base_path('electron/preload/loading.ts'));
        $this->assertFileExists(base_path('electron/preload/main.ts'));
    }

    public function test_loading_screen_already_exists(): void
    {
        // Loading screen is part of the codebase, not created by build:electron
        $this->assertFileExists(base_path('electron/renderer/loading.html'));
    }

    public function test_build_scripts_already_exist(): void
    {
        // Build scripts are part of the codebase in electron/scripts/, not created by build:electron
        $this->assertFileExists(base_path('electron/scripts/setup-binaries.sh'));
        $this->assertFileExists(base_path('electron/scripts/setup-binaries.ps1'));

        // Verify shell script is executable
        $this->assertTrue(is_executable(base_path('electron/scripts/setup-binaries.sh')));
    }

    public function test_tsconfig_already_exists(): void
    {
        // tsconfig.json is part of the codebase, not created by build:electron
        $this->assertFileExists(base_path('electron/tsconfig.json'));

        // Verify it's valid JSON
        $config = json_decode(file_get_contents(base_path('electron/tsconfig.json')), true);
        $this->assertNotNull($config);
        $this->assertArrayHasKey('compilerOptions', $config);
    }

    public function test_it_updates_env_example(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $envExample = file_get_contents(base_path('.env.example'));
        $this->assertStringContainsString('ELECTRON_APP_NAME', $envExample);
        $this->assertStringContainsString('ELECTRON_BUNDLE_ID', $envExample);
        $this->assertStringContainsString('ELECTRON_DB_DRIVER', $envExample);
    }

    public function test_it_updates_package_json_with_electron_scripts(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true])
            ->assertSuccessful();

        $packageJson = json_decode(file_get_contents(base_path('package.json')), true);

        $this->assertArrayHasKey('main', $packageJson);
        $this->assertArrayHasKey('electron:dev', $packageJson['scripts']);
        $this->assertArrayHasKey('electron:build', $packageJson['scripts']);
        $this->assertArrayHasKey('electron:build:mac', $packageJson['scripts']);
        $this->assertArrayHasKey('electron:build:win', $packageJson['scripts']);
    }
}
