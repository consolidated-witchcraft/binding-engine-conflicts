<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts;

use ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums\ConflictResolutionDecisionEnum;
use ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions\InvalidConflictException;

final readonly class ConflictResolution
{
    /**
     * @throws InvalidConflictException
     */
    public function __construct(
        private Conflict $conflict,
        private ConflictResolutionDecisionEnum $decision,
        private ?string $selectedCandidateValue = null,
        private ?string $note = null,
    ) {
        $this->guard();
    }

    public function getConflict(): Conflict
    {
        return $this->conflict;
    }

    public function getDecision(): ConflictResolutionDecisionEnum
    {
        return $this->decision;
    }

    public function getSelectedCandidateValue(): ?string
    {
        return $this->selectedCandidateValue;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @throws InvalidConflictException
     */
    private function guard(): void
    {
        if (
            $this->decision === ConflictResolutionDecisionEnum::AcceptedCandidate
            && $this->selectedCandidateValue === null
        ) {
            throw new InvalidConflictException(
                'Accepted candidate resolutions must include a selected candidate value.',
            );
        }

        if (
            $this->decision !== ConflictResolutionDecisionEnum::AcceptedCandidate
            && $this->selectedCandidateValue !== null
        ) {
            throw new InvalidConflictException(
                'Only accepted candidate resolutions may include a selected candidate value.',
            );
        }

        if ($this->note !== null && trim($this->note) === '') {
            throw new InvalidConflictException(
                'Conflict resolution note must not be empty when provided.',
            );
        }
    }
}
