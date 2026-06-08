<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\RelationshipCardinalityDirectionEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictRuleConfigurationException;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleDefinitionInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleInterface;
use ValueError;

final readonly class RelationshipCardinalityConflictRuleDefinition implements ConflictRuleDefinitionInterface
{
    public function getType(): string
    {
        return 'relationship_cardinality';
    }

    /**
     * @param array<string, mixed> $configuration
     *
     * @throws InvalidConflictRuleConfigurationException
     */
    public function create(
        array $configuration,
        string $path,
    ): ConflictRuleInterface {
        return new RelationshipCardinalityConflictRule(
            relationshipType: $this->requireString(
                data: $configuration,
                key: 'relationshipType',
                path: $path,
            ),
            direction: $this->parseDirection(
                value: $this->requireString(
                    data: $configuration,
                    key: 'direction',
                    path: $path,
                ),
                path: sprintf('%s.direction', $path),
            ),
            severity: $this->parseSeverity(
                value: $this->optionalString(
                    data: $configuration,
                    key: 'severity',
                    path: $path,
                ),
                path: sprintf('%s.severity', $path),
            ),
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidConflictRuleConfigurationException
     */
    private function requireString(
        array $data,
        string $key,
        string $path,
    ): string {
        if (!array_key_exists($key, $data)) {
            throw new InvalidConflictRuleConfigurationException(
                sprintf(
                    'Missing required key "%s" at "%s".',
                    $key,
                    $path,
                ),
            );
        }

        if (!is_string($data[$key])) {
            throw new InvalidConflictRuleConfigurationException(
                sprintf(
                    'Expected string at "%s.%s".',
                    $path,
                    $key,
                ),
            );
        }

        return $data[$key];
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws InvalidConflictRuleConfigurationException
     */
    private function optionalString(
        array $data,
        string $key,
        string $path,
    ): ?string {
        if (!array_key_exists($key, $data)) {
            return null;
        }

        if (!is_string($data[$key])) {
            throw new InvalidConflictRuleConfigurationException(
                sprintf(
                    'Expected string at "%s.%s".',
                    $path,
                    $key,
                ),
            );
        }

        return $data[$key];
    }

    /**
     * @throws InvalidConflictRuleConfigurationException
     */
    private function parseDirection(
        string $value,
        string $path,
    ): RelationshipCardinalityDirectionEnum {
        try {
            return RelationshipCardinalityDirectionEnum::from($value);
        } catch (ValueError $exception) {
            throw new InvalidConflictRuleConfigurationException(
                sprintf(
                    'Invalid relationship cardinality direction "%s" at "%s".',
                    $value,
                    $path,
                ),
                previous: $exception,
            );
        }
    }

    /**
     * @throws InvalidConflictRuleConfigurationException
     */
    private function parseSeverity(
        ?string $value,
        string $path,
    ): ConflictSeverityEnum {
        if ($value === null) {
            return ConflictSeverityEnum::ManualResolutionRequired;
        }

        try {
            return ConflictSeverityEnum::from($value);
        } catch (ValueError $exception) {
            throw new InvalidConflictRuleConfigurationException(
                sprintf(
                    'Invalid conflict severity "%s" at "%s".',
                    $value,
                    $path,
                ),
                previous: $exception,
            );
        }
    }
}
