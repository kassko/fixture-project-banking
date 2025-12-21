<?php

declare(strict_types=1);

namespace App\Service\Simulation\PricingEngine;

class RateCalculator
{
    public function calculateLoanRate(
        float $baseRate,
        string $customerType,
        int $creditScore,
        int $customerSeniority,
        string $brandType,
        string $period
    ): array {
        $rate = $baseRate;
        $adjustments = [];

        // Customer type adjustment
        $customerAdjustment = match ($customerType) {
            'premium' => -0.3,
            'corporate' => -0.2,
            'individual' => 0.0,
            default => 0.0,
        };
        
        if ($customerAdjustment !== 0.0) {
            $rate += $customerAdjustment;
            $adjustments[] = [
                'type' => 'customer_type',
                'description' => "Ajustement client $customerType",
                'adjustment' => $customerAdjustment,
            ];
        }

        // Credit score adjustment
        if ($creditScore >= 80) {
            $scoreAdjustment = -0.5;
            $rate += $scoreAdjustment;
            $adjustments[] = [
                'type' => 'credit_score',
                'description' => 'Excellent score de crédit',
                'adjustment' => $scoreAdjustment,
            ];
        } elseif ($creditScore >= 60) {
            $scoreAdjustment = -0.2;
            $rate += $scoreAdjustment;
            $adjustments[] = [
                'type' => 'credit_score',
                'description' => 'Bon score de crédit',
                'adjustment' => $scoreAdjustment,
            ];
        }

        // Seniority adjustment (loyalty bonus)
        if ($customerSeniority >= 5) {
            $seniorityAdjustment = -0.25;
            $rate += $seniorityAdjustment;
            $adjustments[] = [
                'type' => 'seniority',
                'description' => "Bonus fidélité ($customerSeniority ans)",
                'adjustment' => $seniorityAdjustment,
            ];
        }

        // Brand adjustment
        $brandAdjustment = match ($brandType) {
            'premium' => -0.15,
            'lowcost' => 0.3,
            default => 0.0,
        };
        
        if ($brandAdjustment !== 0.0) {
            $rate += $brandAdjustment;
            $adjustments[] = [
                'type' => 'brand',
                'description' => "Ajustement brand $brandType",
                'adjustment' => $brandAdjustment,
            ];
        }

        // Period adjustment (promotions)
        if ($period === 'end_of_year_promotion') {
            $periodAdjustment = -0.4;
            $rate += $periodAdjustment;
            $adjustments[] = [
                'type' => 'period',
                'description' => 'Promotion de fin d\'année',
                'adjustment' => $periodAdjustment,
            ];
        }

        // Ensure rate doesn't go below a minimum
        $rate = max($rate, 1.0);

        return [
            'final_rate' => $rate,
            'adjustments' => $adjustments,
        ];
    }

    public function calculateMonthlyPayment(float $principal, float $annualRate, int $months): float
    {
        if ($months <= 0) {
            return 0.0;
        }

        $monthlyRate = $annualRate / 100 / 12;
        
        if ($monthlyRate == 0) {
            return $principal / $months;
        }

        return $principal * ($monthlyRate * pow(1 + $monthlyRate, $months)) / (pow(1 + $monthlyRate, $months) - 1);
    }

    public function calculateTotalCost(float $monthlyPayment, int $months): float
    {
        return $monthlyPayment * $months;
    }
}
