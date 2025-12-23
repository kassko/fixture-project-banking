<?php

declare(strict_types=1);

namespace App\Service\Fraud;

class PatternDetector
{
    public function detectPatterns(array $analysis, array $customerHistory): array
    {
        $patterns = [];

        // Check for unusual amount pattern
        if ($analysis['amount_analysis']['is_unusual']) {
            $patterns[] = [
                'type' => 'unusual_amount',
                'severity' => $this->getAmountSeverity($analysis['amount_analysis']),
                'description' => 'Transaction amount significantly exceeds normal spending pattern',
                'details' => $analysis['amount_analysis'],
            ];
        }

        // Check for unusual location pattern
        if ($analysis['location_analysis']['is_unusual']) {
            $patterns[] = [
                'type' => 'unusual_location',
                'severity' => 'high',
                'description' => 'Transaction from unusual or new location',
                'details' => $analysis['location_analysis'],
            ];
        }

        // Check for unusual merchant category
        if ($analysis['merchant_analysis']['is_unusual']) {
            $patterns[] = [
                'type' => 'unusual_merchant',
                'severity' => 'medium',
                'description' => 'Transaction at unusual merchant category',
                'details' => $analysis['merchant_analysis'],
            ];
        }

        // Check for unusual timing
        if ($analysis['timing_analysis']['is_unusual']) {
            $patterns[] = [
                'type' => 'time_pattern',
                'severity' => 'low',
                'description' => 'Transaction during unusual hours',
                'details' => $analysis['timing_analysis'],
            ];
        }

        // Check for velocity (multiple transactions in short time)
        $velocityPattern = $this->checkVelocity($customerHistory);
        if ($velocityPattern !== null) {
            $patterns[] = $velocityPattern;
        }

        // Check for frequency patterns
        $frequencyPattern = $this->checkFrequency($customerHistory);
        if ($frequencyPattern !== null) {
            $patterns[] = $frequencyPattern;
        }

        return $patterns;
    }

    private function getAmountSeverity(array $amountAnalysis): string
    {
        $deviation = $amountAnalysis['deviation_percentage'];
        
        return match (true) {
            $deviation > 500 => 'critical',
            $deviation > 300 => 'high',
            $deviation > 150 => 'medium',
            default => 'low',
        };
    }

    private function checkVelocity(array $history): ?array
    {
        // Simulate checking for multiple transactions in short time
        $recentTransactions = array_filter($history, function ($tx) {
            $txTime = $tx['timestamp'] ?? time();
            return (time() - $txTime) < 3600; // Last hour
        });

        if (count($recentTransactions) > 5) {
            return [
                'type' => 'velocity_check',
                'severity' => 'high',
                'description' => 'Multiple transactions in short time period',
                'details' => [
                    'transaction_count' => count($recentTransactions),
                    'time_period' => '1 hour',
                ],
            ];
        }

        return null;
    }

    private function checkFrequency(array $history): ?array
    {
        // Simulate checking transaction frequency
        $dailyTransactions = array_filter($history, function ($tx) {
            $txTime = $tx['timestamp'] ?? time();
            return (time() - $txTime) < 86400; // Last 24 hours
        });

        if (count($dailyTransactions) > 20) {
            return [
                'type' => 'unusual_frequency',
                'severity' => 'medium',
                'description' => 'Unusually high transaction frequency',
                'details' => [
                    'transaction_count' => count($dailyTransactions),
                    'time_period' => '24 hours',
                ],
            ];
        }

        return null;
    }
}
