<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums;

enum ConflictTypeEnum: string
{
    case FieldValueConflict = 'field_value_conflict';
    case RelationshipConflict = 'relationship_conflict';
    case ChronologyConflict = 'chronology_conflict';
    case IdentityConflict = 'identity_conflict';
}
