<?php

declare(strict_types=1);

namespace App\Service\Fraud;

class TransactionAnalyzer
{
    public function analyzeTransaction(array $transactionData, array $customerHistory): array
    {
        $analysis = [
            'amount_analysis' => $this->analyzeAmount($transactionData, $customerHistory),
            'location_analysis' => $this->analyzeLocation($transactionData, $customerHistory),
            'merchant_analysis' => $this->analyzeMerchant($transactionData, $customerHistory),
            'timing_analysis' => $this->analyzeTiming($transactionData, $customerHistory),
        ];

        return $analysis;
    }

    private function analyzeAmount(array $transaction, array $history): array
    {
        $amount = $transaction['amount'] ?? 0;
        $averageAmount = $this->calculateAverageAmount($history);
        $maxAmount = $this->getMaxAmount($history);

        $isUnusual = $amount > ($averageAmount * 3) || $amount > ($maxAmount * 1.5);
        
        return [
            'is_unusual' => $isUnusual,
            'amount' => $amount,
            'average_amount' => $averageAmount,
            'max_amount' => $maxAmount,
            'deviation_percentage' => $averageAmount > 0 ? (($amount - $averageAmount) / $averageAmount) * 100 : 0,
        ];
    }

    private function analyzeLocation(array $transaction, array $history): array
    {
        $location = $transaction['location'] ?? 'unknown';
        $commonLocations = $this->getCommonLocations($history);
        
        $isUnusual = !in_array($location, $commonLocations) && $location !== 'unknown';
        
        return [
            'is_unusual' => $isUnusual,
            'current_location' => $location,
            'common_locations' => $commonLocations,
        ];
    }

    private function analyzeMerchant(array $transaction, array $history): array
    {
        $merchantCategory = $transaction['merchantCategory'] ?? 'unknown';
        $commonCategories = $this->getCommonMerchantCategories($history);
        
        $isUnusual = !in_array($merchantCategory, $commonCategories);
        
        return [
            'is_unusual' => $isUnusual,
            'category' => $merchantCategory,
            'common_categories' => $commonCategories,
        ];
    }

    private function analyzeTiming(array $transaction, array $history): array
    {
        $currentHour = (int) date('H');
        $isNightTime = $currentHour < 6 || $currentHour > 22;
        
        return [
            'is_unusual' => $isNightTime,
            'hour' => $currentHour,
            'is_night_time' => $isNightTime,
        ];
    }

    private function calculateAverageAmount(array $history): float
    {
        if (empty($history)) {
            return 0.0;
        }

        $total = array_sum(array_column($history, 'amount'));
        return $total / count($history);
    }

    private function getMaxAmount(array $history): float
    {
        if (empty($history)) {
            return 0.0;
        }

        return max(array_column($history, 'amount'));
    }

    private function getCommonLocations(array $history): array
    {
        $locations = array_column($history, 'location');
        $locationCounts = array_count_values($locations);
        arsort($locationCounts);
        
        return array_slice(array_keys($locationCounts), 0, 5);
    }

    private function getCommonMerchantCategories(array $history): array
    {
        $categories = array_column($history, 'merchantCategory');
        $categoryCounts = array_count_values($categories);
        arsort($categoryCounts);
        
        return array_slice(array_keys($categoryCounts), 0, 5);
    }
}
