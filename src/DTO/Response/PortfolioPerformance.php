<?php

declare(strict_types=1);

namespace App\DTO\Response;

class PortfolioPerformance
{
    public function __construct(
        private float $totalReturn,
        private float $annualizedReturn,
        private float $volatility,
        private float $sharpeRatio,
        private float $maxDrawdown,
        private array $periodReturns
    ) {
    }

    public function getTotalReturn(): float
    {
        return $this->totalReturn;
    }

    public function getAnnualizedReturn(): float
    {
        return $this->annualizedReturn;
    }

    public function getVolatility(): float
    {
        return $this->volatility;
    }

    public function getSharpeRatio(): float
    {
        return $this->sharpeRatio;
    }

    public function getMaxDrawdown(): float
    {
        return $this->maxDrawdown;
    }

    public function getPeriodReturns(): array
    {
        return $this->periodReturns;
    }

    public function toArray(): array
    {
        return [
            'total_return' => $this->totalReturn,
            'annualized_return' => $this->annualizedReturn,
            'volatility' => $this->volatility,
            'sharpe_ratio' => $this->sharpeRatio,
            'max_drawdown' => $this->maxDrawdown,
            'period_returns' => $this->periodReturns,
        ];
    }
}
