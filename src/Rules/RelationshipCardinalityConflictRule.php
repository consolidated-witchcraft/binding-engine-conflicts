<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Rules;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Conflict;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictCandidate;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\ConflictSet;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\RelationshipCardinalityDirectionEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictRuleInterface;
use ConsolidatedWitchcraft\BindingEngine\Projection\Interfaces\ProjectionSetInterface;
use ConsolidatedWitchcraft\BindingEngine\Projection\RelationshipProjection;

final readonly class RelationshipCardinalityConflictRule implements ConflictRuleInterface
{
    public function __construct(
        private string $relationshipType,
        private RelationshipCardinalityDirectionEnum $direction,
        private ConflictSeverityEnum $severity = ConflictSeverityEnum::ManualResolutionRequired,
    ) {
    }

    public function getIdentifier(): string
    {
        return sprintf(
            'relationship_cardinality.%s.%s',
            $this->relationshipType,
            $this->direction->value,
        );
    }

    public function detect(ProjectionSetInterface $projectionSet): ConflictSet
    {
        $groups = [];

        foreach ($projectionSet->getProjections() as $projection) {
            if (!$projection instanceof RelationshipProjection) {
                continue;
            }

            if ($projection->getRelationshipType() !== $this->relationshipType) {
                continue;
            }

            $groupKey = $this->getGroupKey($projection);
            $candidateValue = $this->getCandidateValue($projection);

            $groups[$groupKey][$candidateValue][] = $projection;
        }

        $conflicts = [];

        foreach ($groups as $groupKey => $candidateGroups) {
            if (count($candidateGroups) < 2) {
                continue;
            }

            $candidates = [];

            foreach ($candidateGroups as $candidateValue => $projections) {
                foreach ($projections as $projection) {
                    $candidates[] = new ConflictCandidate(
                        value: $candidateValue,
                        projection: $projection,
                    );
                }
            }

            $conflicts[] = new Conflict(
                conflictType: ConflictTypeEnum::RelationshipConflict,
                severity: $this->severity,
                subjectKey: $groupKey,
                field: $this->getConflictField(),
                candidates: $candidates,
            );
        }

        return new ConflictSet($conflicts);
    }

    private function getGroupKey(RelationshipProjection $projection): string
    {
        return match ($this->direction) {
            RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject => sprintf(
                'relationship:%s:subject:%s',
                $this->relationshipType,
                $projection->getSubject(),
            ),
            RelationshipCardinalityDirectionEnum::UniqueSubjectPerObject => sprintf(
                'relationship:%s:object:%s',
                $this->relationshipType,
                $projection->getObject(),
            ),
        };
    }

    private function getCandidateValue(RelationshipProjection $projection): string
    {
        return match ($this->direction) {
            RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject => $projection->getObject(),
            RelationshipCardinalityDirectionEnum::UniqueSubjectPerObject => $projection->getSubject(),
        };
    }

    private function getConflictField(): string
    {
        return match ($this->direction) {
            RelationshipCardinalityDirectionEnum::UniqueObjectPerSubject => 'object',
            RelationshipCardinalityDirectionEnum::UniqueSubjectPerObject => 'subject',
        };
    }
}
