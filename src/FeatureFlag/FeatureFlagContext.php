<?php

declare(strict_types=1);

namespace App\FeatureFlag;

class FeatureFlagContext
{
    public function __construct(
        private ?string $tenantId = null,
        private ?string $brandId = null,
        private ?int $userId = null,
        private ?\DateTimeImmutable $currentDate = null
    ) {
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function getBrandId(): ?string
    {
        return $this->brandId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getCurrentDate(): ?\DateTimeImmutable
    {
        return $this->currentDate;
    }
}
