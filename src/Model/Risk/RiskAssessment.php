<?php

namespace App\Model\Risk;

use App\Traits\IdentifiableTrait;
use App\Traits\AuditableTrait;
use App\Traits\SerializableTrait;

class RiskAssessment
{
    use IdentifiableTrait;
    use AuditableTrait;
    use SerializableTrait;

    private ?int $customerId = null;
    private ?RiskProfile $riskProfile = null;
    private array $scores = [];
    private ?string $recommendation = null;
    private ?bool $approved = null;

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getRiskProfile(): ?RiskProfile
    {
        return $this->riskProfile;
    }

    public function setRiskProfile(?RiskProfile $riskProfile): self
    {
        $this->riskProfile = $riskProfile;
        return $this;
    }

    public function getScores(): array
    {
        return $this->scores;
    }

    public function setScores(array $scores): self
    {
        $this->scores = $scores;
        return $this;
    }

    public function addScore(RiskScore $score): self
    {
        $this->scores[] = $score;
        return $this;
    }

    public function getRecommendation(): ?string
    {
        return $this->recommendation;
    }

    public function setRecommendation(?string $recommendation): self
    {
        $this->recommendation = $recommendation;
        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->approved;
    }

    public function setApproved(?bool $approved): self
    {
        $this->approved = $approved;
        return $this;
    }
}
