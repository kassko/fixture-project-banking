<?php

declare(strict_types=1);

namespace App\DTO\Request;

class RiskAssessmentRequest
{
    public function __construct(
        private int $customerId,
        private ?array $includeFactors = null,
        private bool $generateReport = true
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getIncludeFactors(): ?array
    {
        return $this->includeFactors;
    }

    public function isGenerateReport(): bool
    {
        return $this->generateReport;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['includeFactors'] ?? null,
            $data['generateReport'] ?? true
        );
    }
}
