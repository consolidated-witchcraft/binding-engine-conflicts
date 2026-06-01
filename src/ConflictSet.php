<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictSetInterface;

final readonly class ConflictSet implements ConflictSetInterface
{
    /**
     * @param list<ConflictInterface> $conflicts
     */
    public function __construct(
        private array $conflicts,
    ) {
    }

    /**
     * @return list<ConflictInterface>
     */
    public function getConflicts(): array
    {
        return $this->conflicts;
    }

    public function isEmpty(): bool
    {
        return $this->conflicts === [];
    }

    public function count(): int
    {
        return count($this->conflicts);
    }

    public function first(): ?ConflictInterface
    {
        return $this->conflicts[0] ?? null;
    }
}
