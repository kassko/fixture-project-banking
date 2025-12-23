<?php

declare(strict_types=1);

namespace App\Service\Fraud;

class FraudScoreCalculator
{
    public function calculateScore(array $patterns, array $transactionData): float
    {
        $score = 0.0;
        $weights = [
            'unusual_amount' => 25.0,
            'unusual_location' => 20.0,
            'unusual_frequency' => 20.0,
            'unusual_merchant' => 15.0,
            'velocity_check' => 10.0,
            'time_pattern' => 10.0,
        ];

        foreach ($patterns as $pattern) {
            $patternType = $pattern['type'];
            $severity = $pattern['severity'];
            
            if (isset($weights[$patternType])) {
                $multiplier = match ($severity) {
                    'critical' => 1.0,
                    'high' => 0.8,
                    'medium' => 0.5,
                    'low' => 0.3,
                    default => 0.0,
                };
                
                $score += $weights[$patternType] * $multiplier;
            }
        }

        // Cap the score at 100
        return min(100.0, $score);
    }

    public function getRiskLevel(float $fraudScore): string
    {
        return match (true) {
            $fraudScore >= 80 => 'CRITICAL',
            $fraudScore >= 60 => 'HIGH',
            $fraudScore >= 40 => 'MEDIUM',
            $fraudScore >= 20 => 'LOW',
            default => 'MINIMAL',
        };
    }

    public function shouldBlock(float $fraudScore): bool
    {
        return $fraudScore >= 80.0;
    }
}
