<?php

declare(strict_types=1);

namespace Pandawa\Apm\Exception;

use RuntimeException;
use Throwable;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MissingTransactionException extends RuntimeException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('Transaction must be set.', 500, $previous);
    }
}
