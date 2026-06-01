<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictDetectorInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleInterface;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionSetInterface;

final readonly class ConflictDetector implements ConflictDetectorInterface
{
    /**
     * @param list<ConflictRuleInterface> $rules
     */
    public function __construct(
        private array $rules,
    ) {
    }

    public function detect(
        ProjectionSetInterface $projectionSet,
    ): ConflictSet {
        $conflicts = [];

        foreach ($this->rules as $rule) {
            foreach ($rule->detect($projectionSet)->getConflicts() as $conflict) {
                $conflicts[] = $conflict;
            }
        }

        return new ConflictSet($conflicts);
    }
}
