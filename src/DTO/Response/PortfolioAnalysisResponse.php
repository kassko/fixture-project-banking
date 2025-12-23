<?php

declare(strict_types=1);

namespace App\DTO\Response;

class PortfolioAnalysisResponse
{
    public function __construct(
        private int $customerId,
        private array $portfolio,
        private PortfolioPerformance $performance,
        private array $diversification,
        private array $allocation,
        private ?array $benchmarkComparison = null,
        private ?array $optimizationSuggestions = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getPortfolio(): array
    {
        return $this->portfolio;
    }

    public function getPerformance(): PortfolioPerformance
    {
        return $this->performance;
    }

    public function getDiversification(): array
    {
        return $this->diversification;
    }

    public function getAllocation(): array
    {
        return $this->allocation;
    }

    public function getBenchmarkComparison(): ?array
    {
        return $this->benchmarkComparison;
    }

    public function getOptimizationSuggestions(): ?array
    {
        return $this->optimizationSuggestions;
    }

    public function toArray(): array
    {
        $result = [
            'customer_id' => $this->customerId,
            'portfolio' => $this->portfolio,
            'performance' => $this->performance->toArray(),
            'diversification' => $this->diversification,
            'allocation' => $this->allocation,
        ];

        if ($this->benchmarkComparison !== null) {
            $result['benchmark_comparison'] = $this->benchmarkComparison;
        }

        if ($this->optimizationSuggestions !== null) {
            $result['optimization_suggestions'] = $this->optimizationSuggestions;
        }

        return $result;
    }
}
