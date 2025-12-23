<?php

declare(strict_types=1);

namespace App\Service\Portfolio;

class AllocationOptimizer
{
    public function optimizeAllocation(array $portfolio, array $customerProfile): array
    {
        $currentAllocation = $this->getCurrentAllocation($portfolio);
        $targetAllocation = $this->calculateTargetAllocation($customerProfile);
        $rebalancingNeeds = $this->calculateRebalancingNeeds($currentAllocation, $targetAllocation);

        return [
            'current_allocation' => $currentAllocation,
            'target_allocation' => $targetAllocation,
            'rebalancing_needs' => $rebalancingNeeds,
            'optimization_suggestions' => $this->generateOptimizationSuggestions($rebalancingNeeds, $customerProfile),
        ];
    }

    private function getCurrentAllocation(array $portfolio): array
    {
        $totalValue = array_sum(array_column($portfolio, 'value'));
        $allocation = [
            'STOCKS' => 0,
            'BONDS' => 0,
            'CASH' => 0,
            'REAL_ESTATE' => 0,
            'COMMODITIES' => 0,
        ];

        foreach ($portfolio as $asset) {
            $type = $asset['type'] ?? 'CASH';
            if (isset($allocation[$type])) {
                $allocation[$type] += $asset['value'];
            }
        }

        foreach ($allocation as $type => $value) {
            $allocation[$type] = [
                'value' => $value,
                'percentage' => round(($value / $totalValue) * 100, 2),
            ];
        }

        return $allocation;
    }

    private function calculateTargetAllocation(array $customerProfile): array
    {
        $age = $customerProfile['age'] ?? 40;
        $riskProfile = $customerProfile['risk_profile'] ?? 'MODERATE';
        
        // Rule of thumb: bonds % = age, stocks = 100 - age
        $baseStockPercentage = max(20, 100 - $age);
        $baseBondPercentage = min(80, $age);

        // Adjust based on risk profile
        $adjustments = match ($riskProfile) {
            'AGGRESSIVE' => ['stocks' => 10, 'bonds' => -10],
            'CONSERVATIVE' => ['stocks' => -10, 'bonds' => 10],
            default => ['stocks' => 0, 'bonds' => 0],
        };

        $stockPercentage = max(0, min(100, $baseStockPercentage + $adjustments['stocks']));
        $bondPercentage = max(0, min(100, $baseBondPercentage + $adjustments['bonds']));
        
        // Remaining allocation
        $remaining = 100 - $stockPercentage - $bondPercentage;
        $cashPercentage = max(5, $remaining * 0.5);
        $realEstatePercentage = $remaining * 0.3;
        $commoditiesPercentage = $remaining * 0.2;

        return [
            'STOCKS' => round($stockPercentage, 2),
            'BONDS' => round($bondPercentage, 2),
            'CASH' => round($cashPercentage, 2),
            'REAL_ESTATE' => round($realEstatePercentage, 2),
            'COMMODITIES' => round($commoditiesPercentage, 2),
        ];
    }

    private function calculateRebalancingNeeds(array $current, array $target): array
    {
        $needs = [];

        foreach ($target as $assetType => $targetPercentage) {
            $currentPercentage = $current[$assetType]['percentage'] ?? 0;
            $difference = $targetPercentage - $currentPercentage;

            if (abs($difference) > 5) { // Only rebalance if difference > 5%
                $needs[] = [
                    'asset_type' => $assetType,
                    'current_percentage' => $currentPercentage,
                    'target_percentage' => $targetPercentage,
                    'difference' => round($difference, 2),
                    'action' => $difference > 0 ? 'INCREASE' : 'DECREASE',
                ];
            }
        }

        return $needs;
    }

    private function generateOptimizationSuggestions(array $rebalancingNeeds, array $customerProfile): array
    {
        $suggestions = [];

        if (empty($rebalancingNeeds)) {
            $suggestions[] = [
                'priority' => 'LOW',
                'message' => 'Votre allocation d\'actifs est bien équilibrée. Continuez à surveiller régulièrement.',
            ];
            return $suggestions;
        }

        foreach ($rebalancingNeeds as $need) {
            $message = match ($need['action']) {
                'INCREASE' => sprintf(
                    'Augmentez votre allocation en %s de %.2f%% pour atteindre votre cible.',
                    $need['asset_type'],
                    abs($need['difference'])
                ),
                'DECREASE' => sprintf(
                    'Réduisez votre allocation en %s de %.2f%% pour atteindre votre cible.',
                    $need['asset_type'],
                    abs($need['difference'])
                ),
                default => 'Rééquilibrez cette allocation.',
            };

            $priority = abs($need['difference']) > 15 ? 'HIGH' : 'MEDIUM';

            $suggestions[] = [
                'priority' => $priority,
                'message' => $message,
                'asset_type' => $need['asset_type'],
            ];
        }

        return $suggestions;
    }
}
