<?php

declare(strict_types=1);

namespace Pandawa\Apm\Watcher;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\CallQueuedClosure;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Jobs\Job as QueueJob;
use Illuminate\Foundation\Application;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobQueued;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Contract\WatcherInterface;
use Pandawa\Apm\Span\JobSpan;
use Pandawa\Apm\Transaction;
use Pandawa\Component\Message\QueueEnvelope;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class JobWatcher implements WatcherInterface
{
    private ?string $type;

    public function __construct(private AgentInterface $agent)
    {
    }

    public function setOptions(array $options = []): void
    {
        $this->type = $options['type'] ?? 'job';
    }

    public function register(Application $app): void
    {
        $app['events']->listen(JobProcessing::class, function (JobProcessing $event) {
            $this->startTransactionIfNotExists($event->job);
        });

        $app['events']->listen(JobQueued::class, [$this, 'recordQueuedJob']);
        $app['events']->listen(JobProcessed::class, [$this, 'recordProcessedJob']);
        $app['events']->listen(JobFailed::class, [$this, 'recordFailedJob']);
    }

    public function recordQueuedJob(JobQueued $event): void
    {
        $this->startTransactionIfNotExists($event->job);

        $this->agent->addSpan(
            new JobSpan(
                $this->type,
                'queued',
                $this->getJobName($event->job),
                Carbon::now(),
            )
        );
    }

    public function recordProcessedJob(JobProcessed $event): void
    {
        $this->startTransactionIfNotExists($event->job);

        $this->agent->addSpan(
            new JobSpan(
                $this->type,
                'processed',
                $this->getJobName($event->job),
                Carbon::now(),
            )
        );
        $this->agent->capture();
    }

    public function recordFailedJob(JobFailed $event): void
    {
        $this->startTransactionIfNotExists($event->job);

        $this->agent->addSpan(
            new JobSpan(
                $this->type,
                'failed',
                $this->getJobName($event->job),
                Carbon::now(),
            )
        );
        $this->agent->capture();
    }

    private function startTransactionIfNotExists(object|callable|string $job): void
    {
        if (!$this->agent->hasTransaction()) {
            $this->agent->setCurrentTransaction(
                new Transaction(
                    $this->getJobName($job),
                    $this->type,
                )
            );
        }
    }

    private function getJobName(object|callable|string $job): string
    {
        if (is_string($job)) {
            return $job;
        }

        if ($job instanceof CallQueuedClosure) {
            return $job->displayName();
        }

        if ($job instanceof QueueEnvelope) {
            return get_class($job->getCommand());
        }

        if (is_callable($job)) {
            return 'callable';
        }

        if ($job instanceof Job) {
            return $job->resolveName();
        }

        if (is_object($job)) {
            return get_class($job);
        }

        return 'Unknown';
    }
}
