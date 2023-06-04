<?php

declare(strict_types=1);

namespace Pandawa\Apm\Contract;

use Illuminate\Foundation\Application;
use Pandawa\Apm\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface AgentInterface
{
    public function start(Application $app): bool;

    public function hasTransaction(): bool;

    public function setCurrentTransaction(Transaction $transaction): void;

    public function getCurrentTransaction(): ?Transaction;

    public function addSpan(SpanInterface $span): void;

    public function capture(): void;
}
