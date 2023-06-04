<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

use Pandawa\Apm\Contract\SpanInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MessageSpan implements SpanInterface
{
    use TransactionAwareTrait, SpanEmptyFieldsTrait;

    public function __construct(
        private string $type,
        private string $subType,
        private string $message,
        private float $executionTime,
    ) {
    }

    public function getName(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->subType;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }
}
