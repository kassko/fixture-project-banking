<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

class RelevanceScoreCalculator
{
    public function calculateScore(
        array $product,
        array $customerProfile,
        array $context = []
    ): float {
        $score = 0.0;
        
        // Base score from customer profile match
        $score += $this->calculateProfileMatchScore($product, $customerProfile);
        
        // Bonus from customer behavior
        $score += $this->calculateBehaviorScore($product, $customerProfile);
        
        // Context-based adjustments
        $score += $this->calculateContextScore($product, $context);
        
        // Normalize score to 0-100 range
        return min(100.0, max(0.0, $score));
    }

    private function calculateProfileMatchScore(array $product, array $customerProfile): float
    {
        $score = 50.0; // Base score
        
        // Match customer type
        $productTargetTypes = $product['target_customer_types'] ?? [];
        if (in_array($customerProfile['customer_type'], $productTargetTypes, true)) {
            $score += 15.0;
        }
        
        // Match income level
        if (isset($product['min_income']) && isset($customerProfile['annual_income'])) {
            if ($customerProfile['annual_income'] >= $product['min_income']) {
                $score += 10.0;
            }
        }
        
        // Match age range
        if (isset($product['min_age']) && isset($product['max_age']) && isset($customerProfile['age'])) {
            $age = $customerProfile['age'];
            if ($age >= $product['min_age'] && $age <= $product['max_age']) {
                $score += 5.0;
            }
        }
        
        return $score;
    }

    private function calculateBehaviorScore(array $product, array $customerProfile): float
    {
        $score = 0.0;
        
        // Reward based on credit score
        if (isset($customerProfile['credit_score'])) {
            $creditScore = $customerProfile['credit_score'];
            if ($creditScore >= 80) {
                $score += 10.0;
            } elseif ($creditScore >= 60) {
                $score += 5.0;
            }
        }
        
        // Reward customer loyalty
        if (isset($customerProfile['seniority_years'])) {
            $score += min(10.0, $customerProfile['seniority_years'] * 1.0);
        }
        
        // Consider transaction patterns
        if (isset($customerProfile['avg_monthly_transactions'])) {
            if ($customerProfile['avg_monthly_transactions'] > 50) {
                $score += 5.0;
            }
        }
        
        return $score;
    }

    private function calculateContextScore(array $product, array $context): float
    {
        $score = 0.0;
        
        // Seasonal campaigns
        if (isset($context['active_campaigns'])) {
            foreach ($context['active_campaigns'] as $campaign) {
                if (in_array($product['code'], $campaign['eligible_products'] ?? [], true)) {
                    $score += 5.0;
                }
            }
        }
        
        // Promotional period
        if (isset($context['period']) && $context['period'] === 'end_of_year_promotion') {
            $score += 3.0;
        }
        
        return $score;
    }

    public function rankRecommendations(array $recommendations): array
    {
        usort($recommendations, function ($a, $b) {
            return $b['relevance_score'] <=> $a['relevance_score'];
        });
        
        return $recommendations;
    }
}
