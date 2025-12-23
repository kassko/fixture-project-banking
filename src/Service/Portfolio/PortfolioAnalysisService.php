<?php

declare(strict_types=1);

namespace App\Service\Portfolio;

use App\DTO\Request\PortfolioAnalysisRequest;
use App\DTO\Response\PortfolioAnalysisResponse;
use App\DTO\Response\PortfolioPerformance;
use App\Repository\CustomerRepository;

class PortfolioAnalysisService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private PerformanceCalculator $performanceCalculator,
        private DiversificationAnalyzer $diversificationAnalyzer,
        private AllocationOptimizer $allocationOptimizer,
        private BenchmarkComparator $benchmarkComparator
    ) {
    }

    public function analyzePortfolio(PortfolioAnalysisRequest $request): PortfolioAnalysisResponse
    {
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Build portfolio data
        $portfolio = $this->buildPortfolioData($request->getCustomerId());
        $historicalData = $this->getHistoricalData($request->getCustomerId());

        // Calculate performance
        $performanceData = $this->performanceCalculator->calculatePerformance($portfolio, $historicalData);
        $performance = new PortfolioPerformance(
            $performanceData['total_return'],
            $performanceData['annualized_return'],
            $performanceData['volatility'],
            $performanceData['sharpe_ratio'],
            $performanceData['max_drawdown'],
            $performanceData['period_returns']
        );

        // Analyze diversification
        $diversification = $this->diversificationAnalyzer->analyzeDiversification($portfolio);

        // Get allocation
        $customerProfile = $this->buildCustomerProfile($customer);
        $allocation = $this->allocationOptimizer->optimizeAllocation($portfolio, $customerProfile);

        // Compare with benchmark if provided
        $benchmarkComparison = null;
        if ($request->getBenchmarkIndex()) {
            $benchmarkComparison = $this->benchmarkComparator->compareWithBenchmark(
                $performanceData,
                $request->getBenchmarkIndex()
            );
        }

        // Generate optimization suggestions if requested
        $optimizationSuggestions = null;
        if ($request->isIncludeOptimization()) {
            $optimizationSuggestions = $this->generateOptimizationSuggestions(
                $portfolio,
                $performanceData,
                $diversification,
                $allocation
            );
        }

        return new PortfolioAnalysisResponse(
            $request->getCustomerId(),
            $this->formatPortfolio($portfolio),
            $performance,
            $diversification,
            $allocation,
            $benchmarkComparison,
            $optimizationSuggestions
        );
    }

    public function getPortfolio(int $customerId): array
    {
        $customer = $this->customerRepository->find($customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        return $this->buildPortfolioData($customerId);
    }

    public function getPerformance(int $customerId): array
    {
        $portfolio = $this->buildPortfolioData($customerId);
        $historicalData = $this->getHistoricalData($customerId);

        return $this->performanceCalculator->calculatePerformance($portfolio, $historicalData);
    }

    public function getDiversification(int $customerId): array
    {
        $portfolio = $this->buildPortfolioData($customerId);
        return $this->diversificationAnalyzer->analyzeDiversification($portfolio);
    }

    public function getAllocation(int $customerId): array
    {
        $customer = $this->customerRepository->find($customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        $portfolio = $this->buildPortfolioData($customerId);
        $customerProfile = $this->buildCustomerProfile($customer);

        return $this->allocationOptimizer->optimizeAllocation($portfolio, $customerProfile);
    }

    private function buildPortfolioData(int $customerId): array
    {
        // Simulate portfolio data
        return [
            [
                'symbol' => 'AAPL',
                'name' => 'Apple Inc.',
                'type' => 'STOCKS',
                'sector' => 'TECHNOLOGY',
                'region' => 'US',
                'quantity' => 50,
                'value' => 8500,
                'purchase_price' => 150,
                'current_price' => 170,
            ],
            [
                'symbol' => 'MSFT',
                'name' => 'Microsoft Corp.',
                'type' => 'STOCKS',
                'sector' => 'TECHNOLOGY',
                'region' => 'US',
                'quantity' => 30,
                'value' => 10500,
                'purchase_price' => 300,
                'current_price' => 350,
            ],
            [
                'symbol' => 'BOND-EU',
                'name' => 'European Government Bonds',
                'type' => 'BONDS',
                'sector' => 'FIXED_INCOME',
                'region' => 'EU',
                'quantity' => 100,
                'value' => 9800,
                'purchase_price' => 95,
                'current_price' => 98,
            ],
            [
                'symbol' => 'REIT-US',
                'name' => 'US Real Estate Investment Trust',
                'type' => 'REAL_ESTATE',
                'sector' => 'REAL_ESTATE',
                'region' => 'US',
                'quantity' => 150,
                'value' => 6750,
                'purchase_price' => 42,
                'current_price' => 45,
            ],
            [
                'symbol' => 'CASH',
                'name' => 'Cash & Equivalents',
                'type' => 'CASH',
                'sector' => 'CASH',
                'region' => 'GLOBAL',
                'quantity' => 1,
                'value' => 4450,
                'purchase_price' => 4450,
                'current_price' => 4450,
            ],
        ];
    }

    private function getHistoricalData(int $customerId): array
    {
        // Simulate historical portfolio values (last 12 months)
        $data = [];
        $baseValue = 35000;
        
        for ($i = 0; $i < 12; $i++) {
            $volatility = rand(-500, 800);
            $trend = $i * 200; // Upward trend
            $value = $baseValue + $trend + $volatility;
            
            $data[] = [
                'date' => date('Y-m-d', strtotime("-" . (12 - $i) . " months")),
                'value' => $value,
            ];
        }

        return $data;
    }

    private function buildCustomerProfile($customer): array
    {
        return [
            'age' => rand(25, 65),
            'risk_profile' => ['CONSERVATIVE', 'MODERATE', 'AGGRESSIVE'][rand(0, 2)],
            'investment_horizon' => rand(5, 30),
            'financial_goals' => ['retirement', 'wealth_accumulation', 'income_generation'],
        ];
    }

    private function formatPortfolio(array $portfolio): array
    {
        $totalValue = array_sum(array_column($portfolio, 'value'));
        
        return [
            'total_value' => $totalValue,
            'assets' => array_map(function ($asset) use ($totalValue) {
                return [
                    'symbol' => $asset['symbol'],
                    'name' => $asset['name'],
                    'type' => $asset['type'],
                    'sector' => $asset['sector'],
                    'region' => $asset['region'],
                    'quantity' => $asset['quantity'],
                    'value' => $asset['value'],
                    'weight' => round(($asset['value'] / $totalValue) * 100, 2),
                    'return' => round((($asset['current_price'] - $asset['purchase_price']) / $asset['purchase_price']) * 100, 2),
                ];
            }, $portfolio),
        ];
    }

    private function generateOptimizationSuggestions(
        array $portfolio,
        array $performance,
        array $diversification,
        array $allocation
    ): array {
        $suggestions = [];

        // Performance-based suggestions
        if ($performance['sharpe_ratio'] < 0.5) {
            $suggestions[] = [
                'type' => 'PERFORMANCE',
                'priority' => 'HIGH',
                'message' => 'Le ratio de Sharpe est faible. Envisagez de réduire le risque ou d\'améliorer les rendements.',
            ];
        }

        // Diversification suggestions
        if ($diversification['diversification_score'] < 60) {
            $suggestions[] = [
                'type' => 'DIVERSIFICATION',
                'priority' => 'HIGH',
                'message' => 'Augmentez la diversification pour réduire le risque concentré.',
            ];
        }

        // Allocation suggestions
        if (!empty($allocation['rebalancing_needs'])) {
            $suggestions[] = [
                'type' => 'REBALANCING',
                'priority' => 'MEDIUM',
                'message' => 'Rééquilibrez votre portefeuille selon les recommandations d\'allocation.',
            ];
        }

        return $suggestions;
    }
}
