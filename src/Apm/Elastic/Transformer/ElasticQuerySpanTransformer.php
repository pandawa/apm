<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic\Transformer;

use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use Pandawa\Apm\Span\QuerySpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ElasticQuerySpanTransformer implements TransformerInterface
{
    /**
     * @param  QuerySpan  $span
     *
     * @return array
     */
    public function transform(SpanInterface $span): array
    {
        return [
            'connection' => $span->getConnection(),
            'query'      => $span->getQuery(),
            'time'       => $span->getExecutionTime(),
        ];
    }

    public function supports(SpanInterface $span): bool
    {
        return $span instanceof QuerySpan;
    }
}
