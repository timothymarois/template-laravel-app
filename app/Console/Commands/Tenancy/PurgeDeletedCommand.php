<?php

declare(strict_types=1);

namespace App\Console\Commands\Tenancy;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

/**
 * Force-deletes soft-deleted tenants older than the configured grace window.
 *
 * Each soft-deleted tenant whose `deleted_at` timestamp is older than
 * `config('tenancy.purge_deleted_after_hours')` (default 72) is forceDelete()'d,
 * which fires the TenantDeleted event with isForceDeleting()=true and triggers
 * the per-tenant DB drop via ConditionalDeleteTenantDatabase.
 *
 * Schedule this in app/Console/Kernel.php (or Laravel 11+ routes/console.php):
 *
 *     Schedule::command('tenancy:purge-deleted')->hourly();
 *
 * The grace window gives operators time to restore an accidentally-deleted
 * tenant via $tenant->restore() before the per-tenant DB is permanently gone.
 */
class PurgeDeletedCommand extends Command
{
    protected $signature = 'tenancy:purge-deleted
                            {--hours= : Override config(tenancy.purge_deleted_after_hours) for this run}
                            {--dry-run : Report what would be purged without actually deleting}
                            {--keep-orphan-users : Skip the orphan-user sweep after purging tenants (default: orphans are removed)}';

    protected $description = 'Force-delete soft-deleted tenants older than the configured grace period, then remove any users left without tenants.';

    public function handle(): int
    {
        if (! config('tenancy.enabled')) {
            $this->error('Tenancy is disabled. Run `php artisan tenancy:enable` first.');

            return self::FAILURE;
        }

        $hours = (int) ($this->option('hours') ?? config('tenancy.purge_deleted_after_hours', 72));

        // Refuse to run with a zero/negative grace window — that means a
        // misconfigured env (e.g. TENANCY_PURGE_DELETED_AFTER_HOURS="72h"
        // truncates to 72, but "abc" casts to 0, which would purge every
        // soft-deleted tenant immediately on the next cron tick).
        if ($hours < 1) {
            $this->error("purge_deleted_after_hours resolved to {$hours} — refusing to run with a zero or negative grace window.");

            return self::FAILURE;
        }

        $cutoff = now()->subHours($hours);
        $dryRun = (bool) $this->option('dry-run');

        /** @var Collection<int, Tenant> $candidates */
        $candidates = Tenant::onlyTrashed()
            ->where('deleted_at', '<=', $cutoff)
            ->get();

        if ($candidates->count() === 0) {
            $this->info("No soft-deleted tenants older than {$hours}h. Nothing to purge.");

            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY RUN] Would purge ' : 'Purging ').
            $candidates->count().
            " tenant(s) soft-deleted before {$cutoff->toIso8601String()}:"
        );

        // Snapshot the user IDs that were members of any purged tenant BEFORE
        // we force-delete the tenants (the pivot rows cascade away when the
        // tenant row is removed). After purging, we re-check each of these
        // users and remove ones with zero remaining tenants (subject to the
        // SuperAdmin exemption and the --keep-orphan-users opt-out).
        $candidateIds = $candidates->pluck('id')->all();
        /** @var array<int> $exMemberIds */
        $exMemberIds = \DB::table('tenant_user')
            ->whereIn('tenant_id', $candidateIds)
            ->pluck('user_id')
            ->unique()
            ->all();

        $purged = 0;
        /** @var Tenant $tenant */
        foreach ($candidates as $tenant) {
            $deletedAt = $tenant->getAttribute('deleted_at');
            $deletedAtStr = $deletedAt instanceof \DateTimeInterface ? $deletedAt->format(DATE_ATOM) : '(unknown)';
            $this->line("  - {$tenant->getKey()} (deleted_at: {$deletedAtStr})");

            if ($dryRun) {
                continue;
            }

            // Re-read the row before destroying it. An operator may have
            // called $tenant->restore() between the initial get() above and
            // now; forceDelete() on a restored tenant would bypass the
            // soft-delete guard and silently destroy live data.
            $tenant->refresh();
            if (! $tenant->trashed()) {
                $this->warn('    skipped — tenant was restored since the run started.');

                continue;
            }

            $tenant->forceDelete();
            $purged++;
        }

        $this->info($dryRun ? 'Dry run complete — no tenants deleted.' : "Purged {$purged} tenant(s).");

        // Orphan-user sweep. Scope is intentionally narrow: only consider users
        // who WERE members of the just-purged tenants. A user who was already
        // a tenantless user before this run (e.g. a freshly-registered user
        // who hasn't joined a tenant yet) is NOT touched — purging tenants
        // shouldn't garbage-collect strangers.
        if ($this->option('keep-orphan-users')) {
            return self::SUCCESS;
        }

        if (count($exMemberIds) === 0) {
            return self::SUCCESS;
        }

        $orphans = User::whereIn('id', $exMemberIds)
            ->where(function ($q): void {
                // SuperAdmin exemption — admins may legitimately have no tenant
                // (they manage the system, not a specific tenant). Forks that
                // want to also remove orphan SuperAdmins can drop this clause.
                $q->whereNull('role')->orWhere('role', '!=', UserRole::SuperAdmin->value);
            })
            ->whereDoesntHave('tenants')
            ->get();

        if ($orphans->count() === 0) {
            $this->info('No orphan users to remove.');

            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY RUN] Would remove ' : 'Removing ').
            $orphans->count().
            ' orphan user(s) (members of purged tenants who now belong to zero tenants):'
        );

        foreach ($orphans as $orphan) {
            $this->line("  - user#{$orphan->id} {$orphan->email}");

            if (! $dryRun) {
                $orphan->delete();
            }
        }

        $this->info($dryRun ? 'Dry run complete — no users deleted.' : "Removed {$orphans->count()} orphan user(s).");

        return self::SUCCESS;
    }
}
