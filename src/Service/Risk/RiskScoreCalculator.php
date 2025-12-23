<?php

declare(strict_types=1);

namespace App\Service\Risk;

class RiskScoreCalculator
{
    public function calculateScore(array $factors): float
    {
        $weights = [
            'credit_score' => 0.30,
            'income_stability' => 0.20,
            'debt_ratio' => 0.25,
            'payment_history' => 0.15,
            'account_age' => 0.10,
        ];

        $score = 0.0;

        foreach ($factors as $key => $value) {
            if (isset($weights[$key])) {
                // Normalize value to 0-100 scale
                $normalizedValue = $this->normalizeFactorValue($key, $value);
                $score += $normalizedValue * $weights[$key];
            }
        }

        // Return score on 0-100 scale
        return round($score, 2);
    }

    private function normalizeFactorValue(string $factor, float $value): float
    {
        return match ($factor) {
            'credit_score' => $value, // Already 0-100
            'income_stability' => min(100, $value * 10), // Convert 0-10 to 0-100
            'debt_ratio' => max(0, 100 - $value), // Lower debt ratio is better
            'payment_history' => $value, // Already 0-100
            'account_age' => min(100, $value * 5), // Years to 0-100
            default => 50.0,
        };
    }

    public function calculateTrendAnalysis(float $currentScore, array $historicalScores): array
    {
        if (empty($historicalScores)) {
            return [
                'trend' => 'stable',
                'change' => 0,
                'volatility' => 0,
            ];
        }

        $avgHistorical = array_sum($historicalScores) / count($historicalScores);
        $change = $currentScore - $avgHistorical;

        // Calculate volatility
        $variance = 0;
        foreach ($historicalScores as $score) {
            $variance += pow($score - $avgHistorical, 2);
        }
        $volatility = sqrt($variance / count($historicalScores));

        $trend = $change > 5 ? 'improving' : ($change < -5 ? 'declining' : 'stable');

        return [
            'trend' => $trend,
            'change' => round($change, 2),
            'volatility' => round($volatility, 2),
            'historical_average' => round($avgHistorical, 2),
        ];
    }
}
