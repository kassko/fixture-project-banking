<?php

declare(strict_types=1);

namespace App\DTO\Response;

class InsuranceFormula
{
    public function __construct(
        private string $name,
        private string $level,
        private float $annualPremium,
        private float $monthlyPremium,
        private array $coverages,
        private float $deductible,
        private array $discounts
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getAnnualPremium(): float
    {
        return $this->annualPremium;
    }

    public function getMonthlyPremium(): float
    {
        return $this->monthlyPremium;
    }

    public function getCoverages(): array
    {
        return $this->coverages;
    }

    public function getDeductible(): float
    {
        return $this->deductible;
    }

    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'level' => $this->level,
            'annual_premium' => round($this->annualPremium, 2),
            'monthly_premium' => round($this->monthlyPremium, 2),
            'coverages' => $this->coverages,
            'deductible' => round($this->deductible, 2),
            'discounts' => $this->discounts,
        ];
    }
}
