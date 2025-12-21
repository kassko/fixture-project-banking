<?php

declare(strict_types=1);

namespace App\DTO\Request;

class InsuranceQuoteRequest
{
    public function __construct(
        private int $customerId,
        private string $insuranceType,
        private array $assetDetails
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getInsuranceType(): string
    {
        return $this->insuranceType;
    }

    public function getAssetDetails(): array
    {
        return $this->assetDetails;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['insuranceType'] ?? 'HOME',
            $data['assetDetails'] ?? []
        );
    }
}
