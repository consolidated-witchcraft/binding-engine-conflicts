<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionInterface;

interface ConflictCandidateInterface
{
    public function getValue(): string;

    public function getProjection(): ProjectionInterface;
}
