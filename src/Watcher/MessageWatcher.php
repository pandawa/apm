<?php

declare(strict_types=1);

namespace Pandawa\Apm\Watcher;

use Closure;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Foundation\Application;
use Pandawa\Apm\Contract\WatcherInterface;
use Pandawa\Apm\Middleware\RecordDispatched;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageWatcher implements WatcherInterface
{
    use WatcherHasNoOptionsTrait;

    public function register(Application $app): void
    {
        if (!$app->bound(Dispatcher::class)) {
            return;
        }

        $bus = $app->make(Dispatcher::class);
        $pipes = Closure::bind(fn($object) => $object->pipes, null, $bus)->__invoke($bus);

        if (false === array_search(RecordDispatched::class, $pipes)) {
            array_unshift($pipes, RecordDispatched::class);
        }

        $bus->pipeThrough($pipes);
    }
}
