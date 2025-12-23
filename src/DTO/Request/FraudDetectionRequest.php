<?php

declare(strict_types=1);

namespace App\DTO\Request;

class FraudDetectionRequest
{
    public function __construct(
        private int $transactionId,
        private int $customerId,
        private float $amount,
        private string $merchantCategory,
        private ?string $location = null,
        private ?array $additionalData = null
    ) {
    }

    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getMerchantCategory(): string
    {
        return $this->merchantCategory;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['transactionId'] ?? 0,
            $data['customerId'] ?? 0,
            $data['amount'] ?? 0.0,
            $data['merchantCategory'] ?? '',
            $data['location'] ?? null,
            $data['additionalData'] ?? null
        );
    }
}
