<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionInterface;

final readonly class ConflictCandidate
{
    public function __construct(
        private string $value,
        private ProjectionInterface $projection,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getProjection(): ProjectionInterface
    {
        return $this->projection;
    }
}
