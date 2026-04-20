<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->tempStorage = sys_get_temp_dir().'/ensure-storage-'.uniqid('', true);
    mkdir($this->tempStorage, 0775, true);
    $this->app->useStoragePath($this->tempStorage);
});

afterEach(function () {
    if (isset($this->tempStorage) && is_dir($this->tempStorage)) {
        File::deleteDirectory($this->tempStorage);
    }
});

it('creates every required storage directory when none exist', function () {
    $this->artisan('app:ensure-storage')->assertSuccessful();

    $expected = [
        'app',
        'app/public',
        'framework',
        'framework/cache',
        'framework/cache/data',
        'framework/sessions',
        'framework/testing',
        'framework/views',
        'logs',
    ];

    foreach ($expected as $dir) {
        expect(is_dir($this->tempStorage.'/'.$dir))
            ->toBeTrue("expected storage/{$dir} to exist");
        expect(is_file($this->tempStorage.'/'.$dir.'/.gitignore'))
            ->toBeTrue("expected storage/{$dir}/.gitignore to exist");
    }
});

it('writes the data-preserving .gitignore in framework/cache', function () {
    $this->artisan('app:ensure-storage')->assertSuccessful();

    $contents = (string) file_get_contents($this->tempStorage.'/framework/cache/.gitignore');
    expect($contents)->toContain('!data/');
});

it('restores a missing .gitignore even if the directory exists', function () {
    mkdir($this->tempStorage.'/framework/views', 0775, true);
    expect(is_file($this->tempStorage.'/framework/views/.gitignore'))->toBeFalse();

    $this->artisan('app:ensure-storage')
        ->doesntExpectOutput('  created  storage/framework/views')
        ->expectsOutput('  created  storage/framework/views/.gitignore')
        ->assertSuccessful();

    expect(is_file($this->tempStorage.'/framework/views/.gitignore'))->toBeTrue();
});

it('is idempotent on repeated runs', function () {
    $this->artisan('app:ensure-storage')->assertSuccessful();

    $this->artisan('app:ensure-storage')
        ->doesntExpectOutput('  created  storage/app')
        ->doesntExpectOutput('  created  storage/logs')
        ->expectsOutput('Storage ready.')
        ->assertSuccessful();
});

it('only creates directories that are missing', function () {
    mkdir($this->tempStorage.'/app', 0775, true);
    mkdir($this->tempStorage.'/app/public', 0775, true);
    mkdir($this->tempStorage.'/logs', 0775, true);

    $this->artisan('app:ensure-storage')
        ->doesntExpectOutput('  created  storage/app')
        ->doesntExpectOutput('  created  storage/app/public')
        ->doesntExpectOutput('  created  storage/logs')
        ->expectsOutput('  created  storage/framework')
        ->expectsOutput('  created  storage/framework/views')
        ->assertSuccessful();

    expect(is_dir($this->tempStorage.'/framework/views'))->toBeTrue();
});

it('skips passport key generation when passport is not installed', function () {
    expect(class_exists('Laravel\\Passport\\PassportServiceProvider'))->toBeFalse();

    $this->artisan('app:ensure-storage')
        ->doesntExpectOutput('  generating OAuth signing keys…')
        ->expectsOutput('Storage ready.')
        ->assertSuccessful();

    expect(is_file($this->tempStorage.'/oauth-private.key'))->toBeFalse();
});
