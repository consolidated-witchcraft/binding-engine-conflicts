<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Conflict;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictCandidate;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictException;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionInterface;

function makeConflictCandidate(string $value): ConflictCandidate
{
    return new ConflictCandidate(
        value: $value,
        projection: Mockery::mock(ProjectionInterface::class),
    );
}

it('constructs correctly', function () {
    $firstCandidate = makeConflictCandidate('1844');
    $secondCandidate = makeConflictCandidate('1845');

    $candidates = [
        $firstCandidate,
        $secondCandidate,
    ];

    $conflict = new Conflict(
        conflictType: ConflictTypeEnum::FieldValueConflict,
        severity: ConflictSeverityEnum::ManualResolutionRequired,
        subjectKey: 'event:battle-of-thornbridge',
        field: 'occurred-at',
        candidates: $candidates,
    );

    expect($conflict)->toBeInstanceOf(Conflict::class)
        ->and($conflict->getConflictType())->toBe(ConflictTypeEnum::FieldValueConflict)
        ->and($conflict->getSeverity())->toBe(ConflictSeverityEnum::ManualResolutionRequired)
        ->and($conflict->getSubjectKey())->toBe('event:battle-of-thornbridge')
        ->and($conflict->getField())->toBe('occurred-at')
        ->and($conflict->getCandidates())->toBe($candidates)
        ->and($conflict->countCandidates())->toBe(2);
});

it('rejects empty subject keys', function (string $subjectKey) {
    expect(
        fn () => new Conflict(
            conflictType: ConflictTypeEnum::FieldValueConflict,
            severity: ConflictSeverityEnum::ManualResolutionRequired,
            subjectKey: $subjectKey,
            field: 'occurred-at',
            candidates: [
                makeConflictCandidate('1844'),
                makeConflictCandidate('1845'),
            ],
        )
    )->toThrow(
        InvalidConflictException::class,
        'Conflict subject key must not be empty.',
    );
})->with(function (): iterable {
    yield 'empty string' => '';
    yield 'whitespace' => '   ';
});

it('rejects empty fields', function (string $field) {
    expect(
        fn () => new Conflict(
            conflictType: ConflictTypeEnum::FieldValueConflict,
            severity: ConflictSeverityEnum::ManualResolutionRequired,
            subjectKey: 'event:battle-of-thornbridge',
            field: $field,
            candidates: [
                makeConflictCandidate('1844'),
                makeConflictCandidate('1845'),
            ],
        )
    )->toThrow(
        InvalidConflictException::class,
        'Conflict field must not be empty.',
    );
})->with(function (): iterable {
    yield 'empty string' => '';
    yield 'whitespace' => '   ';
});

it('rejects conflicts with fewer than two candidates', function (array $candidates) {
    expect(
        fn () => new Conflict(
            conflictType: ConflictTypeEnum::FieldValueConflict,
            severity: ConflictSeverityEnum::ManualResolutionRequired,
            subjectKey: 'event:battle-of-thornbridge',
            field: 'occurred-at',
            candidates: $candidates,
        )
    )->toThrow(
        InvalidConflictException::class,
        'Conflict must contain at least two candidates.',
    );
})->with(function (): iterable {
    yield 'no candidates' => [[]];
    yield 'one candidate' => [[makeConflictCandidate('1844')]];
});
