<?php

declare(strict_types=1);

namespace App\DTO\Request;

class ComplianceCheckRequest
{
    public function __construct(
        private int $customerId,
        private ?array $checkTypes = null,
        private bool $includeRecommendations = true,
        private ?array $transactionIds = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getCheckTypes(): array
    {
        return $this->checkTypes ?? ['KYC', 'AML', 'REGULATORY'];
    }

    public function isIncludeRecommendations(): bool
    {
        return $this->includeRecommendations;
    }

    public function getTransactionIds(): ?array
    {
        return $this->transactionIds;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['checkTypes'] ?? null,
            $data['includeRecommendations'] ?? true,
            $data['transactionIds'] ?? null
        );
    }
}
