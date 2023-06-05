<?php

declare(strict_types=1);

namespace Pandawa\Apm\Middleware;

use Closure;
use Illuminate\Queue\CallQueuedClosure;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Span\MessageSpan;
use Pandawa\Component\Message\AbstractCommand;
use Pandawa\Component\Message\AbstractQuery;
use Pandawa\Component\Message\QueueEnvelope;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RecordDispatched
{
    public function handle($message, Closure $next)
    {
        if ($this->skipRecords($message)) {
            return $next($message);
        }

        $startTime = microtime(true);

        $response = $next($message);

        $this->agent()->addSpan(
            new MessageSpan(
                'message',
                $this->getMessageType($message),
                $this->getMessageName($message),
                microtime(true) - $startTime
            )
        );

        return $response;
    }

    private function skipRecords($message): bool
    {
        if (!$this->agent()->hasTransaction()) {
            return true;
        }

        if ($message instanceof CallQueuedClosure) {
            return true;
        }

        if ($message instanceof QueueEnvelope) {
            return true;
        }

        return false;
    }

    private function getMessageName($message): string
    {
        if (is_string($message)) {
            return $message;
        }

        if (is_object($message)) {
            return get_class($message);
        }

        if (is_callable($message)) {
            return 'callable';
        }

        return 'unknown';
    }

    private function getMessageType($message): string
    {
        if ($message instanceof AbstractCommand) {
            return 'command';
        }

        if ($message instanceof AbstractQuery) {
            return 'query';
        }

        return 'message';
    }

    private function agent(): AgentInterface
    {
        return app()->get(AgentInterface::class);
    }
}
