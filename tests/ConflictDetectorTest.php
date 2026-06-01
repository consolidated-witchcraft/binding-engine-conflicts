<?php

declare(strict_types=1);

use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictDetector;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictSet;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictDetectorInterface;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleInterface;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionSetInterface;

it('returns an empty conflict set when no rules are configured', function () {
    $detector = new ConflictDetector([]);

    $projectionSet = Mockery::mock(ProjectionSetInterface::class);

    $conflictSet = $detector->detect($projectionSet);

    expect($detector)->toBeInstanceOf(ConflictDetectorInterface::class)
        ->and($conflictSet)->toBeInstanceOf(ConflictSet::class)
        ->and($conflictSet->isEmpty())->toBeTrue()
        ->and($conflictSet->count())->toBe(0);
});

it('returns conflicts detected by a single rule', function () {
    $projectionSet = Mockery::mock(ProjectionSetInterface::class);

    $conflict = makeConflictSetConflict();

    $rule = Mockery::mock(ConflictRuleInterface::class);
    $rule
        ->shouldReceive('detect')
        ->once()
        ->with($projectionSet)
        ->andReturn(new ConflictSet([
            $conflict,
        ]));

    $detector = new ConflictDetector([
        $rule,
    ]);

    $conflictSet = $detector->detect($projectionSet);

    expect($conflictSet->getConflicts())->toBe([
        $conflict,
    ]);
});

it('concatenates conflicts from multiple rules in rule order', function () {
    $projectionSet = Mockery::mock(ProjectionSetInterface::class);

    $firstConflict = makeConflictSetConflict(
        subjectKey: 'event:first',
        field: 'occurred-at',
    );

    $secondConflict = makeConflictSetConflict(
        subjectKey: 'event:second',
        field: 'location',
    );

    $firstRule = Mockery::mock(ConflictRuleInterface::class);
    $firstRule
        ->shouldReceive('detect')
        ->once()
        ->with($projectionSet)
        ->andReturn(new ConflictSet([
            $firstConflict,
        ]));

    $secondRule = Mockery::mock(ConflictRuleInterface::class);
    $secondRule
        ->shouldReceive('detect')
        ->once()
        ->with($projectionSet)
        ->andReturn(new ConflictSet([
            $secondConflict,
        ]));

    $detector = new ConflictDetector([
        $firstRule,
        $secondRule,
    ]);

    $conflictSet = $detector->detect($projectionSet);

    expect($conflictSet->getConflicts())->toBe([
        $firstConflict,
        $secondConflict,
    ]);
});
