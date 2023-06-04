<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic\Transformer;

use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use Pandawa\Apm\Span\MessageSpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ElasticMessageSpanTransformer implements TransformerInterface
{
    /**
     * @param  MessageSpan  $span
     *
     * @return array
     */
    public function transform(SpanInterface $span): array
    {
        return [
            'message' => $span->getMessage(),
            'type'    => $span->getSubType(),
            'time'    => $span->getExecutionTime(),
        ];
    }

    public function supports(SpanInterface $span): bool
    {
        return $span instanceof MessageSpan;
    }
}
