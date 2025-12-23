<?php

declare(strict_types=1);

namespace App\Service\Fraud;

use App\DTO\Request\FraudDetectionRequest;
use App\DTO\Response\FraudDetectionResponse;
use App\Repository\CustomerRepository;

class FraudDetectionService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private TransactionAnalyzer $transactionAnalyzer,
        private PatternDetector $patternDetector,
        private FraudScoreCalculator $scoreCalculator,
        private AlertManager $alertManager
    ) {
    }

    public function analyzeTransaction(FraudDetectionRequest $request): FraudDetectionResponse
    {
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Get customer transaction history
        $customerHistory = $this->getCustomerHistory($request->getCustomerId());

        // Prepare transaction data
        $transactionData = [
            'amount' => $request->getAmount(),
            'location' => $request->getLocation(),
            'merchantCategory' => $request->getMerchantCategory(),
            'timestamp' => time(),
        ];

        // Analyze the transaction
        $analysis = $this->transactionAnalyzer->analyzeTransaction($transactionData, $customerHistory);

        // Detect fraud patterns
        $patterns = $this->patternDetector->detectPatterns($analysis, $customerHistory);

        // Calculate fraud score
        $fraudScore = $this->scoreCalculator->calculateScore($patterns, $transactionData);
        $riskLevel = $this->scoreCalculator->getRiskLevel($fraudScore);
        $isBlocked = $this->scoreCalculator->shouldBlock($fraudScore);

        // Create alert if needed
        $alert = null;
        if ($fraudScore >= 40) {
            $alert = $this->alertManager->createAlert(
                $request->getTransactionId(),
                $request->getCustomerId(),
                $fraudScore,
                $patterns,
                $isBlocked
            );
        }

        // Generate recommendations
        $recommendations = $this->alertManager->generateRecommendations($fraudScore, $patterns);

        return new FraudDetectionResponse(
            $request->getTransactionId(),
            $request->getCustomerId(),
            $fraudScore,
            $riskLevel,
            $patterns,
            $isBlocked,
            $alert,
            $recommendations
        );
    }

    public function getScore(int $transactionId): float
    {
        // NOTE: Simulated fraud score for demonstration purposes.
        // In production, this should retrieve the actual calculated score from storage.
        return (float) rand(0, 100);
    }

    public function getCustomerAlerts(int $customerId): array
    {
        return $this->alertManager->getCustomerAlerts($customerId);
    }

    public function getPatterns(int $customerId): array
    {
        $customerHistory = $this->getCustomerHistory($customerId);
        
        // Simulate pattern analysis
        return [
            'customer_id' => $customerId,
            'common_patterns' => [
                'average_transaction_amount' => $this->calculateAverageAmount($customerHistory),
                'common_locations' => $this->getCommonLocations($customerHistory),
                'common_merchant_categories' => $this->getCommonMerchantCategories($customerHistory),
                'transaction_frequency' => count($customerHistory),
            ],
            'analyzed_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function reportFraud(int $transactionId, int $customerId, string $reason): array
    {
        // Simulate fraud reporting
        return [
            'report_id' => rand(1000, 9999),
            'transaction_id' => $transactionId,
            'customer_id' => $customerId,
            'reason' => $reason,
            'status' => 'under_investigation',
            'reported_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function resolveAlert(int $alertId, string $resolution): array
    {
        $resolved = $this->alertManager->resolveAlert($alertId);
        
        if (!$resolved) {
            throw new \RuntimeException('Alert not found');
        }

        return [
            'alert_id' => $alertId,
            'resolution' => $resolution,
            'resolved_at' => date('Y-m-d H:i:s'),
            'status' => 'resolved',
        ];
    }

    private function getCustomerHistory(int $customerId): array
    {
        // Simulate customer transaction history
        // In a real implementation, this would fetch from database
        return [
            [
                'amount' => rand(20, 200),
                'location' => 'Paris',
                'merchantCategory' => 'RETAIL',
                'timestamp' => time() - 86400,
            ],
            [
                'amount' => rand(30, 150),
                'location' => 'Paris',
                'merchantCategory' => 'FOOD',
                'timestamp' => time() - 172800,
            ],
            [
                'amount' => rand(50, 300),
                'location' => 'Lyon',
                'merchantCategory' => 'RETAIL',
                'timestamp' => time() - 259200,
            ],
        ];
    }

    private function calculateAverageAmount(array $history): float
    {
        if (empty($history)) {
            return 0.0;
        }

        $total = array_sum(array_column($history, 'amount'));
        return $total / count($history);
    }

    private function getCommonLocations(array $history): array
    {
        $locations = array_column($history, 'location');
        $locationCounts = array_count_values($locations);
        arsort($locationCounts);
        
        return array_slice(array_keys($locationCounts), 0, 5);
    }

    private function getCommonMerchantCategories(array $history): array
    {
        $categories = array_column($history, 'merchantCategory');
        $categoryCounts = array_count_values($categories);
        arsort($categoryCounts);
        
        return array_slice(array_keys($categoryCounts), 0, 5);
    }
}
