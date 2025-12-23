<?php

declare(strict_types=1);

namespace App\Service\Credit;

class ScoringCriteriaAnalyzer
{
    public function analyzeCriteria(array $customerData, array $criteriaScores): array
    {
        $analysis = [];

        foreach ($criteriaScores as $criterion => $score) {
            $analysis[$criterion] = [
                'score' => round($score, 2),
                'rating' => $this->getRating($score),
                'impact' => $this->getImpact($criterion),
                'status' => $this->getStatus($score),
                'details' => $this->getDetails($criterion, $customerData),
            ];
        }

        return $analysis;
    }

    private function getRating(float $score): string
    {
        if ($score >= 90) {
            return 'EXCELLENT';
        } elseif ($score >= 75) {
            return 'GOOD';
        } elseif ($score >= 60) {
            return 'FAIR';
        } elseif ($score >= 40) {
            return 'POOR';
        } else {
            return 'VERY_POOR';
        }
    }

    private function getImpact(string $criterion): string
    {
        return match ($criterion) {
            'payment_history' => 'HIGH',
            'credit_utilization' => 'HIGH',
            'credit_history_length' => 'MEDIUM',
            'credit_mix' => 'LOW',
            'recent_inquiries' => 'LOW',
            default => 'MEDIUM',
        };
    }

    private function getStatus(float $score): string
    {
        if ($score >= 75) {
            return 'POSITIVE';
        } elseif ($score >= 50) {
            return 'NEUTRAL';
        } else {
            return 'NEGATIVE';
        }
    }

    private function getDetails(string $criterion, array $customerData): array
    {
        return match ($criterion) {
            'payment_history' => [
                'on_time_payments' => $customerData['on_time_payments'] ?? 0,
                'late_payments' => $customerData['late_payments'] ?? 0,
                'missed_payments' => $customerData['missed_payments'] ?? 0,
                'description' => 'Historique de vos paiements et respect des échéances',
            ],
            'credit_utilization' => [
                'total_credit_limit' => $customerData['total_credit_limit'] ?? 0,
                'used_credit' => $customerData['used_credit'] ?? 0,
                'utilization_rate' => round((($customerData['used_credit'] ?? 0) / max(1, $customerData['total_credit_limit'] ?? 1)) * 100, 2),
                'description' => 'Pourcentage de crédit utilisé par rapport à la limite disponible',
            ],
            'credit_history_length' => [
                'account_age_years' => $customerData['account_age_years'] ?? 0,
                'oldest_account' => $customerData['oldest_account'] ?? 'N/A',
                'description' => 'Ancienneté de vos comptes de crédit',
            ],
            'credit_mix' => [
                'credit_types' => $customerData['credit_types'] ?? [],
                'number_of_types' => count($customerData['credit_types'] ?? []),
                'description' => 'Diversité des types de crédit (cartes, prêts, etc.)',
            ],
            'recent_inquiries' => [
                'inquiries_count' => $customerData['recent_inquiries'] ?? 0,
                'last_inquiry_date' => $customerData['last_inquiry_date'] ?? 'N/A',
                'description' => 'Nombre de demandes de crédit récentes',
            ],
            default => ['description' => 'Critère non défini'],
        };
    }

    public function identifyStrengths(array $criteriaAnalysis): array
    {
        $strengths = [];
        
        foreach ($criteriaAnalysis as $criterion => $analysis) {
            if ($analysis['status'] === 'POSITIVE') {
                $strengths[] = [
                    'criterion' => $criterion,
                    'score' => $analysis['score'],
                    'rating' => $analysis['rating'],
                    'message' => $this->getStrengthMessage($criterion, $analysis),
                ];
            }
        }

        return $strengths;
    }

    public function identifyWeaknesses(array $criteriaAnalysis): array
    {
        $weaknesses = [];
        
        foreach ($criteriaAnalysis as $criterion => $analysis) {
            if ($analysis['status'] === 'NEGATIVE') {
                $weaknesses[] = [
                    'criterion' => $criterion,
                    'score' => $analysis['score'],
                    'rating' => $analysis['rating'],
                    'message' => $this->getWeaknessMessage($criterion, $analysis),
                ];
            }
        }

        return $weaknesses;
    }

    private function getStrengthMessage(string $criterion, array $analysis): string
    {
        return match ($criterion) {
            'payment_history' => 'Excellent historique de paiements',
            'credit_utilization' => 'Utilisation optimale du crédit disponible',
            'credit_history_length' => 'Ancienneté de crédit bien établie',
            'credit_mix' => 'Bonne diversification des types de crédit',
            'recent_inquiries' => 'Peu de demandes de crédit récentes',
            default => 'Critère en bonne santé',
        };
    }

    private function getWeaknessMessage(string $criterion, array $analysis): string
    {
        return match ($criterion) {
            'payment_history' => 'Historique de paiements à améliorer',
            'credit_utilization' => 'Taux d\'utilisation du crédit trop élevé',
            'credit_history_length' => 'Historique de crédit récent ou limité',
            'credit_mix' => 'Diversification des types de crédit limitée',
            'recent_inquiries' => 'Trop de demandes de crédit récentes',
            default => 'Critère nécessitant une amélioration',
        };
    }
}
