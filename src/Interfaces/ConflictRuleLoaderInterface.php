<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

interface ConflictRuleLoaderInterface
{
    /**
     * @return list<ConflictRuleInterface>
     */
    public function load(string $input): array;
}
