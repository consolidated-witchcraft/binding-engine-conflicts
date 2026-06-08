<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Conflict;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictCandidate;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictResolution;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictResolutionDecisionEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictException;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionInterface;

function makeConflictResolutionCandidate(string $value): ConflictCandidate
{
    return new ConflictCandidate(
        value: $value,
        projection: Mockery::mock(ProjectionInterface::class),
    );
}

function makeConflictResolutionConflict(): Conflict
{
    return new Conflict(
        conflictType: ConflictTypeEnum::RelationshipConflict,
        severity: ConflictSeverityEnum::ManualResolutionRequired,
        subjectKey: 'relationship:born_in:subject:jane-austen',
        field: 'object',
        candidates: [
            makeConflictResolutionCandidate('steventon-hampshire'),
            makeConflictResolutionCandidate('bath'),
        ],
    );
}

it('constructs an accepted candidate resolution correctly', function () {
    $conflict = makeConflictResolutionConflict();

    $resolution = new ConflictResolution(
        conflict: $conflict,
        decision: ConflictResolutionDecisionEnum::AcceptedCandidate,
        selectedCandidateValue: 'steventon-hampshire',
        note: 'Canonical biography source wins.',
    );

    expect($resolution)->toBeInstanceOf(ConflictResolution::class)
        ->and($resolution->getConflict())->toBe($conflict)
        ->and($resolution->getDecision())->toBe(ConflictResolutionDecisionEnum::AcceptedCandidate)
        ->and($resolution->getSelectedCandidateValue())->toBe('steventon-hampshire')
        ->and($resolution->getNote())->toBe('Canonical biography source wins.');
});

it('constructs a deferred resolution correctly', function () {
    $conflict = makeConflictResolutionConflict();

    $resolution = new ConflictResolution(
        conflict: $conflict,
        decision: ConflictResolutionDecisionEnum::Deferred,
    );

    expect($resolution->getConflict())->toBe($conflict)
        ->and($resolution->getDecision())->toBe(ConflictResolutionDecisionEnum::Deferred)
        ->and($resolution->getSelectedCandidateValue())->toBeNull()
        ->and($resolution->getNote())->toBeNull();
});

it('constructs a manual review required resolution correctly', function () {
    $conflict = makeConflictResolutionConflict();

    $resolution = new ConflictResolution(
        conflict: $conflict,
        decision: ConflictResolutionDecisionEnum::ManualReviewRequired,
        note: 'Needs editor review.',
    );

    expect($resolution->getConflict())->toBe($conflict)
        ->and($resolution->getDecision())->toBe(ConflictResolutionDecisionEnum::ManualReviewRequired)
        ->and($resolution->getSelectedCandidateValue())->toBeNull()
        ->and($resolution->getNote())->toBe('Needs editor review.');
});

it('rejects accepted candidate resolutions without a selected candidate value', function () {
    expect(
        fn () => new ConflictResolution(
            conflict: makeConflictResolutionConflict(),
            decision: ConflictResolutionDecisionEnum::AcceptedCandidate,
        )
    )->toThrow(
        InvalidConflictException::class,
        'Accepted candidate resolutions must include a selected candidate value.',
    );
});

it('rejects selected candidate values for non-accepted decisions', function (
    ConflictResolutionDecisionEnum $decision,
) {
    expect(
        fn () => new ConflictResolution(
            conflict: makeConflictResolutionConflict(),
            decision: $decision,
            selectedCandidateValue: 'steventon-hampshire',
        )
    )->toThrow(
        InvalidConflictException::class,
        'Only accepted candidate resolutions may include a selected candidate value.',
    );
})->with(function (): iterable {
    yield 'deferred' => [ConflictResolutionDecisionEnum::Deferred];
    yield 'manual review required' => [ConflictResolutionDecisionEnum::ManualReviewRequired];
});

it('rejects empty notes when provided', function (string $note) {
    expect(
        fn () => new ConflictResolution(
            conflict: makeConflictResolutionConflict(),
            decision: ConflictResolutionDecisionEnum::Deferred,
            note: $note,
        )
    )->toThrow(
        InvalidConflictException::class,
        'Conflict resolution note must not be empty when provided.',
    );
})->with(function (): iterable {
    yield 'empty string' => [''];
    yield 'whitespace' => ['   '];
});
