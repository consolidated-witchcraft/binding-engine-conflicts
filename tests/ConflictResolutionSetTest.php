<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictResolution;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictResolutionSet;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictResolutionDecisionEnum;

function makeConflictResolutionSetResolution(
    ConflictResolutionDecisionEnum $decision = ConflictResolutionDecisionEnum::Deferred,
): ConflictResolution {
    return new ConflictResolution(
        conflict: makeConflictResolutionConflict(),
        decision: $decision,
        selectedCandidateValue: $decision === ConflictResolutionDecisionEnum::AcceptedCandidate
            ? 'steventon-hampshire'
            : null,
    );
}

it('constructs correctly with no resolutions', function () {
    $resolutionSet = new ConflictResolutionSet([]);

    expect($resolutionSet)->toBeInstanceOf(ConflictResolutionSet::class)
        ->and($resolutionSet)->toBeInstanceOf(Countable::class)
        ->and($resolutionSet->getResolutions())->toBe([])
        ->and($resolutionSet->isEmpty())->toBeTrue()
        ->and($resolutionSet->count())->toBe(0)
        ->and(count($resolutionSet))->toBe(0)
        ->and($resolutionSet->first())->toBeNull();
});

it('constructs correctly with resolutions', function () {
    $firstResolution = makeConflictResolutionSetResolution();
    $secondResolution = makeConflictResolutionSetResolution(
        ConflictResolutionDecisionEnum::ManualReviewRequired,
    );

    $resolutions = [
        $firstResolution,
        $secondResolution,
    ];

    $resolutionSet = new ConflictResolutionSet($resolutions);

    expect($resolutionSet->getResolutions())->toBe($resolutions)
        ->and($resolutionSet->isEmpty())->toBeFalse()
        ->and($resolutionSet->count())->toBe(2)
        ->and(count($resolutionSet))->toBe(2)
        ->and($resolutionSet->first())->toBe($firstResolution);
});
