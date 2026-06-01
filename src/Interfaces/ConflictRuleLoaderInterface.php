<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictRuleConfigurationException;

interface ConflictRuleLoaderInterface
{
    /**
     * @return list<ConflictRuleInterface>
     *
     * @throws InvalidConflictRuleConfigurationException
     */
    public function load(string $input): array;
}
