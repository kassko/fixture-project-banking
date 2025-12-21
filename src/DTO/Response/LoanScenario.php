<?php

declare(strict_types=1);

namespace App\DTO\Response;

class LoanScenario
{
    public function __construct(
        private string $name,
        private int $durationMonths,
        private float $monthlyPayment,
        private float $totalCost,
        private float $interestRate,
        private float $totalInterest,
        private array $rateAdjustments
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDurationMonths(): int
    {
        return $this->durationMonths;
    }

    public function getMonthlyPayment(): float
    {
        return $this->monthlyPayment;
    }

    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    public function getInterestRate(): float
    {
        return $this->interestRate;
    }

    public function getTotalInterest(): float
    {
        return $this->totalInterest;
    }

    public function getRateAdjustments(): array
    {
        return $this->rateAdjustments;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'duration_months' => $this->durationMonths,
            'monthly_payment' => round($this->monthlyPayment, 2),
            'total_cost' => round($this->totalCost, 2),
            'interest_rate' => round($this->interestRate, 2),
            'total_interest' => round($this->totalInterest, 2),
            'rate_adjustments' => $this->rateAdjustments,
        ];
    }
}
