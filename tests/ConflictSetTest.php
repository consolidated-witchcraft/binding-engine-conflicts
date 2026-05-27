<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Conflict;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictCandidate;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictSet;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionInterface;

function makeConflictSetCandidate(string $value): ConflictCandidate
{
    return new ConflictCandidate(
        value: $value,
        projection: Mockery::mock(ProjectionInterface::class),
    );
}

function makeConflictSetConflict(
    string $subjectKey = 'event:battle-of-thornbridge',
    string $field = 'occurred-at',
): Conflict {
    return new Conflict(
        conflictType: ConflictTypeEnum::FieldValueConflict,
        severity: ConflictSeverityEnum::ManualResolutionRequired,
        subjectKey: $subjectKey,
        field: $field,
        candidates: [
            makeConflictSetCandidate('1844'),
            makeConflictSetCandidate('1845'),
        ],
    );
}

it('constructs correctly with no conflicts', function () {
    $conflictSet = new ConflictSet([]);

    expect($conflictSet)->toBeInstanceOf(ConflictSet::class)
        ->and($conflictSet)->toBeInstanceOf(Countable::class)
        ->and($conflictSet->getConflicts())->toBe([])
        ->and($conflictSet->isEmpty())->toBeTrue()
        ->and($conflictSet->count())->toBe(0)
        ->and(count($conflictSet))->toBe(0)
        ->and($conflictSet->first())->toBeNull();
});

it('constructs correctly with conflicts', function () {
    $firstConflict = makeConflictSetConflict();
    $secondConflict = makeConflictSetConflict(
        subjectKey: 'event:other-battle',
        field: 'location',
    );

    $conflicts = [
        $firstConflict,
        $secondConflict,
    ];

    $conflictSet = new ConflictSet($conflicts);

    expect($conflictSet->getConflicts())->toBe($conflicts)
        ->and($conflictSet->isEmpty())->toBeFalse()
        ->and($conflictSet->count())->toBe(2)
        ->and(count($conflictSet))->toBe(2)
        ->and($conflictSet->first())->toBe($firstConflict);
});
