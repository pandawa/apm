<?php

declare(strict_types=1);

namespace Pandawa\Apm\Contract;

use Pandawa\Apm\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface SpanInterface
{
    public function setTransaction(Transaction $transaction): void;

    public function getName(): string;

    public function getType(): string;

    public function getSubType(): string;

    public function getAction(): string;

    public function getLabels(): array;
}
