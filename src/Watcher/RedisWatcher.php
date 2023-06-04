<?php

declare(strict_types=1);

namespace Pandawa\Apm\Watcher;

use Illuminate\Foundation\Application;
use Illuminate\Redis\Events\CommandExecuted;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Contract\WatcherInterface;
use Pandawa\Apm\Span\RedisSpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisWatcher implements WatcherInterface
{
    private ?string $type;

    public function __construct(private AgentInterface $agent)
    {
    }

    public function setOptions(array $options = []): void
    {
        $this->type = $options['type'] ?? 'redis';
    }

    public function register(Application $app): void
    {
        if (!$app->bound('redis')) {
            return;
        }

        $app['events']->listen(CommandExecuted::class, [$this, 'recordCommand']);

        foreach ((array) $app['redis']->connections() as $connection) {
            $connection->setEventDispatcher($app['events']);
        }

        $app['redis']->enableEvents();
    }

    public function recordCommand(CommandExecuted $event): void
    {
        if (!$this->agent->hasTransaction()) {
            return;
        }

        $this->agent->addSpan(
            new RedisSpan(
                $this->type,
                $event->command,
                $event->connectionName,
                $event->time
            )
        );
    }
}
