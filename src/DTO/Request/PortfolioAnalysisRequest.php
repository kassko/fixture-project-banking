<?php

declare(strict_types=1);

namespace App\DTO\Request;

class PortfolioAnalysisRequest
{
    public function __construct(
        private int $customerId,
        private ?array $assetTypes = null,
        private ?string $benchmarkIndex = null,
        private bool $includeOptimization = false
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAssetTypes(): ?array
    {
        return $this->assetTypes;
    }

    public function getBenchmarkIndex(): ?string
    {
        return $this->benchmarkIndex;
    }

    public function isIncludeOptimization(): bool
    {
        return $this->includeOptimization;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['assetTypes'] ?? null,
            $data['benchmarkIndex'] ?? null,
            $data['includeOptimization'] ?? false
        );
    }
}
