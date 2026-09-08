<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

/**
 * Writes a structured log line whenever background work fails.
 *
 * Without this, background failures are invisible to the production log sink. A failed
 * job lands in `failed_jobs` and Horizon's UI, neither of which a stderr-based shipper
 * reads; a failed *scheduled task* is recorded nowhere at all, so a task that has been
 * throwing for a week looks identical to one that simply had nothing to do.
 *
 * Production logs to stderr as one-line JSON (`LOG_CHANNEL=stderr`), so the context
 * array below is what becomes queryable in the sink — hence the flat, named keys.
 */
class LogBackgroundFailures
{
    public function jobFailed(JobFailed $event): void
    {
        Log::error('Queued job failed', [
            'event' => 'job.failed',
            'job' => $event->job->resolveName(),
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'attempts' => $event->job->attempts(),
            'job_uuid' => $event->job->uuid(),
            'exception' => $event->exception::class,
            'message' => $event->exception->getMessage(),
        ]);
    }

    public function scheduledTaskFailed(ScheduledTaskFailed $event): void
    {
        Log::error('Scheduled task failed', [
            'event' => 'schedule.failed',
            // `getSummaryForDisplay` is the description when one is set and the command
            // string otherwise, so the line always names something greppable.
            'task' => $event->task->getSummaryForDisplay(),
            'expression' => $event->task->expression,
            'exception' => $event->exception::class,
            'message' => $event->exception->getMessage(),
        ]);
    }
}
