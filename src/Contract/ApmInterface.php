<?php

declare(strict_types=1);

namespace Pandawa\Apm\Contract;

use Pandawa\Apm\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ApmInterface
{
    public function capture(Transaction $transaction): void;

    public function name(): string;
}
