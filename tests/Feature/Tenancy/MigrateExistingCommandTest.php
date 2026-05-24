<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| tenancy:migrate-existing skeleton tests
|--------------------------------------------------------------------------
|
| The skeleton command refuses to do work until (a) tenancy is enabled
| and (b) a concrete ExistingDataMigrator is bound. These tests verify
| the refusal paths — the actual iteration loop is fork-implemented per
| docs/guidelines/tenancy-migrating.md and not tested here.
|
*/

it('refuses to run when tenancy is disabled', function () {
    // Default state: TENANCY_ENABLED=false via phpunit.xml.
    $this->artisan('tenancy:migrate-existing')
        ->expectsOutputToContain('Tenancy is disabled')
        ->assertFailed();
});

it('refuses to run with NullExistingDataMigrator and points at the migration doc', function () {
    // Temporarily flip the config + bind the null migrator to simulate the
    // enabled-but-not-configured state. This is exactly what a fork would see
    // immediately after running `tenancy:enable` and before they implement
    // their own migrator.
    config(['tenancy.enabled' => true]);
    app()->bind(
        App\Tenancy\Contracts\ExistingDataMigrator::class,
        App\Tenancy\NullExistingDataMigrator::class,
    );

    $this->artisan('tenancy:migrate-existing')
        ->expectsOutputToContain('No ExistingDataMigrator bound')
        ->expectsOutputToContain('docs/guidelines/tenancy-migrating.md')
        ->assertFailed();
});

it('errors when a concrete migrator is bound but no fork override exists', function () {
    // A fork has implemented the contract but hasn't overridden the command's
    // handle() with their iteration loop. The skeleton should fail loudly
    // rather than do nothing silently.
    config(['tenancy.enabled' => true]);

    $stub = new class implements App\Tenancy\Contracts\ExistingDataMigrator
    {
        public function tablesToMoveToTenant(): array
        {
            return [];
        }

        public function userMapper(): Closure
        {
            return fn () => [];
        }

        public function tenantProvisioner(): Closure
        {
            return fn () => [];
        }
    };
    app()->instance(App\Tenancy\Contracts\ExistingDataMigrator::class, $stub);

    $this->artisan('tenancy:migrate-existing')
        ->expectsOutputToContain('iteration loop is fork-implemented')
        ->assertFailed();
});
