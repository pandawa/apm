<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic\Transformer;

use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use Pandawa\Apm\Span\RedisSpan;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ElasticRedisSpanTransformer implements TransformerInterface
{
    /**
     * @param  RedisSpan  $span
     *
     * @return array
     */
    public function transform(SpanInterface $span): array
    {
        return [
            'command'    => $span->getCommand(),
            'connection' => $span->getConnection(),
            'time'       => $span->getExecutionTime(),
        ];
    }

    public function supports(SpanInterface $span): bool
    {
        return $span instanceof RedisSpan;
    }
}
