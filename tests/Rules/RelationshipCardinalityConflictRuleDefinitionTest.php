<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\RelationshipCardinalityDirectionEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictRuleConfigurationException;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\RelationshipCardinalityConflictRule;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\RelationshipCardinalityConflictRuleDefinition;

it('creates a relationship cardinality conflict rule from configuration', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $rule = $definition->create([
        'relationshipType' => 'parent_of',
        'direction' => RelationshipCardinalityDirectionEnum::cases()[0]->value,
        'severity' => ConflictSeverityEnum::ManualResolutionRequired->value,
    ], 'rules[0]');

    expect($rule)->toBeInstanceOf(RelationshipCardinalityConflictRule::class);
});

it('defaults severity when omitted', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $rule = $definition->create([
        'relationshipType' => 'parent_of',
        'direction' => RelationshipCardinalityDirectionEnum::cases()[0]->value,
    ], 'rules[0]');

    expect($rule)->toBeInstanceOf(RelationshipCardinalityConflictRule::class);
});

it('throws when relationship type is missing', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $definition->create([
        'direction' => RelationshipCardinalityDirectionEnum::cases()[0]->value,
    ], 'rules[0]');
})->throws(
    InvalidConflictRuleConfigurationException::class,
    'Missing required key "relationshipType" at "rules[0]".',
);

it('throws when direction is missing', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $definition->create([
        'relationshipType' => 'parent_of',
    ], 'rules[0]');
})->throws(
    InvalidConflictRuleConfigurationException::class,
    'Missing required key "direction" at "rules[0]".',
);

it('throws when direction is invalid', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $definition->create([
        'relationshipType' => 'parent_of',
        'direction' => 'sideways',
    ], 'rules[0]');
})->throws(
    InvalidConflictRuleConfigurationException::class,
    'Invalid relationship cardinality direction "sideways" at "rules[0].direction".',
);

it('throws when severity is invalid', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $definition->create([
        'relationshipType' => 'parent_of',
        'direction' => RelationshipCardinalityDirectionEnum::cases()[0]->value,
        'severity' => 'apocalyptic',
    ], 'rules[0]');
})->throws(
    InvalidConflictRuleConfigurationException::class,
    'Invalid conflict severity "apocalyptic" at "rules[0].severity".',
);

it('throws when relationship type is not a string', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $definition->create([
        'relationshipType' => 123,
        'direction' => RelationshipCardinalityDirectionEnum::cases()[0]->value,
    ], 'rules[0]');
})->throws(
    InvalidConflictRuleConfigurationException::class,
    'Expected string at "rules[0].relationshipType".',
);

it('throws when direction is not a string', function (): void {
    $definition = new RelationshipCardinalityConflictRuleDefinition();

    $definition->create([
        'relationshipType' => 'parent_of',
        'direction' => 123,
    ], 'rules[0]');
})->throws(
    InvalidConflictRuleConfigurationException::class,
    'Expected string at "rules[0].direction".',
);
