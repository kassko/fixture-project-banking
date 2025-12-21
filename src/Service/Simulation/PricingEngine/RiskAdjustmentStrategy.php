<?php

declare(strict_types=1);

namespace App\Service\Simulation\PricingEngine;

class RiskAdjustmentStrategy
{
    public function calculateRiskPremium(
        string $insuranceType,
        int $creditScore,
        array $assetDetails,
        string $customerType
    ): array {
        $basePremium = $this->getBasePremium($insuranceType, $assetDetails);
        $adjustments = [];
        
        // Credit score adjustment
        $scoreMultiplier = 1.0;
        if ($creditScore >= 80) {
            $scoreMultiplier = 0.85;
            $adjustments[] = [
                'type' => 'credit_score',
                'description' => 'Excellent score de crédit',
                'multiplier' => $scoreMultiplier,
            ];
        } elseif ($creditScore >= 60) {
            $scoreMultiplier = 0.95;
            $adjustments[] = [
                'type' => 'credit_score',
                'description' => 'Bon score de crédit',
                'multiplier' => $scoreMultiplier,
            ];
        } elseif ($creditScore < 40) {
            $scoreMultiplier = 1.3;
            $adjustments[] = [
                'type' => 'credit_score',
                'description' => 'Score de crédit à améliorer',
                'multiplier' => $scoreMultiplier,
            ];
        }
        
        $basePremium *= $scoreMultiplier;

        // Customer type adjustment
        $customerMultiplier = match ($customerType) {
            'premium' => 0.9,
            'corporate' => 1.05,
            default => 1.0,
        };
        
        if ($customerMultiplier !== 1.0) {
            $basePremium *= $customerMultiplier;
            $adjustments[] = [
                'type' => 'customer_type',
                'description' => "Ajustement client $customerType",
                'multiplier' => $customerMultiplier,
            ];
        }

        // Asset-specific adjustments
        if ($insuranceType === 'HOME' && isset($assetDetails['yearBuilt'])) {
            $age = date('Y') - $assetDetails['yearBuilt'];
            if ($age > 30) {
                $ageMultiplier = 1.2;
                $basePremium *= $ageMultiplier;
                $adjustments[] = [
                    'type' => 'asset_age',
                    'description' => "Bâtiment ancien ($age ans)",
                    'multiplier' => $ageMultiplier,
                ];
            }
        }

        return [
            'base_premium' => $basePremium,
            'adjustments' => $adjustments,
        ];
    }

    private function getBasePremium(string $insuranceType, array $assetDetails): float
    {
        return match ($insuranceType) {
            'HOME' => ($assetDetails['value'] ?? 100000) * 0.005,
            'AUTO' => ($assetDetails['value'] ?? 20000) * 0.03,
            'LIFE' => ($assetDetails['coverage'] ?? 100000) * 0.01,
            default => 1000.0,
        };
    }
}
