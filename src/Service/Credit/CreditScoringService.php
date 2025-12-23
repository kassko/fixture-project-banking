<?php

declare(strict_types=1);

namespace App\Service\Credit;

use App\DTO\Request\CreditScoringRequest;
use App\DTO\Response\CreditScoringResponse;
use App\DTO\Response\ScoreBreakdown;
use App\Repository\CustomerRepository;

class CreditScoringService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private ScoreCalculator $scoreCalculator,
        private ScoringCriteriaAnalyzer $criteriaAnalyzer,
        private ScoreSimulator $scoreSimulator,
        private ScoreImprovementAdvisor $improvementAdvisor
    ) {
    }

    public function calculateScore(CreditScoringRequest $request): CreditScoringResponse
    {
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Build customer credit data
        $customerData = $this->buildCustomerCreditData($request->getCustomerId());

        // Calculate criterion scores
        $criteriaScores = $this->calculateCriteriaScores($customerData, $request->getCriteriaToInclude());

        // Calculate overall credit score
        $creditScore = $this->scoreCalculator->calculateScore($criteriaScores);
        $scoreRating = $this->getScoreRating($creditScore);

        // Generate breakdown if requested
        $breakdown = null;
        if ($request->isIncludeBreakdown()) {
            $criteriaAnalysis = $this->criteriaAnalyzer->analyzeCriteria($customerData, $criteriaScores);
            $breakdown = new ScoreBreakdown(
                $criteriaAnalysis,
                $this->scoreCalculator->getWeights(),
                $criteriaScores,
                date('Y-m-d H:i:s')
            );
        }

        // Generate recommendations if requested
        $recommendations = null;
        if ($request->isIncludeRecommendations()) {
            $criteriaAnalysis = $this->criteriaAnalyzer->analyzeCriteria($customerData, $criteriaScores);
            $recommendations = $this->improvementAdvisor->generateRecommendations(
                $creditScore,
                $criteriaAnalysis,
                $customerData
            );
        }

        return new CreditScoringResponse(
            $request->getCustomerId(),
            $creditScore,
            $scoreRating,
            $breakdown,
            $recommendations
        );
    }

    public function getScore(int $customerId): int
    {
        $customerData = $this->buildCustomerCreditData($customerId);
        $criteriaScores = $this->calculateCriteriaScores($customerData, null);
        return $this->scoreCalculator->calculateScore($criteriaScores);
    }

    public function getBreakdown(int $customerId): array
    {
        $customerData = $this->buildCustomerCreditData($customerId);
        $criteriaScores = $this->calculateCriteriaScores($customerData, null);
        $criteriaAnalysis = $this->criteriaAnalyzer->analyzeCriteria($customerData, $criteriaScores);

        return [
            'customer_id' => $customerId,
            'credit_score' => $this->scoreCalculator->calculateScore($criteriaScores),
            'criteria_analysis' => $criteriaAnalysis,
            'weights' => $this->scoreCalculator->getWeights(),
            'strengths' => $this->criteriaAnalyzer->identifyStrengths($criteriaAnalysis),
            'weaknesses' => $this->criteriaAnalyzer->identifyWeaknesses($criteriaAnalysis),
        ];
    }

    public function simulateImpact(int $customerId, array $changes): array
    {
        $customerData = $this->buildCustomerCreditData($customerId);
        return $this->scoreSimulator->simulateScenario($customerData, $changes);
    }

    public function getRecommendations(int $customerId): array
    {
        $customerData = $this->buildCustomerCreditData($customerId);
        $criteriaScores = $this->calculateCriteriaScores($customerData, null);
        $creditScore = $this->scoreCalculator->calculateScore($criteriaScores);
        $criteriaAnalysis = $this->criteriaAnalyzer->analyzeCriteria($customerData, $criteriaScores);

        $recommendations = $this->improvementAdvisor->generateRecommendations(
            $creditScore,
            $criteriaAnalysis,
            $customerData
        );

        $actionPlan = $this->improvementAdvisor->generateActionPlan(
            $recommendations,
            750, // Target score
            $creditScore
        );

        return [
            'customer_id' => $customerId,
            'current_score' => $creditScore,
            'recommendations' => $recommendations,
            'action_plan' => $actionPlan,
        ];
    }

    private function buildCustomerCreditData(int $customerId): array
    {
        // Simulate customer credit data
        return [
            'on_time_payments' => rand(70, 100),
            'late_payments' => rand(0, 10),
            'missed_payments' => rand(0, 3),
            'total_credit_limit' => rand(5000, 20000),
            'used_credit' => rand(1000, 8000),
            'account_age_years' => rand(1, 15),
            'oldest_account' => date('Y-m-d', strtotime('-' . rand(1, 15) . ' years')),
            'credit_types' => $this->generateCreditTypes(),
            'recent_inquiries' => rand(0, 5),
            'last_inquiry_date' => date('Y-m-d', strtotime('-' . rand(1, 24) . ' months')),
        ];
    }

    private function generateCreditTypes(): array
    {
        $allTypes = ['credit_card', 'installment_loan', 'mortgage', 'auto_loan', 'student_loan'];
        $count = rand(1, 4);
        return array_slice($allTypes, 0, $count);
    }

    private function calculateCriteriaScores(array $customerData, ?array $criteriaToInclude): array
    {
        $allCriteria = [
            'payment_history',
            'credit_utilization',
            'credit_history_length',
            'credit_mix',
            'recent_inquiries',
        ];

        $criteria = $criteriaToInclude ?? $allCriteria;
        $scores = [];

        foreach ($criteria as $criterion) {
            if (in_array($criterion, $allCriteria)) {
                $scores[$criterion] = $this->scoreCalculator->calculateCriterionScore($criterion, $customerData);
            }
        }

        return $scores;
    }

    private function getScoreRating(int $score): string
    {
        if ($score >= 800) {
            return 'EXCEPTIONAL';
        } elseif ($score >= 740) {
            return 'VERY_GOOD';
        } elseif ($score >= 670) {
            return 'GOOD';
        } elseif ($score >= 580) {
            return 'FAIR';
        } elseif ($score >= 300) {
            return 'POOR';
        } else {
            return 'VERY_POOR';
        }
    }
}
