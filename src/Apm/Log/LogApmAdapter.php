<?php

declare(strict_types=1);

namespace Pandawa\Apm\Apm\Log;

use Pandawa\Apm\Contract\ApmInterface;
use Pandawa\Apm\Span\JobSpan;
use Pandawa\Apm\Span\MessageSpan;
use Pandawa\Apm\Span\QuerySpan;
use Pandawa\Apm\Span\RedisSpan;
use Pandawa\Apm\Span\RequestSpan;
use Pandawa\Apm\Transaction;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class LogApmAdapter implements ApmInterface
{
    public function capture(Transaction $transaction): void
    {
        logger("\n\n\n========== START ========== \n\n\n");

        logger(sprintf('===== TRANSACTION "%s - %s" STARTED =====', $transaction->getType(), $transaction->getName()));

        foreach ($transaction->getSpans() as $span) {
            $message = match (true) {
                $span instanceof RequestSpan => $this->formatRequestMessage($span),
                $span instanceof QuerySpan => $this->formatQueryMessage($span),
                $span instanceof RedisSpan => $this->formatRedisMessage($span),
                $span instanceof MessageSpan => $this->formatDispatchMessage($span),
                $span instanceof JobSpan => $this->formatJobMessage($span),
                default => null,
            };

            if (null !== $message) {
                logger($message);
            }
        }

        logger(
            sprintf('===== TRANSACTION "%s - %s" ENDED =====', $transaction->getType(), $transaction->getName())."\n\n"
        );


        logger("\n\n\n========== END ========== \n\n\n");
    }

    public function name(): string
    {
        return 'log';
    }

    private function formatRequestMessage(RequestSpan $span): string
    {
        return sprintf(
            'http request made to "%s %s" with data: %s',
            $span->getStatusCode(),
            $span->getName(),
            json_encode(
                [
                    'time'            => $span->getNow()->toDateTimeString(),
                    'user_agent'      => $span->getUserAgent(),
                    'processing_time' => $span->getProcessingTime(),
                ],
                JSON_PRETTY_PRINT
            )
        );
    }

    private function formatQueryMessage(QuerySpan $span): string
    {
        return sprintf(
            'query executed "%s %s" with data: %s',
            $span->getConnection(),
            $span->getQuery(),
            json_encode(['execution_time' => $span->getExecutionTime()])
        );
    }

    private function formatRedisMessage(RedisSpan $span): string
    {
        return sprintf(
            'redis command executed "%s - %s" with data: %s',
            $span->getConnection(),
            $span->getCommand(),
            json_encode(['execution_time' => $span->getExecutionTime()])
        );
    }

    private function formatDispatchMessage(MessageSpan $span): string
    {
        return sprintf(
            'message dispatched "%s - %s" with data: %s',
            $span->getSubType(),
            $span->getMessage(),
            json_encode(['execution_time' => $span->getExecutionTime()])
        );
    }

    private function formatJobMessage(JobSpan $span): string
    {
        return sprintf(
            'job dispatched "%s - %s" with data: %s',
            $span->getSubType(),
            $span->getJob(),
            json_encode(['time' => $span->getTime()->toDateTimeString()])
        );
    }
}
