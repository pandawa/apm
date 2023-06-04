<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Elastic;

use Elastic\Apm\ElasticApm;
use Elastic\Apm\TransactionInterface;
use Pandawa\Apm\Apm\Elastic\Transformer\ElasticChainSpanTransformer;
use Pandawa\Apm\Contract\ApmInterface;
use Pandawa\Apm\Contract\SpanInterface;
use Pandawa\Apm\Contract\TransformerInterface;
use Pandawa\Apm\Transaction;
use RuntimeException;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class ElasticApmAdapter implements ApmInterface
{
    private ?TransformerInterface $transformer;

    public function __construct()
    {
        $this->transformer = new ElasticChainSpanTransformer();
    }

    public function capture(Transaction $transaction): void
    {
        $this->assertHasElasticExtension();

        $apmTransaction = $this->getCurrentTransaction();

        $apmTransaction->setName($transaction->getName());
        $apmTransaction->setType($transaction->getType());

        foreach ($transaction->getSpans() as $span) {
            $this->includeSpan($apmTransaction, $span);
        }
    }

    public function name(): string
    {
        return 'elastic';
    }

    private function getCurrentTransaction(): TransactionInterface
    {
        return ElasticApm::getCurrentTransaction();
    }

    private function includeSpan(TransactionInterface $transaction, SpanInterface $span): void
    {
        $childSpan = $transaction->beginChildSpan($span->getName(), $span->getType(), $span->getSubType());

        foreach ($span->getLabels() as $key => $value) {
            $childSpan->context()->setLabel($key, $value);
        }

        $childSpan->setAction(json_encode($this->transformer->transform($span)));
        $childSpan->end();
    }

    private function assertHasElasticExtension(): void
    {
        if (!class_exists('Elastic\Apm\ElasticApm')) {
            throw new RuntimeException('Please install "elastic_apm" php extension.', 500);
        }
    }
}
