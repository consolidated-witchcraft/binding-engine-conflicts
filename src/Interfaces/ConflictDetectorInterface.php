<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionSetInterface;

interface ConflictDetectorInterface
{
    public function detect(
        ProjectionSetInterface $projectionSet,
    ): ConflictSetInterface;
}
