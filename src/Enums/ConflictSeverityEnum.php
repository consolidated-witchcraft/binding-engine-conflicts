<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Enums;

enum ConflictSeverityEnum: string
{
    case Notice = 'notice';
    case Warning = 'warning';
    case Error = 'error';
    case ManualResolutionRequired = 'manual_resolution_required';
}
