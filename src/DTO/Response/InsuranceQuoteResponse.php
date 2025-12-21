<?php

declare(strict_types=1);

namespace App\DTO\Response;

class InsuranceQuoteResponse
{
    public function __construct(
        private int $customerId,
        private string $insuranceType,
        private array $formulas,
        private array $customerRiskProfile,
        private string $recommendedFormula
    ) {
    }

    public function toArray(): array
    {
        return [
            'customer_id' => $this->customerId,
            'insurance_type' => $this->insuranceType,
            'formulas' => array_map(fn(InsuranceFormula $f) => $f->toArray(), $this->formulas),
            'customer_risk_profile' => $this->customerRiskProfile,
            'recommended_formula' => $this->recommendedFormula,
        ];
    }
}
