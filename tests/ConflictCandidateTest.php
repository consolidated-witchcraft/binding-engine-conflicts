<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictCandidate;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionInterface;

it('constructs correctly', function () {
    $projection = Mockery::mock(ProjectionInterface::class);

    $candidate = new ConflictCandidate(
        value: 'jane-austen',
        projection: $projection,
    );

    expect($candidate)->toBeInstanceOf(ConflictCandidate::class)
        ->and($candidate->getValue())->toBe('jane-austen')
        ->and($candidate->getProjection())->toBe($projection);
});
