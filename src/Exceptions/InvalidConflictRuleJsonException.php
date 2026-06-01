<?php

declare(strict_types=1);

namespace ConsolidatedWitchcraft\BindingEngine\Conflicts\Exceptions;

final class InvalidConflictRuleJsonException extends InvalidConflictRuleConfigurationException
{
    public function __construct(
        string $detail,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf('Invalid conflict rule JSON: %s', $detail),
            previous: $previous,
        );
    }
}
