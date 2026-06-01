<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\RuleLoading;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictRuleConfigurationException;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleDefinitionInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleLoaderInterface;

final readonly class JsonConflictRuleLoader implements ConflictRuleLoaderInterface
{
    /**
     * @param list<ConflictRuleDefinitionInterface> $ruleDefinitions
     */
    public function __construct(
        private array $ruleDefinitions,
    ) {
    }

    /**
     * @return list<ConflictRuleInterface>
     *
     * @throws InvalidConflictRuleConfigurationException
     */
    public function load(string $input): array
    {
        try {
            $decoded = json_decode(
                json: $input,
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw new InvalidConflictRuleConfigurationException(
                sprintf('Invalid conflict rule JSON: %s', $exception->getMessage()),
                previous: $exception,
            );
        }

        if (!is_array($decoded)) {
            throw new InvalidConflictRuleConfigurationException(
                'Expected object-like array at root.',
            );
        }

        if (!array_key_exists('rules', $decoded)) {
            throw new InvalidConflictRuleConfigurationException(
                'Missing required key "rules" at root.',
            );
        }

        if (!is_array($decoded['rules'])) {
            throw new InvalidConflictRuleConfigurationException(
                'Expected array at "rules".',
            );
        }

        $rules = [];

        foreach ($decoded['rules'] as $index => $ruleConfiguration) {
            $path = sprintf('rules[%d]', $index);

            if (!is_array($ruleConfiguration)) {
                throw new InvalidConflictRuleConfigurationException(
                    sprintf('Expected object-like array at "%s".', $path),
                );
            }

            /** @var array<string, mixed> $ruleConfiguration */
            $type = $this->requireString(
                data: $ruleConfiguration,
                key: 'type',
                path: $path,
            );

            $definition = $this->findDefinition(
                type: $type,
                path: sprintf('%s.type', $path),
            );

            $rules[] = $definition->create(
                configuration: $ruleConfiguration,
                path: $path,
            );
        }

        return $rules;
    }

    /**
     * @throws InvalidConflictRuleConfigurationException
     */
    private function findDefinition(
        string $type,
        string $path,
    ): ConflictRuleDefinitionInterface {
        foreach ($this->ruleDefinitions as $ruleDefinition) {
            if ($ruleDefinition->getType() === $type) {
                return $ruleDefinition;
            }
        }

        throw new InvalidConflictRuleConfigurationException(
            sprintf(
                'Unknown conflict rule type "%s" at "%s".',
                $type,
                $path,
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
}
