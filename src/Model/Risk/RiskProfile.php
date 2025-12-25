<?php

namespace App\Model\Risk;

use App\Traits\IdentifiableTrait;
use App\Traits\TimestampableTrait;
use App\Traits\SerializableTrait;

class RiskProfile
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use SerializableTrait;

    private ?int $customerId = null;
    private ?string $riskLevel = null;
    private ?float $riskScore = null;
    private array $riskFactors = [];

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getRiskLevel(): ?string
    {
        return $this->riskLevel;
    }

    public function setRiskLevel(?string $riskLevel): self
    {
        $this->riskLevel = $riskLevel;
        return $this;
    }

    public function getRiskScore(): ?float
    {
        return $this->riskScore;
    }

    public function setRiskScore(?float $riskScore): self
    {
        $this->riskScore = $riskScore;
        return $this;
    }

    public function getRiskFactors(): array
    {
        return $this->riskFactors;
    }

    public function setRiskFactors(array $riskFactors): self
    {
        $this->riskFactors = $riskFactors;
        return $this;
    }

    public function addRiskFactor(string $factor, $value): self
    {
        $this->riskFactors[$factor] = $value;
        return $this;
    }
}
