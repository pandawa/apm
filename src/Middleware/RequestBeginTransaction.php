<?php

declare(strict_types=1);

namespace Pandawa\Apm\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pandawa\Apm\Contract\AgentInterface;
use Pandawa\Apm\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RequestBeginTransaction
{
    public function handle(Request $request, Closure $next)
    {
        if (!$this->agent()->hasTransaction()) {
            $this->setupTransaction($request);
        }

        return $next($request);
    }

    private function setupTransaction(Request $request): void
    {
        $this->agent()->setCurrentTransaction(
            new Transaction(
                $this->getTransactionName($request),
                $this->getTransactionType()
            )
        );
    }

    private function getTransactionName(Request $request): string
    {
        return sprintf('%s %s', $request->method(), $this->getPath($request));
    }

    private function getTransactionType(): string
    {
        return 'request';
    }

    private function getPath(Request $request): string
    {
        return '/' . ltrim($request->path(), '/');
    }

    private function agent(): AgentInterface
    {
        return app()->get(AgentInterface::class);
    }
}
