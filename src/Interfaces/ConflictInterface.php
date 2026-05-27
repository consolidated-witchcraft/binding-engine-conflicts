<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;

interface ConflictInterface
{
    public function getConflictType(): ConflictTypeEnum;

    public function getSeverity(): ConflictSeverityEnum;

    public function getSubjectKey(): string;

    public function getField(): string;

    /**
     * @return list<ConflictCandidateInterface>
     */
    public function getCandidates(): array;

    public function countCandidates(): int;
}
