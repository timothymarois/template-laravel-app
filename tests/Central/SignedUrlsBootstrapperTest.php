<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Tenancy\Bootstrappers\SignedUrls;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Stancl\Tenancy\Events\TenantCreated;
use Tests\CentralBaseTestCase;

/*
|--------------------------------------------------------------------------
| SignedUrls bootstrapper — direct invocation tests
|--------------------------------------------------------------------------
|
| The bootstrapper rewrites the URL root when a tenant is initialized so
| signed URLs (password reset / email verification / signed magic links)
| compute their signature against the tenant's host rather than central.
|
| It ships listed-but-commented-out in config/tenancy.php because path
| mode (the default) doesn't need it. These tests exercise the methods
| directly without registering the bootstrapper.
|
*/

uses(CentralBaseTestCase::class);

beforeEach(function () {
    Event::fake([TenantCreated::class]);
});

it('bootstrap() rewrites the URL root to use the tenant domain', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->domains()->create(['domain' => 'acme.example.com']);

    $originalRoot = url('/');

    (new SignedUrls)->bootstrap($tenant);

    expect(url('/'))->toContain('acme.example.com');
    expect(url('/'))->not->toBe($originalRoot);
});

it('bootstrap() is a no-op when the tenant has no domains', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);

    $before = url('/');
    (new SignedUrls)->bootstrap($tenant);
    $after = url('/');

    expect($after)->toBe($before);
});

it('revert() restores the URL root to config(app.url)', function () {
    $tenant = Tenant::create(['id' => (string) Str::uuid()]);
    $tenant->domains()->create(['domain' => 'acme.example.com']);

    $bootstrapper = new SignedUrls;
    $bootstrapper->bootstrap($tenant);
    expect(url('/'))->toContain('acme.example.com');

    $bootstrapper->revert();

    expect(url('/'))->toBe(rtrim((string) config('app.url'), '/'));
});
