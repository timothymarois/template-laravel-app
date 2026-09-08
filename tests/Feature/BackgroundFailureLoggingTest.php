<?php

declare(strict_types=1);

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Log;

/*
| A failed job reaches failed_jobs and Horizon's UI; a failed scheduled task reaches
| nothing at all. Production ships stderr to the log sink, so without these listeners a
| task that has been throwing for a week looks exactly like one that had nothing to do.
*/

it('logs a failed queued job with the context needed to find it', function () {
    Log::spy();

    $job = Mockery::mock(Job::class);
    $job->shouldReceive('resolveName')->andReturn('App\Jobs\SendInvoice');
    $job->shouldReceive('getQueue')->andReturn('default');
    $job->shouldReceive('attempts')->andReturn(3);
    $job->shouldReceive('uuid')->andReturn('9f1c-uuid');
    // Horizon listens to JobFailed too and reads the id off the job.
    $job->shouldReceive('getJobId')->andReturn('9f1c-uuid');
    $job->shouldReceive('getConnectionName')->andReturn('redis');

    event(new JobFailed('redis', $job, new RuntimeException('gateway timeout')));

    Log::shouldHaveReceived('error')
        ->once()
        ->withArgs(function (string $message, array $context) {
            expect($message)->toBe('Queued job failed')
                ->and($context['event'])->toBe('job.failed')
                ->and($context['job'])->toBe('App\Jobs\SendInvoice')
                ->and($context['connection'])->toBe('redis')
                ->and($context['queue'])->toBe('default')
                ->and($context['attempts'])->toBe(3)
                ->and($context['job_uuid'])->toBe('9f1c-uuid')
                ->and($context['exception'])->toBe(RuntimeException::class)
                ->and($context['message'])->toBe('gateway timeout');

            return true;
        });
});

it('logs a failed scheduled task, which nothing else records', function () {
    Log::spy();

    $task = (new Schedule)->command('health:check')->everyMinute();

    event(new ScheduledTaskFailed($task, new RuntimeException('check blew up')));

    Log::shouldHaveReceived('error')
        ->once()
        ->withArgs(function (string $message, array $context) {
            expect($message)->toBe('Scheduled task failed')
                ->and($context['event'])->toBe('schedule.failed')
                ->and($context['task'])->toContain('health:check')
                ->and($context['expression'])->toBe('* * * * *')
                ->and($context['message'])->toBe('check blew up');

            return true;
        });
});
