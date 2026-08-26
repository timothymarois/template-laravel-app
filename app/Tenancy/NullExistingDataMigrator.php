<?php

declare(strict_types=1);

namespace App\Tenancy;

use App\Tenancy\Contracts\ExistingDataMigrator;
use Closure;
use RuntimeException;

/**
 * Default binding for ExistingDataMigrator. Every method throws an informative
 * error directing the operator to implement and bind a real migrator before
 * running `php artisan tenancy:migrate-existing`.
 *
 * The template ships this so that the migrate-existing command fails loudly
 * with actionable guidance instead of silently doing the wrong thing.
 */
class NullExistingDataMigrator implements ExistingDataMigrator
{
    public function tablesToMoveToTenant(): array
    {
        throw new RuntimeException($this->error());
    }

    public function userMapper(): Closure
    {
        throw new RuntimeException($this->error());
    }

    public function tenantProvisioner(): Closure
    {
        throw new RuntimeException($this->error());
    }

    private function error(): string
    {
        return 'No ExistingDataMigrator implementation is bound. Implement '
            .'App\Tenancy\Contracts\ExistingDataMigrator and bind it in '
            .'AppServiceProvider::register(). See docs/guides/tenancy-migrating.md.';
    }
}
