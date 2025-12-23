<?php

declare(strict_types=1);

namespace App\Service\Credit;

class ScoreSimulator
{
    public function __construct(
        private ScoreCalculator $scoreCalculator
    ) {
    }

    public function simulateImpact(array $customerData, array $scenarios): array
    {
        $results = [];

        foreach ($scenarios as $scenario) {
            $modifiedData = $this->applyScenario($customerData, $scenario);
            $newScore = $this->calculateScoreFromData($modifiedData);
            $currentScore = $this->calculateScoreFromData($customerData);

            $results[] = [
                'scenario' => $scenario,
                'current_score' => $currentScore,
                'projected_score' => $newScore,
                'score_change' => $newScore - $currentScore,
                'impact' => $this->getImpactLevel($newScore - $currentScore),
            ];
        }

        return $results;
    }

    public function simulateScenario(array $customerData, array $changes): array
    {
        $currentScore = $this->calculateScoreFromData($customerData);
        $modifiedData = array_merge($customerData, $changes);
        $newScore = $this->calculateScoreFromData($modifiedData);

        return [
            'current_score' => $currentScore,
            'projected_score' => $newScore,
            'score_change' => $newScore - $currentScore,
            'impact' => $this->getImpactLevel($newScore - $currentScore),
            'changes_applied' => $changes,
            'timeframe_estimate' => $this->estimateTimeframe($changes),
        ];
    }

    private function applyScenario(array $customerData, array $scenario): array
    {
        $modifiedData = $customerData;

        switch ($scenario['type']) {
            case 'reduce_utilization':
                $reduction = $scenario['amount'] ?? 1000;
                $modifiedData['used_credit'] = max(0, ($customerData['used_credit'] ?? 0) - $reduction);
                break;

            case 'pay_on_time':
                $months = $scenario['months'] ?? 6;
                $currentOnTime = $customerData['on_time_payments'] ?? 85;
                $modifiedData['on_time_payments'] = min(100, $currentOnTime + ($months * 0.5));
                $modifiedData['late_payments'] = max(0, ($customerData['late_payments'] ?? 0) - 1);
                break;

            case 'increase_credit_limit':
                $increase = $scenario['amount'] ?? 5000;
                $modifiedData['total_credit_limit'] = ($customerData['total_credit_limit'] ?? 0) + $increase;
                break;

            case 'add_credit_type':
                $currentTypes = $customerData['credit_types'] ?? [];
                $newType = $scenario['credit_type'] ?? 'installment_loan';
                if (!in_array($newType, $currentTypes)) {
                    $modifiedData['credit_types'] = array_merge($currentTypes, [$newType]);
                }
                break;

            case 'wait_inquiries':
                $modifiedData['recent_inquiries'] = max(0, ($customerData['recent_inquiries'] ?? 0) - 1);
                break;
        }

        return $modifiedData;
    }

    private function calculateScoreFromData(array $customerData): int
    {
        $criteriaScores = [];
        $criteria = ['payment_history', 'credit_utilization', 'credit_history_length', 'credit_mix', 'recent_inquiries'];

        foreach ($criteria as $criterion) {
            $criteriaScores[$criterion] = $this->scoreCalculator->calculateCriterionScore($criterion, $customerData);
        }

        return $this->scoreCalculator->calculateScore($criteriaScores);
    }

    private function getImpactLevel(int $scoreDifference): string
    {
        $abs = abs($scoreDifference);
        
        if ($abs >= 50) {
            return 'MAJOR';
        } elseif ($abs >= 20) {
            return 'SIGNIFICANT';
        } elseif ($abs >= 10) {
            return 'MODERATE';
        } elseif ($abs >= 5) {
            return 'MINOR';
        } else {
            return 'NEGLIGIBLE';
        }
    }

    private function estimateTimeframe(array $changes): string
    {
        if (isset($changes['used_credit'])) {
            return 'Immédiat à 1 mois (après mise à jour du rapport de crédit)';
        }

        if (isset($changes['on_time_payments'])) {
            return '3 à 6 mois de paiements réguliers';
        }

        if (isset($changes['total_credit_limit'])) {
            return '1 à 2 mois (après approbation)';
        }

        if (isset($changes['credit_types'])) {
            return '6 à 12 mois (établissement du nouvel historique)';
        }

        if (isset($changes['recent_inquiries'])) {
            return '12 à 24 mois (expiration des demandes)';
        }

        return 'Variable selon les actions';
    }
}
