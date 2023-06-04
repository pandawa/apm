<?php

declare(strict_types=1);

namespace Pandawa\Apm\Span;

use Carbon\Carbon;
use Pandawa\Apm\Contract\SpanInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class JobSpan implements SpanInterface
{
    use TransactionAwareTrait, SpanEmptyFieldsTrait;

    public function __construct(
        private string $type,
        private string $state,
        private string $job,
        private Carbon $time,
    ) {
    }

    public function getName(): string
    {
        return $this->job;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubType(): string
    {
        return $this->state;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getJob(): string
    {
        return $this->job;
    }

    public function getTime(): Carbon
    {
        return $this->time;
    }

    public function getLabels(): array
    {
        return [
            'time'  => $this->time->toDateTimeString(),
            'job'   => $this->job,
            'state' => $this->state,
        ];
    }
}
