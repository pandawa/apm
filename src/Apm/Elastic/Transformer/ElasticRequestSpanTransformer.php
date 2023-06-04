<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic\Transformer;

use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use Pandawa\Apm\Span\RequestSpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ElasticRequestSpanTransformer implements TransformerInterface
{
    /**
     * @param  RequestSpan  $span
     *
     * @return array
     */
    public function transform(SpanInterface $span): array
    {
        return [
            'now'             => $span->getNow()->toDateTimeString(),
            'status_code'     => $span->getStatusCode(),
            'path'            => $span->getRequestPath(),
            'processing_time' => $span->getProcessingTime(),
            'user_agent'      => $span->getUserAgent(),
        ];
    }

    public function supports(SpanInterface $span): bool
    {
        return $span instanceof RequestSpan;
    }
}
