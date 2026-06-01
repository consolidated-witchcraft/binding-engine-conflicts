<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Assertions\Assertion;
use ConsolidatedWitchcraft\BindingEngine\Assertions\SourceContext;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\RelationshipCardinalityDirectionEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules\RelationshipCardinalityConflictRule;
use ConsolidatedWitchcraft\BindingEngine\Parser\Ast\SourceSpan;
use ConsolidatedWitchcraft\BindingEngine\Projection\ProjectionSet;
use ConsolidatedWitchcraft\BindingEngine\Projection\RelationshipProjection;
use ConsolidatedWitchcraft\BindingEngine\Vocabulary\Enums\BindingPayloadShapeEnum;

function makeRelationshipCardinalityAssertion(
    string $relationshipType,
    string $subject,
    string $object,
): Assertion {
    return new Assertion(
        bindingType: 'relationship',
        payloadShape: BindingPayloadShapeEnum::AttributeList,
        shorthandValue: null,
        attributes: [
            'type' => [$relationshipType],
            'subject' => [$subject],
            'object' => [$object],
        ],
        label: sprintf('%s %s %s', $subject, $relationshipType, $object),
        raw: sprintf(
            '@relationship[type:%s, subject:%s, object:%s]',
            $relationshipType,
            $subject,
            $object,
        ),
        sourceSpan: new SourceSpan(0, 1),
        sourceContext: new SourceContext(
            sourceId: 'worldbook',
            documentId: 'document.md',
            revisionId: 'revision-1',
            vocabularyIdentifier: 'test-vocabulary',
            vocabularyVersion: '0.2.0',
        ),
    );
}

function makeRelationshipCardinalityProjection(
    string $relationshipType,
    string $subject,
    string $object,
): RelationshipProjection {
    return new RelationshipProjection(
        relationshipType: $relationshipType,
        subject: $subject,
        object: $object,
        label: sprintf('%s %s %s', $subject, $relationshipType, $object),
        originatingAssertion: makeRelationshipCardinalityAssertion(
            relationshipType: $relationshipType,
            subject: $subject,
            object: $object,
        ),
    );
}

it('exposes a deterministic identifier', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'born_in',
        direction: RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject,
    );

    expect($rule->getIdentifier())
        ->toBe('relationship_cardinality.born_in.unique_object_per_subject');
});

it('detects unique-object-per-subject conflicts for relationship projections', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'born_in',
        direction: RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject,
    );

    $steventonProjection = makeRelationshipCardinalityProjection(
        relationshipType: 'born_in',
        subject: 'jane-austen',
        object: 'steventon-hampshire',
    );

    $bathProjection = makeRelationshipCardinalityProjection(
        relationshipType: 'born_in',
        subject: 'jane-austen',
        object: 'bath',
    );

    $projectionSet = new ProjectionSet([
        $steventonProjection,
        $bathProjection,
    ]);

    $conflictSet = $rule->detect($projectionSet);

    expect($conflictSet->count())->toBe(1);

    $conflict = $conflictSet->first();

    expect($conflict)->not->toBeNull()
        ->and($conflict->getConflictType())->toBe(ConflictTypeEnum::RelationshipConflict)
        ->and($conflict->getSeverity())->toBe(ConflictSeverityEnum::ManualResolutionRequired)
        ->and($conflict->getSubjectKey())->toBe('relationship:born_in:subject:jane-austen')
        ->and($conflict->getField())->toBe('object')
        ->and($conflict->countCandidates())->toBe(2)
        ->and($conflict->getCandidates()[0]->getValue())->toBe('steventon-hampshire')
        ->and($conflict->getCandidates()[0]->getProjection())->toBe($steventonProjection)
        ->and($conflict->getCandidates()[1]->getValue())->toBe('bath')
        ->and($conflict->getCandidates()[1]->getProjection())->toBe($bathProjection);
});

it('does not detect unique-object-per-subject conflicts when subjects differ', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'born_in',
        direction: RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject,
    );

    $projectionSet = new ProjectionSet([
        makeRelationshipCardinalityProjection(
            relationshipType: 'born_in',
            subject: 'jane-austen',
            object: 'steventon-hampshire',
        ),
        makeRelationshipCardinalityProjection(
            relationshipType: 'born_in',
            subject: 'george-austen',
            object: 'steventon-hampshire',
        ),
    ]);

    $conflictSet = $rule->detect($projectionSet);

    expect($conflictSet->isEmpty())->toBeTrue()
        ->and($conflictSet->count())->toBe(0);
});

it('does not detect unique-object-per-subject conflicts when the object is the same', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'born_in',
        direction: RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject,
    );

    $projectionSet = new ProjectionSet([
        makeRelationshipCardinalityProjection(
            relationshipType: 'born_in',
            subject: 'jane-austen',
            object: 'steventon-hampshire',
        ),
        makeRelationshipCardinalityProjection(
            relationshipType: 'born_in',
            subject: 'jane-austen',
            object: 'steventon-hampshire',
        ),
    ]);

    $conflictSet = $rule->detect($projectionSet);

    expect($conflictSet->isEmpty())->toBeTrue()
        ->and($conflictSet->count())->toBe(0);
});

it('ignores relationship projections with other relationship types', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'born_in',
        direction: RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject,
    );

    $projectionSet = new ProjectionSet([
        makeRelationshipCardinalityProjection(
            relationshipType: 'died_in',
            subject: 'jane-austen',
            object: 'winchester',
        ),
        makeRelationshipCardinalityProjection(
            relationshipType: 'died_in',
            subject: 'jane-austen',
            object: 'bath',
        ),
    ]);

    $conflictSet = $rule->detect($projectionSet);

    expect($conflictSet->isEmpty())->toBeTrue()
        ->and($conflictSet->count())->toBe(0);
});

it('detects unique-subject-per-object conflicts for relationship projections', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'biological_father_of',
        direction: RelationshipCardinalityDirectionEnum::UniqueSubjectPerObject,
    );

    $georgeProjection = makeRelationshipCardinalityProjection(
        relationshipType: 'biological_father_of',
        subject: 'george-austen',
        object: 'jane-austen',
    );

    $someoneElseProjection = makeRelationshipCardinalityProjection(
        relationshipType: 'biological_father_of',
        subject: 'someone-else',
        object: 'jane-austen',
    );

    $projectionSet = new ProjectionSet([
        $georgeProjection,
        $someoneElseProjection,
    ]);

    $conflictSet = $rule->detect($projectionSet);

    expect($conflictSet->count())->toBe(1);

    $conflict = $conflictSet->first();

    expect($conflict)->not->toBeNull()
        ->and($conflict->getConflictType())->toBe(ConflictTypeEnum::RelationshipConflict)
        ->and($conflict->getSeverity())->toBe(ConflictSeverityEnum::ManualResolutionRequired)
        ->and($conflict->getSubjectKey())->toBe('relationship:biological_father_of:object:jane-austen')
        ->and($conflict->getField())->toBe('subject')
        ->and($conflict->countCandidates())->toBe(2)
        ->and($conflict->getCandidates()[0]->getValue())->toBe('george-austen')
        ->and($conflict->getCandidates()[0]->getProjection())->toBe($georgeProjection)
        ->and($conflict->getCandidates()[1]->getValue())->toBe('someone-else')
        ->and($conflict->getCandidates()[1]->getProjection())->toBe($someoneElseProjection);
});

it('uses a custom severity when provided', function () {
    $rule = new RelationshipCardinalityConflictRule(
        relationshipType: 'born_in',
        direction: RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject,
        severity: ConflictSeverityEnum::Warning,
    );

    $projectionSet = new ProjectionSet([
        makeRelationshipCardinalityProjection(
            relationshipType: 'born_in',
            subject: 'jane-austen',
            object: 'steventon-hampshire',
        ),
        makeRelationshipCardinalityProjection(
            relationshipType: 'born_in',
            subject: 'jane-austen',
            object: 'bath',
        ),
    ]);

    $conflict = $rule->detect($projectionSet)->first();

    expect($conflict)->not->toBeNull()
        ->and($conflict->getSeverity())->toBe(ConflictSeverityEnum::Warning);
});
