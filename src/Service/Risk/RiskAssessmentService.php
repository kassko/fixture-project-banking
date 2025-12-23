<?php

declare(strict_types=1);

namespace App\Service\Risk;

use App\DTO\Request\RiskAssessmentRequest;
use App\DTO\Response\RiskAssessmentResponse;
use App\DTO\Response\RiskReport;
use App\Repository\CustomerRepository;

class RiskAssessmentService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private RiskScoreCalculator $scoreCalculator,
        private RiskFactorAnalyzer $factorAnalyzer,
        private RiskClassifier $classifier
    ) {
    }

    public function assess(RiskAssessmentRequest $request): RiskAssessmentResponse
    {
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Build customer data
        $customerData = $this->buildCustomerData($customer);

        // Analyze risk factors
        $riskFactors = $this->factorAnalyzer->analyzeFactors($request->getCustomerId(), $customerData);

        // Extract factor values for score calculation
        $factorValues = array_map(fn($factor) => $factor['value'], $riskFactors);

        // Calculate risk score
        $riskScore = $this->scoreCalculator->calculateScore($factorValues);

        // Classify risk level
        $riskLevel = $this->classifier->classify($riskScore);

        // Generate report if requested
        $report = null;
        if ($request->isGenerateReport()) {
            $report = $this->generateReport($request->getCustomerId(), $riskScore, $riskLevel, $riskFactors);
        }

        return new RiskAssessmentResponse(
            $request->getCustomerId(),
            $riskScore,
            $riskLevel,
            $riskFactors,
            $report
        );
    }

    public function getScore(int $customerId): float
    {
        $request = new RiskAssessmentRequest($customerId, null, false);
        $response = $this->assess($request);
        return $response->getRiskScore();
    }

    public function getClassification(int $customerId): array
    {
        $request = new RiskAssessmentRequest($customerId, null, false);
        $response = $this->assess($request);
        
        $classificationDetails = $this->classifier->getClassificationDetails($response->getRiskLevel());
        $limits = $this->classifier->getLimitsForRiskLevel($response->getRiskLevel());

        return [
            'customer_id' => $customerId,
            'risk_level' => $response->getRiskLevel(),
            'risk_score' => $response->getRiskScore(),
            'classification' => $classificationDetails,
            'limits' => $limits,
        ];
    }

    public function getFactors(int $customerId): array
    {
        $request = new RiskAssessmentRequest($customerId, null, false);
        $response = $this->assess($request);
        
        return [
            'customer_id' => $customerId,
            'risk_factors' => $response->getRiskFactors(),
            'overall_score' => $response->getRiskScore(),
        ];
    }

    private function buildCustomerData($customer): array
    {
        // Simulate customer data extraction
        return [
            'customer_number' => $customer->getCustomerNumber(),
            'credit_score' => rand(50, 95),
            'seniority_years' => rand(1, 10),
            'employment_type' => ['permanent', 'contract', 'self_employed'][rand(0, 2)],
            'monthly_income' => rand(2000, 8000),
            'monthly_debt' => rand(300, 2000),
            'on_time_payments' => rand(70, 100),
        ];
    }

    private function generateReport(int $customerId, float $riskScore, string $riskLevel, array $factors): RiskReport
    {
        $classificationDetails = $this->classifier->getClassificationDetails($riskLevel);
        $limits = $this->classifier->getLimitsForRiskLevel($riskLevel);

        // Generate recommendations based on risk level and factors
        $recommendations = $this->generateRecommendations($riskLevel, $factors);

        $summary = [
            'overall_assessment' => $classificationDetails['description'],
            'key_strengths' => $this->identifyStrengths($factors),
            'key_concerns' => $this->identifyConcerns($factors),
            'trend_analysis' => $this->scoreCalculator->calculateTrendAnalysis(
                $riskScore,
                $this->getHistoricalScores($customerId)
            ),
        ];

        return new RiskReport(
            $customerId,
            $riskScore,
            $riskLevel,
            $factors,
            $recommendations,
            date('Y-m-d H:i:s'),
            $summary
        );
    }

    private function generateRecommendations(string $riskLevel, array $factors): array
    {
        $recommendations = [];

        if ($riskLevel === 'CRITICAL' || $riskLevel === 'HIGH') {
            if ($factors['debt_ratio']['status'] === 'poor') {
                $recommendations[] = 'Consider debt consolidation to reduce monthly payments';
            }
            if ($factors['payment_history']['status'] === 'poor') {
                $recommendations[] = 'Set up automatic payments to improve payment history';
            }
            $recommendations[] = 'Financial counseling session recommended';
        }

        if ($riskLevel === 'MEDIUM') {
            $recommendations[] = 'Maintain current payment behavior to improve score';
            $recommendations[] = 'Consider reducing debt ratio for better rates';
        }

        if ($riskLevel === 'LOW') {
            $recommendations[] = 'Eligible for premium financial products';
            $recommendations[] = 'Consider investment opportunities';
        }

        return $recommendations;
    }

    private function identifyStrengths(array $factors): array
    {
        $strengths = [];
        foreach ($factors as $name => $factor) {
            if (in_array($factor['status'], ['excellent', 'good'])) {
                $strengths[] = [
                    'factor' => $name,
                    'status' => $factor['status'],
                    'description' => $factor['description'],
                ];
            }
        }
        return $strengths;
    }

    private function identifyConcerns(array $factors): array
    {
        $concerns = [];
        foreach ($factors as $name => $factor) {
            if (in_array($factor['status'], ['poor', 'moderate'])) {
                $concerns[] = [
                    'factor' => $name,
                    'status' => $factor['status'],
                    'description' => $factor['description'],
                ];
            }
        }
        return $concerns;
    }

    private function getHistoricalScores(int $customerId): array
    {
        // Simulate historical scores
        return [
            rand(50, 70),
            rand(55, 75),
            rand(60, 80),
        ];
    }
}
