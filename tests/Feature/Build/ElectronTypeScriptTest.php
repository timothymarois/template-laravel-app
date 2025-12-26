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
        $this->files = new Filesystem;
    }

    protected function tearDown(): void
    {
        $this->artisan('build:electron', ['--rollback' => true, '--force' => true]);
        parent::tearDown();
    }

    public function test_tsconfig_is_valid_json(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

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
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $tsconfig = json_decode(
            file_get_contents(base_path('electron/tsconfig.json')),
            true
        );

        $compilerOptions = $tsconfig['compilerOptions'];

        $this->assertEquals('ES2022', $compilerOptions['target']);
        $this->assertEquals('CommonJS', $compilerOptions['module']);
        $this->assertEquals('dist', $compilerOptions['outDir']);
        $this->assertTrue($compilerOptions['strict']);
    }

    public function test_all_typescript_files_have_valid_syntax(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $tsFiles = [
            base_path('electron/main/index.ts'),
            base_path('electron/main/boot-manager.ts'),
            base_path('electron/main/windows.ts'),
            base_path('electron/main/services/config-service.ts'),
            base_path('electron/main/services/port-service.ts'),
            base_path('electron/main/services/process-service.ts'),
            base_path('electron/main/services/laravel-service.ts'),
            base_path('electron/main/services/health-service.ts'),
            base_path('electron/preload/loading.ts'),
            base_path('electron/preload/main.ts'),
        ];

        foreach ($tsFiles as $file) {
            $this->assertFileExists($file, "TypeScript file should exist: {$file}");

            // Check file is not empty
            $content = file_get_contents($file);
            $this->assertNotEmpty($content, "TypeScript file should not be empty: {$file}");

            // Basic syntax checks
            $this->assertStringContainsString('export', $content, "TypeScript file should have exports: {$file}");
        }
    }

    public function test_main_entry_point_imports_boot_manager(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $indexContent = file_get_contents(base_path('electron/main/index.ts'));

        $this->assertStringContainsString('import { BootManager }', $indexContent);
        $this->assertStringContainsString("from './boot-manager'", $indexContent);
    }

    public function test_boot_manager_imports_services(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $bootManagerContent = file_get_contents(base_path('electron/main/boot-manager.ts'));

        $this->assertStringContainsString('ConfigService', $bootManagerContent);
        $this->assertStringContainsString('PortService', $bootManagerContent);
        $this->assertStringContainsString('ProcessService', $bootManagerContent);
        $this->assertStringContainsString('LaravelService', $bootManagerContent);
    }

    public function test_preload_scripts_use_context_bridge(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

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
        $this->artisan('build:electron', ['--force' => true]);

        // Check if TypeScript is available
        $tscCheck = Process::run('npx tsc --version');
        if (! $tscCheck->successful()) {
            $this->markTestSkipped('TypeScript not available');
        }

        // Run TypeScript compilation (type-check only)
        $result = Process::path(base_path())
            ->timeout(120)
            ->run('npx tsc -p electron --noEmit');

        $this->assertTrue(
            $result->successful(),
            'TypeScript compilation failed: '.$result->errorOutput()
        );
    }
}
