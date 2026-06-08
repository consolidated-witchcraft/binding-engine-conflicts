<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

final readonly class ConflictResolutionSet implements \Countable
{
    /**
     * @param list<ConflictResolution> $resolutions
     */
    public function __construct(
        private array $resolutions,
    ) {
    }

    /**
     * @return list<ConflictResolution>
     */
    public function getResolutions(): array
    {
        return $this->resolutions;
    }

    public function isEmpty(): bool
    {
        return $this->resolutions === [];
    }

    public function count(): int
    {
        return count($this->resolutions);
    }

    public function first(): ?ConflictResolution
    {
        return $this->resolutions[0] ?? null;
    }
}
