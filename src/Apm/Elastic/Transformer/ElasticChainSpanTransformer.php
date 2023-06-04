<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic\Transformer;

use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class ElasticChainSpanTransformer implements TransformerInterface
{
    /**
     * @var TransformerInterface[]
     */
    private array $transformers = [];

    public function __construct()
    {
        $this->transformers = [
            new ElasticRequestSpanTransformer(),
            new ElasticQuerySpanTransformer(),
            new ElasticRedisSpanTransformer(),
            new ElasticJobSpanTransformer(),
            new ElasticMessageSpanTransformer(),
        ];
    }

    public function transform(SpanInterface $span): array
    {
        if ($this->supports($span)) {
            foreach ($this->transformers as $transformer) {
                if ($transformer->supports($span)) {
                    return $transformer->transform($span);
                }
            }
        }

        throw new RuntimeException(
            sprintf('"%s" transformer is not supported to transform "%s"', static::class, get_class($span)),
            500
        );
    }

    public function supports(SpanInterface $span): bool
    {
        foreach ($this->transformers as $transformer) {
            if ($transformer->supports($span)) {
                return true;
            }
        }

        return false;
    }
}
