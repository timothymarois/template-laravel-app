<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Base class for tests that exercise central-side tenancy behavior.
 *
 * Skipped automatically when TENANCY_ENABLED=false. Uses RefreshDatabase so
 * each test starts with a fresh migrated DB — including the central/ folder
 * which the TenancyServiceProvider registers via `loadMigrationsFrom`.
 *
 * Forks that enable tenancy populate tests/Central/ with feature tests that
 * extend this class.
 *
 * Typical usage with Pest:
 *
 *     uses(\Tests\CentralBaseTestCase::class);
 *     it('attaches a user to a tenant', function () {
 *         // central DB is migrated; tenant_user pivot exists
 *     });
 */
abstract class CentralBaseTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // Bootstrap the framework first — config() / DB are unavailable until parent::setUp().
        parent::setUp();

        if (! config('tenancy.enabled')) {
            $this->markTestSkipped('Central tests require TENANCY_ENABLED=true.');
        }
    }
}
