<?php

declare(strict_types=1);

namespace Pandawa\Apm\Watcher;

use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Contract\WatcherInterface;
use Pandawa\Apm\Span\RequestSpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RequestWatcher implements WatcherInterface
{
    private ?string $type;

    public function __construct(private AgentInterface $agent)
    {
    }

    public function setOptions(array $options = []): void
    {
        $this->type = $options['type'] ?? 'request';
    }

    public function register(Application $app): void
    {
        $app['events']->listen(RequestHandled::class, [$this, 'recordRequest']);
    }

    public function recordRequest(RequestHandled $event): void
    {
        if (!$this->agent->hasTransaction()) {
            return;
        }

        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');

        $this->agent->addSpan(
            new RequestSpan(
                $this->type,
                Carbon::now(),
                microtime(true) - ($startTime ?? 0),
                $event->response->getStatusCode(),
                $event->request->path(),
                $event->request->userAgent()
            )
        );
        $this->agent->capture();
    }
}
