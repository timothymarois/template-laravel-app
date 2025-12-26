<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class ElectronTypeScriptTest extends TestCase
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
        @unlink(app_path('Providers/ElectronServiceProvider.php'));
        @unlink(config_path('electron.php'));

        try {
            $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        } catch (\Throwable $e) {
            // Ignore errors during cleanup
        }
    }

    public function test_tsconfig_is_valid_json(): void
    {
        // tsconfig.json is part of the codebase, no need to run build:electron
        $tsconfig = json_decode(
            file_get_contents(base_path('electron/tsconfig.json')),
            true
        );

        $this->assertNotNull($tsconfig);
        $this->assertArrayHasKey('compilerOptions', $tsconfig);
        $this->assertArrayHasKey('include', $tsconfig);
    }

    public function test_tsconfig_has_correct_compiler_options(): void
    {
        // tsconfig.json is part of the codebase, no need to run build:electron
        $tsconfig = json_decode(
            file_get_contents(base_path('electron/tsconfig.json')),
            true
        );

        $compilerOptions = $tsconfig['compilerOptions'];

        $this->assertEquals('ES2022', $compilerOptions['target']);
        $this->assertEquals('CommonJS', $compilerOptions['module']);
        $this->assertEquals('dist', $compilerOptions['outDir']);
        $this->assertFalse($compilerOptions['strict']);
    }

    public function test_all_typescript_files_have_valid_syntax(): void
    {
        // All TypeScript files are part of the codebase, no need to run build:electron

        // Entry point files (don't require exports)
        $entryPoints = [
            base_path('electron/main/index.ts'),
        ];

        // Module files (require exports)
        $moduleFiles = [
            base_path('electron/main/boot-manager.ts'),
            base_path('electron/main/windows.ts'),
            base_path('electron/main/services/config-service.ts'),
            base_path('electron/main/services/port-service.ts'),
            base_path('electron/main/services/process-service.ts'),
            base_path('electron/main/services/laravel-service.ts'),
            base_path('electron/main/services/health-service.ts'),
        ];

        // Preload scripts (use contextBridge, not exports)
        $preloadFiles = [
            base_path('electron/preload/loading.ts'),
            base_path('electron/preload/main.ts'),
        ];

        // Check entry points exist and are not empty
        foreach ($entryPoints as $file) {
            $this->assertFileExists($file, "TypeScript entry point should exist: {$file}");
            $content = file_get_contents($file);
            $this->assertNotEmpty($content, "TypeScript entry point should not be empty: {$file}");
        }

        // Check module files exist, are not empty, and have exports
        foreach ($moduleFiles as $file) {
            $this->assertFileExists($file, "TypeScript module should exist: {$file}");
            $content = file_get_contents($file);
            $this->assertNotEmpty($content, "TypeScript module should not be empty: {$file}");
            $this->assertStringContainsString('export', $content, "TypeScript module should have exports: {$file}");
        }

        // Check preload files exist, are not empty, and use contextBridge
        foreach ($preloadFiles as $file) {
            $this->assertFileExists($file, "Preload script should exist: {$file}");
            $content = file_get_contents($file);
            $this->assertNotEmpty($content, "Preload script should not be empty: {$file}");
            $this->assertStringContainsString('contextBridge', $content, "Preload script should use contextBridge: {$file}");
        }
    }

    public function test_main_entry_point_imports_boot_manager(): void
    {
        // index.ts is part of the codebase, no need to run build:electron
        $indexContent = file_get_contents(base_path('electron/main/index.ts'));

        $this->assertStringContainsString('import { BootManager }', $indexContent);
        $this->assertStringContainsString("from './boot-manager'", $indexContent);
    }

    public function test_boot_manager_imports_services(): void
    {
        // boot-manager.ts is part of the codebase, no need to run build:electron
        $bootManagerContent = file_get_contents(base_path('electron/main/boot-manager.ts'));

        $this->assertStringContainsString('ConfigService', $bootManagerContent);
        $this->assertStringContainsString('PortService', $bootManagerContent);
        $this->assertStringContainsString('ProcessService', $bootManagerContent);
        $this->assertStringContainsString('LaravelService', $bootManagerContent);
    }

    public function test_preload_scripts_use_context_bridge(): void
    {
        // Preload scripts are part of the codebase, no need to run build:electron
        $loadingPreload = file_get_contents(base_path('electron/preload/loading.ts'));
        $mainPreload = file_get_contents(base_path('electron/preload/main.ts'));

        $this->assertStringContainsString('contextBridge', $loadingPreload);
        $this->assertStringContainsString('contextBridge', $mainPreload);
        $this->assertStringContainsString('exposeInMainWorld', $loadingPreload);
        $this->assertStringContainsString('exposeInMainWorld', $mainPreload);
    }

    /**
     * @group slow
     */
    public function test_typescript_files_compile_without_errors(): void
    {
        // Check if TypeScript is available
        $tscCheck = Process::run('npx tsc --version');
        if (! $tscCheck->successful()) {
            $this->markTestSkipped('TypeScript not available');
        }

        // Check if electron is installed (required for type checking)
        $packageJson = json_decode(file_get_contents(base_path('package.json')), true);
        $hasElectron = isset($packageJson['devDependencies']['electron'])
            || isset($packageJson['dependencies']['electron']);

        if (! $hasElectron) {
            $this->markTestSkipped('Electron not installed - run build:electron first');
        }

        // Run TypeScript compilation (type-check only)
        // The electron directory is part of the codebase, no need to run build:electron
        $result = Process::path(base_path())
            ->timeout(120)
            ->run('npx tsc -p electron --noEmit');

        $this->assertTrue(
            $result->successful(),
            'TypeScript compilation failed: '.$result->errorOutput()
        );
    }
}
