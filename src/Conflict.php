<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictSeverityEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictTypeEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictException;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Interfaces\ConflictInterface;

final readonly class Conflict implements ConflictInterface
{
    /**
     * @param list<ConflictCandidate> $candidates
     *
     * @throws InvalidConflictException
     */
    public function __construct(
        private ConflictTypeEnum $conflictType,
        private ConflictSeverityEnum $severity,
        private string $subjectKey,
        private string $field,
        private array $candidates,
    ) {
        $this->guard();
    }

    public function getConflictType(): ConflictTypeEnum
    {
        return $this->conflictType;
    }

    public function getSeverity(): ConflictSeverityEnum
    {
        return $this->severity;
    }

    public function getSubjectKey(): string
    {
        return $this->subjectKey;
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return list<ConflictCandidate>
     */
    public function getCandidates(): array
    {
        return $this->candidates;
    }

    public function countCandidates(): int
    {
        return count($this->candidates);
    }

    /**
     * @throws InvalidConflictException
     */
    private function guard(): void
    {
        if (trim($this->subjectKey) === '') {
            throw new InvalidConflictException(
                'Conflict subject key must not be empty.',
            );
        }

        if (trim($this->field) === '') {
            throw new InvalidConflictException(
                'Conflict field must not be empty.',
            );
        }

        if (count($this->candidates) < 2) {
            throw new InvalidConflictException(
                'Conflict must contain at least two candidates.',
            );
        }
    }
}
