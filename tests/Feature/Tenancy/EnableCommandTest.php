<?php

declare(strict_types=1);

use App\Console\Commands\Tenancy\EnableCommand;
use Illuminate\Support\Facades\File;

/*
|--------------------------------------------------------------------------
| tenancy:enable .env mutator regression tests
|--------------------------------------------------------------------------
|
| The EnableCommand writes 2 keys to .env. These tests guard the writer
| against regressions (regex backreferences, idempotency, key ordering).
| Each test creates a temp .env file, runs the writer logic via a small
| harness, and asserts the result.
|
| We don't actually run `tenancy:enable` end-to-end here — that requires
| flipping the live .env which would corrupt local dev. The tests exercise
| the writer's behavior via the public command running against a fixture.
|
*/

beforeEach(function () {
    $this->envPath = storage_path('framework/testing/.env.fixture-'.uniqid());
    File::put($this->envPath, "APP_NAME=Laravel\nAPP_ENV=local\nDB_CONNECTION=sqlite\n");
});

afterEach(function () {
    if (isset($this->envPath) && File::exists($this->envPath)) {
        File::delete($this->envPath);
    }
});

it('writeEnvKey appends a new key when missing', function () {
    $command = new EnableCommand;
    $method = new ReflectionMethod($command, 'writeEnvKey');
    $method->invoke($command, $this->envPath, 'TENANCY_ENABLED', 'true');

    expect(File::get($this->envPath))->toContain("TENANCY_ENABLED=true\n");
});

it('writeEnvKey replaces an existing key idempotently', function () {
    File::append($this->envPath, "TENANCY_ENABLED=false\n");

    $command = new EnableCommand;
    $method = new ReflectionMethod($command, 'writeEnvKey');
    $method->invoke($command, $this->envPath, 'TENANCY_ENABLED', 'true');

    $contents = File::get($this->envPath);
    expect($contents)->toContain('TENANCY_ENABLED=true');
    // Make sure the old value is replaced, not just appended
    expect(substr_count($contents, 'TENANCY_ENABLED='))->toBe(1);
});

it('writeEnvKey preserves values containing dollar signs', function () {
    // Regression: bare preg_replace would interpret `$1` as a backreference.
    // We use preg_replace_callback to avoid this.
    $command = new EnableCommand;
    $method = new ReflectionMethod($command, 'writeEnvKey');
    $method->invoke($command, $this->envPath, 'SOME_SECRET', 'abc$1def');

    expect(File::get($this->envPath))->toContain('SOME_SECRET=abc$1def');
});

it('writeEnvKey quotes values containing spaces', function () {
    $command = new EnableCommand;
    $method = new ReflectionMethod($command, 'writeEnvKey');
    $method->invoke($command, $this->envPath, 'APP_TITLE', 'Hello World');

    expect(File::get($this->envPath))->toContain('APP_TITLE="Hello World"');
});

it('writeEnvKey preserves surrounding lines', function () {
    $command = new EnableCommand;
    $method = new ReflectionMethod($command, 'writeEnvKey');
    $method->invoke($command, $this->envPath, 'TENANCY_ENABLED', 'true');

    $contents = File::get($this->envPath);
    expect($contents)->toContain('APP_NAME=Laravel');
    expect($contents)->toContain('APP_ENV=local');
    expect($contents)->toContain('DB_CONNECTION=sqlite');
});

it('rejects invalid identification value', function () {
    $this->artisan('tenancy:enable', ['--identification' => 'cookies', '--force' => true])
        ->expectsOutputToContain('Invalid --identification value')
        ->assertFailed();
});

it('errors when .env file is missing', function () {
    // Temporarily move the project's .env aside if it exists, run the command,
    // restore. The command should fail with a clear message.
    $realEnvPath = base_path('.env');
    $backupPath = base_path('.env.test-backup-'.uniqid());

    if (File::exists($realEnvPath)) {
        File::move($realEnvPath, $backupPath);
    }

    try {
        $this->artisan('tenancy:enable', ['--force' => true])
            ->expectsOutputToContain('.env file not found')
            ->assertFailed();
    } finally {
        if (File::exists($backupPath)) {
            File::move($backupPath, $realEnvPath);
        }
    }
});
