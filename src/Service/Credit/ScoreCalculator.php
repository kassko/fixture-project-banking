<?php

declare(strict_types=1);

namespace App\Service\Credit;

class ScoreCalculator
{
    private const SCORE_WEIGHTS = [
        'payment_history' => 0.35,
        'credit_utilization' => 0.30,
        'credit_history_length' => 0.15,
        'credit_mix' => 0.10,
        'recent_inquiries' => 0.10,
    ];

    public function calculateScore(array $criteriaScores): int
    {
        $weightedScore = 0.0;

        foreach (self::SCORE_WEIGHTS as $criterion => $weight) {
            if (isset($criteriaScores[$criterion])) {
                $weightedScore += $criteriaScores[$criterion] * $weight;
            }
        }

        // Convert to 300-850 scale (standard FICO-like range)
        $score = 300 + ($weightedScore / 100) * 550;

        return (int) round($score);
    }

    public function calculateCriterionScore(string $criterion, array $customerData): float
    {
        return match ($criterion) {
            'payment_history' => $this->calculatePaymentHistoryScore($customerData),
            'credit_utilization' => $this->calculateCreditUtilizationScore($customerData),
            'credit_history_length' => $this->calculateCreditHistoryScore($customerData),
            'credit_mix' => $this->calculateCreditMixScore($customerData),
            'recent_inquiries' => $this->calculateInquiriesScore($customerData),
            default => 50.0,
        };
    }

    private function calculatePaymentHistoryScore(array $data): float
    {
        $onTimePayments = $data['on_time_payments'] ?? 85;
        $latePayments = $data['late_payments'] ?? 5;
        $missedPayments = $data['missed_payments'] ?? 0;

        $score = 100.0;
        $score -= $latePayments * 3;
        $score -= $missedPayments * 10;
        $score = max(0, min(100, $score));

        return $score;
    }

    private function calculateCreditUtilizationScore(array $data): float
    {
        $totalCredit = $data['total_credit_limit'] ?? 10000;
        $usedCredit = $data['used_credit'] ?? 3000;

        if ($totalCredit === 0) {
            return 50.0;
        }

        $utilizationRate = ($usedCredit / $totalCredit) * 100;

        // Optimal utilization is below 30%
        if ($utilizationRate < 10) {
            return 100.0;
        } elseif ($utilizationRate < 30) {
            return 85.0;
        } elseif ($utilizationRate < 50) {
            return 65.0;
        } elseif ($utilizationRate < 75) {
            return 40.0;
        } else {
            return 20.0;
        }
    }

    private function calculateCreditHistoryScore(array $data): float
    {
        $accountAgeYears = $data['account_age_years'] ?? 5;

        // Longer history is better
        if ($accountAgeYears >= 10) {
            return 100.0;
        } elseif ($accountAgeYears >= 7) {
            return 85.0;
        } elseif ($accountAgeYears >= 5) {
            return 70.0;
        } elseif ($accountAgeYears >= 3) {
            return 55.0;
        } elseif ($accountAgeYears >= 1) {
            return 40.0;
        } else {
            return 25.0;
        }
    }

    private function calculateCreditMixScore(array $data): float
    {
        $creditTypes = $data['credit_types'] ?? ['credit_card'];
        $numberOfTypes = count($creditTypes);

        // Having diverse credit types is good
        if ($numberOfTypes >= 4) {
            return 100.0;
        } elseif ($numberOfTypes === 3) {
            return 80.0;
        } elseif ($numberOfTypes === 2) {
            return 60.0;
        } else {
            return 40.0;
        }
    }

    private function calculateInquiriesScore(array $data): float
    {
        $recentInquiries = $data['recent_inquiries'] ?? 1;

        // Fewer recent inquiries is better
        if ($recentInquiries === 0) {
            return 100.0;
        } elseif ($recentInquiries === 1) {
            return 85.0;
        } elseif ($recentInquiries === 2) {
            return 70.0;
        } elseif ($recentInquiries <= 4) {
            return 50.0;
        } else {
            return 30.0;
        }
    }

    public function getWeights(): array
    {
        return self::SCORE_WEIGHTS;
    }
}
