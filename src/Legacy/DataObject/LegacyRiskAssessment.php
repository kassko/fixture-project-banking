<?php

declare(strict_types=1);

namespace App\Legacy\DataObject;

/**
 * Legacy risk assessment data object WITHOUT Doctrine annotations.
 * Represents risk data from a legacy system.
 */
class LegacyRiskAssessment
{
    private ?string $assessmentId = null;
    
    private ?string $customerId = null;
    
    private ?int $riskScore = null;
    
    private ?string $riskCategory = null;
    
    private ?array $riskFactors = null;
    
    private ?string $assessmentDate = null;
    
    private ?string $assessedBy = null;
    
    private ?array $historicalScores = null;
    
    private ?float $defaultProbability = null;
    
    private ?string $comments = null;
    
    public function getAssessmentId(): ?string
    {
        return $this->assessmentId;
    }
    
    public function setAssessmentId(?string $assessmentId): self
    {
        $this->assessmentId = $assessmentId;
        return $this;
    }
    
    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }
    
    public function setCustomerId(?string $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }
    
    public function getRiskScore(): ?int
    {
        return $this->riskScore;
    }
    
    public function setRiskScore(?int $riskScore): self
    {
        $this->riskScore = $riskScore;
        return $this;
    }
    
    public function getRiskCategory(): ?string
    {
        return $this->riskCategory;
    }
    
    public function setRiskCategory(?string $riskCategory): self
    {
        $this->riskCategory = $riskCategory;
        return $this;
    }
    
    public function getRiskFactors(): ?array
    {
        return $this->riskFactors;
    }
    
    public function setRiskFactors(?array $riskFactors): self
    {
        $this->riskFactors = $riskFactors;
        return $this;
    }
    
    public function getAssessmentDate(): ?string
    {
        return $this->assessmentDate;
    }
    
    public function setAssessmentDate(?string $assessmentDate): self
    {
        $this->assessmentDate = $assessmentDate;
        return $this;
    }
    
    public function getAssessedBy(): ?string
    {
        return $this->assessedBy;
    }
    
    public function setAssessedBy(?string $assessedBy): self
    {
        $this->assessedBy = $assessedBy;
        return $this;
    }
    
    public function getHistoricalScores(): ?array
    {
        return $this->historicalScores;
    }
    
    public function setHistoricalScores(?array $historicalScores): self
    {
        $this->historicalScores = $historicalScores;
        return $this;
    }
    
    public function getDefaultProbability(): ?float
    {
        return $this->defaultProbability;
    }
    
    public function setDefaultProbability(?float $defaultProbability): self
    {
        $this->defaultProbability = $defaultProbability;
        return $this;
    }
    
    public function getComments(): ?string
    {
        return $this->comments;
    }
    
    public function setComments(?string $comments): self
    {
        $this->comments = $comments;
        return $this;
    }
}
