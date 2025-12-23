<?php

declare(strict_types=1);

namespace App\DTO\Request;

class CreditScoringRequest
{
    public function __construct(
        private int $customerId,
        private ?array $criteriaToInclude = null,
        private bool $includeBreakdown = true,
        private bool $includeRecommendations = false
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getCriteriaToInclude(): ?array
    {
        return $this->criteriaToInclude;
    }

    public function isIncludeBreakdown(): bool
    {
        return $this->includeBreakdown;
    }

    public function isIncludeRecommendations(): bool
    {
        return $this->includeRecommendations;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['criteriaToInclude'] ?? null,
            $data['includeBreakdown'] ?? true,
            $data['includeRecommendations'] ?? false
        );
    }
}
