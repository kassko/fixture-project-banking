<?php

declare(strict_types=1);

namespace App\DTO\Request;

class ConsolidationRequest
{
    public function __construct(
        private int $customerId,
        private ?array $accountIds = null,
        private bool $includeInactiveAccounts = false,
        private ?string $consolidationType = 'ALL'
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAccountIds(): ?array
    {
        return $this->accountIds;
    }

    public function isIncludeInactiveAccounts(): bool
    {
        return $this->includeInactiveAccounts;
    }

    public function getConsolidationType(): string
    {
        return $this->consolidationType ?? 'ALL';
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['accountIds'] ?? null,
            $data['includeInactiveAccounts'] ?? false,
            $data['consolidationType'] ?? 'ALL'
        );
    }
}
