<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

use Pandawa\Apm\Contract\SpanInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class QuerySpan implements SpanInterface
{
    use TransactionAwareTrait, SpanEmptyFieldsTrait;

    public function __construct(
        private string $type,
        private string $connection,
        private string $query,
        private float $executionTime,
    ) {
    }

    public function getName(): string
    {
        return $this->query;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->connection;
    }

    public function getLabels(): array
    {
        return [
            'execution_time' => $this->executionTime,
        ];
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }
}
