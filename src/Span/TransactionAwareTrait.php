<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

use Pandawa\Apm\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait TransactionAwareTrait
{
    protected ?Transaction $transaction = null;

    public function setTransaction(Transaction $transaction): void
    {
        $this->transaction = $transaction;
    }
}
