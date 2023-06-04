<?php

declare(strict_types=1);

namespace Pandawa\Apm\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface TransformerInterface
{
    public function transform(SpanInterface $span): array;

    public function supports(SpanInterface $span): bool;
}
