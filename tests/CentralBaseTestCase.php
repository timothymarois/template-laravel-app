<?php

declare(strict_types=1);

namespace Tests;

/**
 * Base class for tests that exercise central-side tenancy behavior.
 *
 * Skipped automatically when TENANCY_ENABLED=false. Forks that enable
 * tenancy populate tests/Central/ with feature tests that extend this class.
 *
 * Typical usage from a fork:
 *
 *     class AccountPickerTest extends \Tests\CentralBaseTestCase
 *     {
 *         public function test_picker_lists_owned_tenants(): void
 *         {
 *             // ...
 *         }
 *     }
 *
 * Or with Pest:
 *
 *     uses(\Tests\CentralBaseTestCase::class);
 *     it('lists owned tenants', function () { ... });
 */
abstract class CentralBaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! config('tenancy.enabled')) {
            $this->markTestSkipped('Central tests require TENANCY_ENABLED=true.');
        }
    }
}
