<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class RuleResult
{
    public function __construct(
        private bool $passed,
        private ?string $message = null,
        private array $conditions = []
    ) {
    }

    public function isPassed(): bool
    {
        return $this->passed;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function hasConditions(): bool
    {
        return !empty($this->conditions);
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public static function pass(?string $message = null, array $conditions = []): self
    {
        return new self(true, $message, $conditions);
    }

    public static function fail(string $message): self
    {
        return new self(false, $message);
    }
}
