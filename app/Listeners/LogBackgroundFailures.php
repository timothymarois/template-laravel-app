<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

/**
 * Adds a queryable log line whenever background work fails.
 *
 * **Laravel already logs these.** `Queue\Worker` calls `exceptions->report()` on a failed
 * job and `ScheduleRunCommand` calls `handler->report()` on a failed task, so the sink
 * already receives the exception and its stack trace. This listener does not fix a gap in
 * coverage — it fixes a gap in *shape*.
 *
 * The framework's entry is the exception message plus a trace blob. The job class, queue,
 * attempt count and uuid appear only inside that trace string, so a sink cannot filter on
 * them. Under `LOG_CHANNEL=stderr` the context below is emitted as one-line JSON, which
 * makes each key a field: `event="job.failed"`, `job="App\Jobs\SendInvoice"`, and so on.
 *
 * The cost is a second ERROR line per failure — the trace in one, the metadata in the
 * other. A fork that does not query its logs by field can drop this listener and lose
 * nothing but the filtering.
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
