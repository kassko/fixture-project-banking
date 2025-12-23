<?php

declare(strict_types=1);

namespace App\Service\Portfolio;

class DiversificationAnalyzer
{
    public function analyzeDiversification(array $portfolio): array
    {
        $assetAllocation = $this->calculateAssetAllocation($portfolio);
        $sectorAllocation = $this->calculateSectorAllocation($portfolio);
        $geographicAllocation = $this->calculateGeographicAllocation($portfolio);
        
        $diversificationScore = $this->calculateDiversificationScore($assetAllocation, $sectorAllocation);
        $concentrationRisk = $this->calculateConcentrationRisk($portfolio);

        return [
            'diversification_score' => round($diversificationScore, 2),
            'asset_allocation' => $assetAllocation,
            'sector_allocation' => $sectorAllocation,
            'geographic_allocation' => $geographicAllocation,
            'concentration_risk' => $concentrationRisk,
            'recommendations' => $this->generateDiversificationRecommendations($diversificationScore, $concentrationRisk),
        ];
    }

    private function calculateAssetAllocation(array $portfolio): array
    {
        $totalValue = array_sum(array_column($portfolio, 'value'));
        $allocation = [];

        foreach ($portfolio as $asset) {
            $type = $asset['type'] ?? 'OTHER';
            if (!isset($allocation[$type])) {
                $allocation[$type] = 0;
            }
            $allocation[$type] += $asset['value'];
        }

        foreach ($allocation as $type => $value) {
            $allocation[$type] = [
                'value' => $value,
                'percentage' => round(($value / $totalValue) * 100, 2),
            ];
        }

        return $allocation;
    }

    private function calculateSectorAllocation(array $portfolio): array
    {
        $totalValue = array_sum(array_column($portfolio, 'value'));
        $allocation = [];

        foreach ($portfolio as $asset) {
            $sector = $asset['sector'] ?? 'DIVERSIFIED';
            if (!isset($allocation[$sector])) {
                $allocation[$sector] = 0;
            }
            $allocation[$sector] += $asset['value'];
        }

        foreach ($allocation as $sector => $value) {
            $allocation[$sector] = [
                'value' => $value,
                'percentage' => round(($value / $totalValue) * 100, 2),
            ];
        }

        return $allocation;
    }

    private function calculateGeographicAllocation(array $portfolio): array
    {
        $totalValue = array_sum(array_column($portfolio, 'value'));
        $allocation = [];

        foreach ($portfolio as $asset) {
            $region = $asset['region'] ?? 'GLOBAL';
            if (!isset($allocation[$region])) {
                $allocation[$region] = 0;
            }
            $allocation[$region] += $asset['value'];
        }

        foreach ($allocation as $region => $value) {
            $allocation[$region] = [
                'value' => $value,
                'percentage' => round(($value / $totalValue) * 100, 2),
            ];
        }

        return $allocation;
    }

    private function calculateDiversificationScore(array $assetAllocation, array $sectorAllocation): float
    {
        // Use Herfindahl-Hirschman Index (HHI) inverted to score
        $assetHHI = 0;
        foreach ($assetAllocation as $allocation) {
            $assetHHI += pow($allocation['percentage'], 2);
        }

        $sectorHHI = 0;
        foreach ($sectorAllocation as $allocation) {
            $sectorHHI += pow($allocation['percentage'], 2);
        }

        // Convert HHI to diversification score (0-100, higher is more diversified)
        $assetScore = max(0, 100 - ($assetHHI / 100));
        $sectorScore = max(0, 100 - ($sectorHHI / 100));

        return ($assetScore + $sectorScore) / 2;
    }

    private function calculateConcentrationRisk(array $portfolio): array
    {
        $totalValue = array_sum(array_column($portfolio, 'value'));
        $concentrations = [];

        foreach ($portfolio as $asset) {
            $percentage = ($asset['value'] / $totalValue) * 100;
            if ($percentage > 10) { // Flag assets > 10% of portfolio
                $concentrations[] = [
                    'asset' => $asset['name'] ?? $asset['symbol'],
                    'percentage' => round($percentage, 2),
                    'risk_level' => $percentage > 25 ? 'HIGH' : ($percentage > 15 ? 'MEDIUM' : 'LOW'),
                ];
            }
        }

        return $concentrations;
    }

    private function generateDiversificationRecommendations(float $score, array $concentrationRisk): array
    {
        $recommendations = [];

        if ($score < 50) {
            $recommendations[] = [
                'priority' => 'HIGH',
                'message' => 'Votre portefeuille manque de diversification. Envisagez d\'élargir vos investissements.',
            ];
        } elseif ($score < 70) {
            $recommendations[] = [
                'priority' => 'MEDIUM',
                'message' => 'Une diversification supplémentaire pourrait réduire votre risque global.',
            ];
        }

        if (!empty($concentrationRisk)) {
            $highRisk = array_filter($concentrationRisk, fn($r) => $r['risk_level'] === 'HIGH');
            if (!empty($highRisk)) {
                $recommendations[] = [
                    'priority' => 'HIGH',
                    'message' => 'Forte concentration détectée sur certains actifs. Pensez à rééquilibrer.',
                ];
            }
        }

        return $recommendations;
    }
}
