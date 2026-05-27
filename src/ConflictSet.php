<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

final readonly class ConflictSet implements \Countable
{
    /**
     * @param list<Conflict> $conflicts
     */
    public function __construct(
        private array $conflicts,
    ) {
    }

    /**
     * @return list<Conflict>
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

    public function first(): ?Conflict
    {
        return $this->conflicts[0] ?? null;
    }
}
