<?php

namespace App\Traits;

trait ValidatableTrait
{
    private array $validationErrors = [];

    public function isValid(): bool
    {
        $this->validate();
        return empty($this->validationErrors);
    }

    public function validate(): void
    {
        $this->validationErrors = [];
        // Override in implementing classes to add specific validation logic
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    protected function addValidationError(string $field, string $message): void
    {
        $this->validationErrors[$field] = $message;
    }
}
