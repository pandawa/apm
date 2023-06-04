<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait SpanEmptyFieldsTrait
{
    public function getAction(): string
    {
        return '';
    }

    public function getLabels(): array
    {
        return [];
    }
}
