<?php

declare(strict_types=1);

namespace Tests\Feature\Build;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class ElectronConfigurationTest extends TestCase
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

    public function test_it_writes_correct_service_configuration(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertArrayHasKey('services', $config);
        $this->assertArrayHasKey('database', $config['services']);
        $this->assertArrayHasKey('redis', $config['services']);
        $this->assertArrayHasKey('horizon', $config['services']);
        $this->assertArrayHasKey('reverb', $config['services']);
        $this->assertArrayHasKey('scheduler', $config['services']);
    }

    public function test_it_writes_correct_port_configuration(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertArrayHasKey('ports', $config);
        $this->assertEquals(48000, $config['ports']['laravel']);
        $this->assertEquals(6379, $config['ports']['redis']);
        $this->assertEquals(48080, $config['ports']['reverb']);
    }

    public function test_it_writes_correct_app_configuration(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertArrayHasKey('app', $config);
        $this->assertArrayHasKey('name', $config['app']);
        $this->assertArrayHasKey('bundle_id', $config['app']);
    }

    public function test_it_writes_correct_build_configuration(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertArrayHasKey('build', $config);
        $this->assertArrayHasKey('platforms', $config['build']);
        $this->assertArrayHasKey('php_version', $config['build']);
        $this->assertArrayHasKey('redis_version', $config['build']);
    }

    public function test_database_defaults_to_sqlite(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertEquals('sqlite', $config['services']['database']['driver']);
    }

    public function test_redis_is_enabled_by_default(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertTrue($config['services']['redis']['enabled']);
    }

    public function test_horizon_is_enabled_by_default(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertTrue($config['services']['horizon']['enabled']);
    }

    public function test_reverb_is_disabled_by_default(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertFalse($config['services']['reverb']['enabled']);
    }

    public function test_scheduler_is_disabled_by_default(): void
    {
        $this->artisan('build:electron', ['--force' => true, '--skip-npm' => true]);

        $config = require config_path('electron.php');

        $this->assertFalse($config['services']['scheduler']['enabled']);
    }
}
