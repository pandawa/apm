<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

use Carbon\Carbon;
use Pandawa\Apm\Contract\SpanInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RequestSpan implements SpanInterface
{
    use TransactionAwareTrait, SpanEmptyFieldsTrait;

    public function __construct(
        private string $type,
        protected Carbon $now,
        protected float $processingTime,
        protected int $statusCode,
        protected string $requestPath,
        protected string $userAgent,
    ) {
    }

    public function getName(): string
    {
        return $this->transaction->getName();
    }

    public function getNow(): Carbon
    {
        return $this->now;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getRequestPath(): string
    {
        return $this->requestPath;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function getProcessingTime(): float
    {
        return $this->processingTime;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return 'processed';
    }
}
