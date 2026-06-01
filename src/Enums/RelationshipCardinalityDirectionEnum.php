<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums;

enum RelationshipCardinalityDirectionEnum: string
{
    case UniqueObjectPerSubject = 'unique_object_per_subject';
    case UniqueSubjectPerObject = 'unique_subject_per_object';
}
