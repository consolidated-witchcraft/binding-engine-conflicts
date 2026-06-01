<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictRuleConfigurationException;

interface ConflictRuleDefinitionInterface
{
    /**
     * Returns the configuration type handled by this definition.
     *
     * Example:
     *
     * relationship_cardinality
     */
    public function getType(): string;

    /**
     * Creates a configured conflict rule from configuration data.
     *
     * @param array<string, mixed> $configuration
     *
     * @throws InvalidConflictRuleConfigurationException
     */
    public function create(
        array $configuration,
        string $path,
    ): ConflictRuleInterface;
}
