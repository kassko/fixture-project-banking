<?php

declare(strict_types=1);

namespace App\Service\Credit;

class ScoreImprovementAdvisor
{
    private const TARGET_UTILIZATION_RATE = 0.30; // 30% optimal credit utilization

    public function generateRecommendations(int $currentScore, array $criteriaAnalysis, array $customerData): array
    {
        $recommendations = [];

        // Analyze each criterion and provide specific recommendations
        foreach ($criteriaAnalysis as $criterion => $analysis) {
            if ($analysis['status'] === 'NEGATIVE' || $analysis['status'] === 'NEUTRAL') {
                $recommendations = array_merge(
                    $recommendations,
                    $this->getRecommendationsForCriterion($criterion, $analysis, $customerData)
                );
            }
        }

        // Sort by priority
        usort($recommendations, function ($a, $b) {
            $priorityOrder = ['HIGH' => 0, 'MEDIUM' => 1, 'LOW' => 2];
            return $priorityOrder[$a['priority']] <=> $priorityOrder[$b['priority']];
        });

        return $recommendations;
    }

    private function getRecommendationsForCriterion(string $criterion, array $analysis, array $customerData): array
    {
        return match ($criterion) {
            'payment_history' => $this->getPaymentHistoryRecommendations($analysis, $customerData),
            'credit_utilization' => $this->getCreditUtilizationRecommendations($analysis, $customerData),
            'credit_history_length' => $this->getCreditHistoryRecommendations($analysis, $customerData),
            'credit_mix' => $this->getCreditMixRecommendations($analysis, $customerData),
            'recent_inquiries' => $this->getInquiriesRecommendations($analysis, $customerData),
            default => [],
        };
    }

    private function getPaymentHistoryRecommendations(array $analysis, array $customerData): array
    {
        $recommendations = [];

        if ($analysis['score'] < 75) {
            $recommendations[] = [
                'criterion' => 'payment_history',
                'priority' => 'HIGH',
                'action' => 'Configurez des paiements automatiques',
                'expected_impact' => '+30 à +50 points en 6-12 mois',
                'timeframe' => '6-12 mois',
                'description' => 'Les paiements à temps sont le facteur le plus important. Mettez en place des paiements automatiques pour ne jamais manquer une échéance.',
            ];

            if (($customerData['late_payments'] ?? 0) > 0) {
                $recommendations[] = [
                    'criterion' => 'payment_history',
                    'priority' => 'HIGH',
                    'action' => 'Contactez vos créanciers pour négocier',
                    'expected_impact' => 'Peut éviter de futurs retards',
                    'timeframe' => 'Immédiat',
                    'description' => 'Si vous avez des difficultés, contactez vos créanciers pour établir un plan de paiement.',
                ];
            }
        }

        return $recommendations;
    }

    private function getCreditUtilizationRecommendations(array $analysis, array $customerData): array
    {
        $recommendations = [];
        $utilizationRate = ($customerData['used_credit'] ?? 0) / max(1, $customerData['total_credit_limit'] ?? 1) * 100;

        if ($utilizationRate > (self::TARGET_UTILIZATION_RATE * 100)) {
            $targetReduction = ($customerData['used_credit'] ?? 0) - (($customerData['total_credit_limit'] ?? 0) * self::TARGET_UTILIZATION_RATE);
            
            $recommendations[] = [
                'criterion' => 'credit_utilization',
                'priority' => 'HIGH',
                'action' => sprintf('Réduisez votre solde d\'environ %.0f €', $targetReduction),
                'expected_impact' => '+20 à +40 points',
                'timeframe' => '1-3 mois',
                'description' => 'Visez à utiliser moins de 30% de votre crédit disponible. Idéalement, restez en dessous de 10%.',
            ];

            $recommendations[] = [
                'criterion' => 'credit_utilization',
                'priority' => 'MEDIUM',
                'action' => 'Demandez une augmentation de limite de crédit',
                'expected_impact' => '+10 à +20 points',
                'timeframe' => '1-2 mois',
                'description' => 'Une limite plus élevée réduira votre taux d\'utilisation si vous maintenez le même solde.',
            ];
        }

        return $recommendations;
    }

    private function getCreditHistoryRecommendations(array $analysis, array $customerData): array
    {
        $recommendations = [];
        $accountAge = $customerData['account_age_years'] ?? 0;

        if ($accountAge < 5) {
            $recommendations[] = [
                'criterion' => 'credit_history_length',
                'priority' => 'LOW',
                'action' => 'Gardez vos anciens comptes ouverts',
                'expected_impact' => '+5 à +15 points sur le long terme',
                'timeframe' => '12-24 mois',
                'description' => 'L\'ancienneté du crédit s\'améliore avec le temps. Ne fermez pas vos anciens comptes même si vous ne les utilisez plus.',
            ];

            if ($accountAge < 2) {
                $recommendations[] = [
                    'criterion' => 'credit_history_length',
                    'priority' => 'LOW',
                    'action' => 'Devenez utilisateur autorisé sur un compte ancien',
                    'expected_impact' => '+10 à +20 points',
                    'timeframe' => '1-3 mois',
                    'description' => 'Demandez à un membre de la famille avec un bon historique de vous ajouter comme utilisateur autorisé.',
                ];
            }
        }

        return $recommendations;
    }

    private function getCreditMixRecommendations(array $analysis, array $customerData): array
    {
        $recommendations = [];
        $creditTypes = $customerData['credit_types'] ?? [];

        if (count($creditTypes) < 3) {
            $recommendations[] = [
                'criterion' => 'credit_mix',
                'priority' => 'LOW',
                'action' => 'Diversifiez vos types de crédit',
                'expected_impact' => '+5 à +10 points',
                'timeframe' => '6-12 mois',
                'description' => 'Avoir différents types de crédit (cartes, prêts auto, hypothèque) peut améliorer votre score, mais ne le faites que si nécessaire.',
            ];
        }

        return $recommendations;
    }

    private function getInquiriesRecommendations(array $analysis, array $customerData): array
    {
        $recommendations = [];
        $recentInquiries = $customerData['recent_inquiries'] ?? 0;

        if ($recentInquiries > 2) {
            $recommendations[] = [
                'criterion' => 'recent_inquiries',
                'priority' => 'MEDIUM',
                'action' => 'Limitez les nouvelles demandes de crédit',
                'expected_impact' => '+5 à +15 points',
                'timeframe' => '12-24 mois',
                'description' => 'Évitez de faire plusieurs demandes de crédit en peu de temps. Les demandes restent sur votre rapport pendant 2 ans.',
            ];

            $recommendations[] = [
                'criterion' => 'recent_inquiries',
                'priority' => 'LOW',
                'action' => 'Groupez vos demandes de prêt',
                'expected_impact' => 'Minimise l\'impact',
                'timeframe' => 'Immédiat',
                'description' => 'Si vous cherchez un prêt auto ou hypothécaire, faites toutes vos demandes dans une période de 14-45 jours pour qu\'elles comptent comme une seule.',
            ];
        }

        return $recommendations;
    }

    public function generateActionPlan(array $recommendations, int $targetScore, int $currentScore): array
    {
        $scoreDifference = $targetScore - $currentScore;
        
        // Select most impactful recommendations to reach target
        $actionPlan = [
            'target_score' => $targetScore,
            'current_score' => $currentScore,
            'score_gap' => $scoreDifference,
            'recommended_actions' => [],
            'estimated_timeframe' => '',
        ];

        if ($scoreDifference <= 0) {
            $actionPlan['message'] = 'Vous avez déjà atteint ou dépassé votre score cible !';
            return $actionPlan;
        }

        // Prioritize high-impact actions
        $selectedActions = array_slice(
            array_filter($recommendations, fn($r) => $r['priority'] === 'HIGH'),
            0,
            3
        );

        if (count($selectedActions) < 3) {
            $mediumActions = array_filter($recommendations, fn($r) => $r['priority'] === 'MEDIUM');
            $selectedActions = array_merge($selectedActions, array_slice($mediumActions, 0, 3 - count($selectedActions)));
        }

        $actionPlan['recommended_actions'] = $selectedActions;
        $actionPlan['estimated_timeframe'] = '6-12 mois avec une mise en œuvre cohérente';

        return $actionPlan;
    }
}
