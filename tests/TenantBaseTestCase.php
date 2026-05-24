<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;

/**
 * Base class for tests that exercise per-tenant behavior. Boots a transient
 * tenant via the package's tenancy()->initialize() in setUp() and ends
 * tenancy in tearDown().
 *
 * Skipped automatically when TENANCY_ENABLED=false. Forks that enable
 * tenancy populate tests/Tenant/ with feature tests that extend this class.
 *
 * Important: this base class assumes the test environment has the central
 * connection configured (the tenancy:enable command + a working central DB).
 * Configure your test phpunit.xml or .env.testing accordingly.
 */
abstract class TenantBaseTestCase extends TestCase
{
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        if (! config('tenancy.enabled')) {
            $this->markTestSkipped('Tenant tests require TENANCY_ENABLED=true.');
        }

        $this->tenant = Tenant::create();
        app(Tenancy::class)->initialize($this->tenant);
    }

    protected function tearDown(): void
    {
        if (isset($this->tenant)) {
            app(Tenancy::class)->end();
        }

        parent::tearDown();
    }
}
