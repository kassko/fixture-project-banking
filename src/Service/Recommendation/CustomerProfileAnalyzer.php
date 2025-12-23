<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

use App\Entity\Customer;

class CustomerProfileAnalyzer
{
    public function analyzeProfile(Customer $customer): array
    {
        return [
            'customer_type' => $this->determineCustomerType($customer),
            'age' => $this->calculateAge($customer),
            'credit_score' => $this->estimateCreditScore($customer),
            'seniority_years' => $this->calculateSeniority(),
            'annual_income' => $this->estimateIncome($customer),
            'avg_monthly_transactions' => $this->calculateAvgTransactions(),
            'risk_profile' => $this->determineRiskProfile($customer),
            'life_stage' => $this->determineLifeStage($customer),
            'financial_goals' => $this->inferFinancialGoals($customer),
        ];
    }

    private function determineCustomerType(Customer $customer): string
    {
        $className = get_class($customer);
        
        if (str_contains($className, 'VIP')) {
            return 'vip';
        } elseif (str_contains($className, 'Premium')) {
            return 'premium';
        } elseif (str_contains($className, 'Corporate')) {
            return 'corporate';
        }
        
        return 'individual';
    }

    private function calculateAge(Customer $customer): int
    {
        // Simulate age calculation - in real app would use birthDate
        return rand(25, 65);
    }

    private function estimateCreditScore(Customer $customer): int
    {
        // Simulate credit score - in real app would fetch from credit bureau
        $baseScore = 70;
        
        $customerType = $this->determineCustomerType($customer);
        if ($customerType === 'vip') {
            $baseScore += 20;
        } elseif ($customerType === 'premium') {
            $baseScore += 10;
        }
        
        return min(100, $baseScore + rand(0, 10));
    }

    private function calculateSeniority(): int
    {
        // Simulate seniority - in real app would calculate from customer creation date
        return rand(1, 15);
    }

    private function estimateIncome(Customer $customer): float
    {
        // Simulate income estimation
        $customerType = $this->determineCustomerType($customer);
        
        return match ($customerType) {
            'vip' => rand(150000, 500000),
            'premium' => rand(80000, 150000),
            'corporate' => rand(200000, 1000000),
            default => rand(30000, 80000),
        };
    }

    private function calculateAvgTransactions(): int
    {
        // Simulate transaction count
        return rand(10, 100);
    }

    private function determineRiskProfile(Customer $customer): string
    {
        $creditScore = $this->estimateCreditScore($customer);
        
        if ($creditScore >= 85) {
            return 'conservative';
        } elseif ($creditScore >= 70) {
            return 'moderate';
        } elseif ($creditScore >= 50) {
            return 'balanced';
        }
        
        return 'aggressive';
    }

    private function determineLifeStage(Customer $customer): string
    {
        $age = $this->calculateAge($customer);
        
        if ($age < 30) {
            return 'young_professional';
        } elseif ($age < 45) {
            return 'family_building';
        } elseif ($age < 60) {
            return 'wealth_accumulation';
        }
        
        return 'retirement_planning';
    }

    private function inferFinancialGoals(Customer $customer): array
    {
        $lifeStage = $this->determineLifeStage($customer);
        
        return match ($lifeStage) {
            'young_professional' => ['savings', 'credit_building', 'investment_start'],
            'family_building' => ['home_ownership', 'education_savings', 'insurance'],
            'wealth_accumulation' => ['investment_growth', 'retirement_planning', 'wealth_preservation'],
            'retirement_planning' => ['income_generation', 'estate_planning', 'healthcare'],
            default => ['general_savings'],
        };
    }
}
