<?php

namespace App\Services;

/**
 * Thrown when imported MS Project data fails validation rules.
 * Carries a structured list of error messages for display in the UI.
 */
class ImportValidationException extends \RuntimeException
{
    public function __construct(
        private readonly array $validationErrors
    ) {
        $count = count($validationErrors);
        parent::__construct("Import validation failed with {$count} issue(s).");
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /** Separate blocking errors from warnings (lines containing "Warning") */
    public function getErrors(): array
    {
        return array_values(array_filter(
            $this->validationErrors,
            fn($e) => !str_starts_with($e, 'Warning')
        ));
    }

    public function getWarnings(): array
    {
        return array_values(array_filter(
            $this->validationErrors,
            fn($e) => str_starts_with($e, 'Warning')
        ));
    }

    public function hasBlockingErrors(): bool
    {
        return !empty($this->getErrors());
    }
}
