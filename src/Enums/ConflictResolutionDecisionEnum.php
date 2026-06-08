<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums;

enum ConflictResolutionDecisionEnum: string
{
    case AcceptedCandidate = 'accepted_candidate';
    case Deferred = 'deferred';
    case ManualReviewRequired = 'manual_review_required';
}
