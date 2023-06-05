<?php

declare(strict_types=1);

namespace Pandawa\Apm\Watcher;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Application;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Contract\WatcherInterface;
use Pandawa\Apm\Span\QuerySpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class QueryWatcher implements WatcherInterface
{
    private ?string $type;

    public function setOptions(array $options = []): void
    {
        $this->type = $options['type'] ?? 'db';
    }

    public function register(Application $app): void
    {
        $app['events']->listen(QueryExecuted::class, [$this, 'recordQuery']);
    }

    public function recordQuery(QueryExecuted $event): void
    {
        if (!$this->agent()->hasTransaction()) {
            return;
        }

        $this->agent()->addSpan(
            new QuerySpan(
                $this->type,
                $event->connectionName,
                $event->sql,
                $event->time
            )
        );
    }

    private function agent(): AgentInterface
    {
        return app()->get(AgentInterface::class);
    }
}
