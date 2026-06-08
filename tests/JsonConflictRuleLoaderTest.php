<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictSet;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictRuleConfigurationException;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleDefinitionInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\RuleLoading\JsonConflictRuleLoader;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionSetInterface;

function makeJsonConflictRuleLoaderRule(string $identifier = 'test.rule'): ConflictRuleInterface
{
    return new class ($identifier) implements ConflictRuleInterface {
        public function __construct(
            private readonly string $identifier,
        ) {
        }

        public function getIdentifier(): string
        {
            return $this->identifier;
        }

        public function detect(ProjectionSetInterface $projectionSet): ConflictSet
        {
            return new ConflictSet([]);
        }
    };
}

function makeJsonConflictRuleLoaderDefinition(
    string $type,
    ConflictRuleInterface $rule,
): ConflictRuleDefinitionInterface {
    return new class ($type, $rule) implements ConflictRuleDefinitionInterface {
        public function __construct(
            private readonly string $type,
            private readonly ConflictRuleInterface $rule,
        ) {
        }

        public function getType(): string
        {
            return $this->type;
        }

        public function create(
            array $configuration,
            string $path,
        ): ConflictRuleInterface {
            expect($configuration['type'])->toBe($this->type);
            expect($path)->toBe('rules[0]');

            return $this->rule;
        }
    };
}

it('loads configured rules from json', function () {
    $rule = makeJsonConflictRuleLoaderRule();

    $loader = new JsonConflictRuleLoader([
        makeJsonConflictRuleLoaderDefinition(
            type: 'test_rule',
            rule: $rule,
        ),
    ]);

    $rules = $loader->load(json_encode([
        'rules' => [
            [
                'type' => 'test_rule',
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    expect($rules)->toBe([$rule]);
});

it('loads an empty rule list', function () {
    $loader = new JsonConflictRuleLoader([]);

    $rules = $loader->load(json_encode([
        'rules' => [],
    ], JSON_THROW_ON_ERROR));

    expect($rules)->toBe([]);
});

it('rejects invalid json', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load('{"rules": [}')
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Invalid conflict rule JSON:',
    );
});

it('rejects non-object root values', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode(['not', 'an', 'object'], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Missing required key "rules" at root.',
    );
});

it('rejects missing rules key', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode([
            'foo' => 'bar',
        ], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Missing required key "rules" at root.',
    );
});

it('rejects non-array rules values', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode([
            'rules' => 'not-an-array',
        ], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Expected array at "rules".',
    );
});

it('rejects non-object rule entries', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode([
            'rules' => [
                'not-an-object',
            ],
        ], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Expected object-like array at "rules[0]".',
    );
});

it('rejects rules without a type', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode([
            'rules' => [
                [],
            ],
        ], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Missing required key "type" at "rules[0]".',
    );
});

it('rejects rules with a non-string type', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode([
            'rules' => [
                [
                    'type' => true,
                ],
            ],
        ], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Expected string at "rules[0].type".',
    );
});

it('rejects unknown rule types', function () {
    $loader = new JsonConflictRuleLoader([]);

    expect(
        fn () => $loader->load(json_encode([
            'rules' => [
                [
                    'type' => 'unknown_rule',
                ],
            ],
        ], JSON_THROW_ON_ERROR))
    )->toThrow(
        InvalidConflictRuleConfigurationException::class,
        'Unknown conflict rule type "unknown_rule" at "rules[0].type".',
    );
});

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\RelationshipCardinalityConflictRule;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\RelationshipCardinalityConflictRuleDefinition;

it('loads a relationship cardinality rule from json', function () {
    $loader = new JsonConflictRuleLoader([
        new RelationshipCardinalityConflictRuleDefinition(),
    ]);

    $rules = $loader->load(json_encode([
        'rules' => [
            [
                'type' => 'relationship_cardinality',
                'relationshipType' => 'born_in',
                'direction' => 'unique_object_per_subject',
                'severity' => 'manual_resolution_required',
            ],
        ],
    ], JSON_THROW_ON_ERROR));

    expect($rules)->toHaveCount(1)
        ->and($rules[0])->toBeInstanceOf(RelationshipCardinalityConflictRule::class)
        ->and($rules[0]->getIdentifier())->toBe(
            'relationship_cardinality.born_in.unique_object_per_subject',
        );
});
