<?php

declare(strict_types=1);

namespace Pandawa\Apm;

use Pandawa\Apm\Contract\SpanInterface;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class Transaction
{
    /**
     * @var SpanInterface[]
     */
    private array $spans = [];

    public function __construct(private ?string $name, private ?string $type)
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function addSpan(SpanInterface $span): void
    {
        $this->spans[] = $span;
    }

    public function getSpans(): array
    {
        return $this->spans;
    }
}
