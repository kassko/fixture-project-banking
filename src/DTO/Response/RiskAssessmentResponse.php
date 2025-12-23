<?php

declare(strict_types=1);

namespace App\DTO\Response;

class RiskAssessmentResponse
{
    public function __construct(
        private int $customerId,
        private float $riskScore,
        private string $riskLevel,
        private array $riskFactors,
        private ?RiskReport $report = null
    ) {
    }

    public function getRiskScore(): float
    {
        return $this->riskScore;
    }

    public function getRiskLevel(): string
    {
        return $this->riskLevel;
    }

    public function getRiskFactors(): array
    {
        return $this->riskFactors;
    }

    public function toArray(): array
    {
        $result = [
            'customer_id' => $this->customerId,
            'risk_score' => $this->riskScore,
            'risk_level' => $this->riskLevel,
            'risk_factors' => $this->riskFactors,
        ];

        if ($this->report !== null) {
            $result['report'] = $this->report->toArray();
        }

        return $result;
    }
}
