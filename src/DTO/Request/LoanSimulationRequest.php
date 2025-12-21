<?php

declare(strict_types=1);

namespace App\DTO\Request;

class LoanSimulationRequest
{
    public function __construct(
        private int $customerId,
        private float $amount,
        private string $currency,
        private string $purpose,
        private int $preferredDuration
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getPreferredDuration(): int
    {
        return $this->preferredDuration;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['amount'] ?? 0.0,
            $data['currency'] ?? 'EUR',
            $data['purpose'] ?? 'PERSONAL',
            $data['preferredDuration'] ?? 12
        );
    }
}
