<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic\Transformer;

use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use Pandawa\Apm\Span\JobSpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ElasticJobSpanTransformer implements TransformerInterface
{
    /**
     * @param  JobSpan  $span
     *
     * @return array
     */
    public function transform(SpanInterface $span): array
    {
        return [
            'time'  => $span->getTime()->toDateTimeString(),
            'job'   => $span->getJob(),
            'state' => $span->getState(),
        ];
    }

    public function supports(SpanInterface $span): bool
    {
        return $span instanceof JobSpan;
    }
}
