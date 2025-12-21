<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ComplianceCheckResponse
{
    public function __construct(
        private int $customerId,
        private string $overallStatus,
        private array $kycStatus,
        private array $amlStatus,
        private array $regulatoryStatus,
        private array $recommendations,
        private array $riskScore,
        private string $checkDate
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'overall_status' => $this->overallStatus,
            'kyc_status' => $this->kycStatus,
            'aml_status' => $this->amlStatus,
            'regulatory_status' => $this->regulatoryStatus,
            'recommendations' => $this->recommendations,
            'risk_score' => $this->riskScore,
            'check_date' => $this->checkDate,
        ];
    }
}
