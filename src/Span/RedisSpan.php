<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

use Pandawa\Apm\Contract\SpanInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class RedisSpan implements SpanInterface
{
    use TransactionAwareTrait, SpanEmptyFieldsTrait;

    public function __construct(
        private string $type,
        private string $command,
        private ?string $connection,
        private ?float $executionTime,
    ) {
    }

    public function getName(): string
    {
        return $this->command;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->connection ?? 'NULL';
    }

    public function getLabels(): array
    {
        return [
            'execution_time' => $this->executionTime,
        ];
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getConnection(): ?string
    {
        return $this->connection;
    }

    public function getExecutionTime(): ?float
    {
        return $this->executionTime;
    }
}
