<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

interface ConflictSetInterface extends \Countable
{
    /**
     * @return list<ConflictInterface>
     */
    public function getConflicts(): array;

    public function isEmpty(): bool;

    public function first(): ?ConflictInterface;
}
