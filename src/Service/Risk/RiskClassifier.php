<?php

declare(strict_types=1);

namespace App\Service\Risk;

class RiskClassifier
{
    public function classify(float $riskScore): string
    {
        return match (true) {
            $riskScore >= 75 => 'LOW',
            $riskScore >= 50 => 'MEDIUM',
            $riskScore >= 25 => 'HIGH',
            default => 'CRITICAL',
        };
    }

    public function getClassificationDetails(string $riskLevel): array
    {
        return match ($riskLevel) {
            'LOW' => [
                'level' => 'LOW',
                'color' => 'green',
                'description' => 'Low risk - Excellent creditworthiness',
                'recommended_actions' => [
                    'Eligible for premium products',
                    'Higher credit limits available',
                    'Preferential interest rates',
                ],
                'restrictions' => [],
            ],
            'MEDIUM' => [
                'level' => 'MEDIUM',
                'color' => 'yellow',
                'description' => 'Medium risk - Good creditworthiness with minor concerns',
                'recommended_actions' => [
                    'Standard product offerings',
                    'Regular credit limit',
                    'Standard interest rates',
                ],
                'restrictions' => [
                    'Premium products may require additional verification',
                ],
            ],
            'HIGH' => [
                'level' => 'HIGH',
                'color' => 'orange',
                'description' => 'High risk - Creditworthiness concerns present',
                'recommended_actions' => [
                    'Limited product offerings',
                    'Lower credit limits',
                    'Higher interest rates',
                    'Additional collateral may be required',
                ],
                'restrictions' => [
                    'Premium products not available',
                    'Loan amounts limited',
                ],
            ],
            'CRITICAL' => [
                'level' => 'CRITICAL',
                'color' => 'red',
                'description' => 'Critical risk - Significant creditworthiness issues',
                'recommended_actions' => [
                    'Risk mitigation plan required',
                    'Mandatory financial counseling',
                    'Debt consolidation options',
                ],
                'restrictions' => [
                    'New credit products not available',
                    'Existing credit limits frozen',
                    'Manual approval required for all transactions',
                ],
            ],
            default => [
                'level' => 'UNKNOWN',
                'color' => 'grey',
                'description' => 'Risk level could not be determined',
                'recommended_actions' => [],
                'restrictions' => [],
            ],
        };
    }

    public function getLimitsForRiskLevel(string $riskLevel): array
    {
        return match ($riskLevel) {
            'LOW' => [
                'max_loan_amount' => 500000,
                'max_credit_limit' => 50000,
                'min_down_payment_percent' => 10,
            ],
            'MEDIUM' => [
                'max_loan_amount' => 250000,
                'max_credit_limit' => 25000,
                'min_down_payment_percent' => 20,
            ],
            'HIGH' => [
                'max_loan_amount' => 100000,
                'max_credit_limit' => 10000,
                'min_down_payment_percent' => 30,
            ],
            'CRITICAL' => [
                'max_loan_amount' => 0,
                'max_credit_limit' => 0,
                'min_down_payment_percent' => 100,
            ],
            default => [
                'max_loan_amount' => 0,
                'max_credit_limit' => 0,
                'min_down_payment_percent' => 100,
            ],
        };
    }
}
