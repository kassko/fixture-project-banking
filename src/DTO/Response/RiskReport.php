<?php

declare(strict_types=1);

namespace App\DTO\Response;

class RiskReport
{
    public function __construct(
        private int $customerId,
        private float $riskScore,
        private string $riskLevel,
        private array $factors,
        private array $recommendations,
        private string $generatedAt,
        private array $summary
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'risk_score' => $this->riskScore,
            'risk_level' => $this->riskLevel,
            'factors' => $this->factors,
            'recommendations' => $this->recommendations,
            'generated_at' => $this->generatedAt,
            'summary' => $this->summary,
        ];
    }
}
