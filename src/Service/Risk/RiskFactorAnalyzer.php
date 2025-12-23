<?php

declare(strict_types=1);

namespace App\Service\Risk;

class RiskFactorAnalyzer
{
    public function analyzeFactors(int $customerId, array $customerData): array
    {
        $factors = [];

        // Analyze credit score
        $creditScore = $customerData['credit_score'] ?? rand(50, 95);
        $factors['credit_score'] = [
            'value' => $creditScore,
            'weight' => 0.30,
            'status' => $this->getCreditScoreStatus($creditScore),
            'description' => 'Credit score based on payment history and creditworthiness',
        ];

        // Analyze income stability
        $incomeStability = $this->calculateIncomeStability($customerData);
        $factors['income_stability'] = [
            'value' => $incomeStability,
            'weight' => 0.20,
            'status' => $incomeStability >= 7 ? 'good' : ($incomeStability >= 5 ? 'moderate' : 'poor'),
            'description' => 'Stability and consistency of income sources',
        ];

        // Analyze debt ratio
        $debtRatio = $this->calculateDebtRatio($customerData);
        $factors['debt_ratio'] = [
            'value' => $debtRatio,
            'weight' => 0.25,
            'status' => $debtRatio <= 30 ? 'good' : ($debtRatio <= 50 ? 'moderate' : 'poor'),
            'description' => 'Ratio of debt to income',
        ];

        // Analyze payment history
        $paymentHistory = $this->analyzePaymentHistory($customerData);
        $factors['payment_history'] = [
            'value' => $paymentHistory,
            'weight' => 0.15,
            'status' => $paymentHistory >= 80 ? 'excellent' : ($paymentHistory >= 60 ? 'good' : 'poor'),
            'description' => 'Track record of on-time payments',
        ];

        // Analyze account age
        $accountAge = $customerData['seniority_years'] ?? rand(1, 10);
        $factors['account_age'] = [
            'value' => $accountAge,
            'weight' => 0.10,
            'status' => $accountAge >= 5 ? 'excellent' : ($accountAge >= 2 ? 'good' : 'moderate'),
            'description' => 'Length of relationship with the bank',
        ];

        return $factors;
    }

    private function getCreditScoreStatus(float $score): string
    {
        return match (true) {
            $score >= 80 => 'excellent',
            $score >= 70 => 'good',
            $score >= 60 => 'fair',
            default => 'poor',
        };
    }

    private function calculateIncomeStability(array $customerData): float
    {
        // Simulate income stability calculation (0-10 scale)
        $baseStability = 6.0;
        
        if (isset($customerData['employment_type'])) {
            $baseStability += match ($customerData['employment_type']) {
                'permanent' => 2.0,
                'contract' => 0.5,
                'self_employed' => -1.0,
                default => 0,
            };
        }

        return min(10, max(0, $baseStability + (rand(-10, 10) / 10)));
    }

    private function calculateDebtRatio(array $customerData): float
    {
        // Simulate debt ratio calculation (percentage)
        $monthlyIncome = $customerData['monthly_income'] ?? 3000;
        $monthlyDebt = $customerData['monthly_debt'] ?? rand(500, 1500);

        return round(($monthlyDebt / $monthlyIncome) * 100, 2);
    }

    private function analyzePaymentHistory(array $customerData): float
    {
        // Simulate payment history score (0-100)
        $onTimePayments = $customerData['on_time_payments'] ?? rand(70, 100);
        return min(100, max(0, $onTimePayments));
    }
}
