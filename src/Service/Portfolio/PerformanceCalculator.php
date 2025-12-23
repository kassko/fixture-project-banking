<?php

declare(strict_types=1);

namespace App\Service\Portfolio;

class PerformanceCalculator
{
    private const RISK_FREE_RATE = 0.02; // 2% annual risk-free rate

    public function calculatePerformance(array $portfolio, array $historicalData): array
    {
        $returns = $this->calculateReturns($historicalData);
        $annualizedReturn = $this->calculateAnnualizedReturn($returns);
        $volatility = $this->calculateVolatility($returns);
        $sharpeRatio = $this->calculateSharpeRatio($annualizedReturn, $volatility);
        $maxDrawdown = $this->calculateMaxDrawdown($historicalData);

        return [
            'total_return' => round(array_sum($returns), 4),
            'annualized_return' => round($annualizedReturn, 4),
            'volatility' => round($volatility, 4),
            'sharpe_ratio' => round($sharpeRatio, 4),
            'max_drawdown' => round($maxDrawdown, 4),
            'period_returns' => $this->formatPeriodReturns($returns),
        ];
    }

    private function calculateReturns(array $historicalData): array
    {
        $returns = [];
        for ($i = 1; $i < count($historicalData); $i++) {
            $previousValue = $historicalData[$i - 1]['value'];
            $currentValue = $historicalData[$i]['value'];
            $returns[] = ($currentValue - $previousValue) / $previousValue;
        }
        return $returns;
    }

    private function calculateAnnualizedReturn(array $returns): float
    {
        if (empty($returns)) {
            return 0.0;
        }

        $totalReturn = 1.0;
        foreach ($returns as $return) {
            $totalReturn *= (1 + $return);
        }

        // Assume monthly returns, annualize them
        $periods = count($returns);
        $years = $periods / 12;
        
        if ($years <= 0) {
            return 0.0;
        }

        return pow($totalReturn, 1 / $years) - 1;
    }

    private function calculateVolatility(array $returns): float
    {
        if (count($returns) < 2) {
            return 0.0;
        }

        $mean = array_sum($returns) / count($returns);
        
        $variance = 0;
        foreach ($returns as $return) {
            $variance += pow($return - $mean, 2);
        }
        $variance /= count($returns);

        // Annualize volatility (monthly to annual)
        return sqrt($variance) * sqrt(12);
    }

    private function calculateSharpeRatio(float $annualizedReturn, float $volatility): float
    {
        if ($volatility === 0.0) {
            return 0.0;
        }

        return ($annualizedReturn - self::RISK_FREE_RATE) / $volatility;
    }

    private function calculateMaxDrawdown(array $historicalData): float
    {
        if (count($historicalData) < 2) {
            return 0.0;
        }

        $maxDrawdown = 0.0;
        $peak = $historicalData[0]['value'];

        foreach ($historicalData as $data) {
            $value = $data['value'];
            if ($value > $peak) {
                $peak = $value;
            }

            $drawdown = ($peak - $value) / $peak;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }
        }

        return $maxDrawdown;
    }

    private function formatPeriodReturns(array $returns): array
    {
        $formatted = [];
        $periods = ['1M', '3M', '6M', '1Y', 'YTD'];
        
        foreach ($periods as $period) {
            $periodReturns = $this->getPeriodReturns($returns, $period);
            $formatted[$period] = round(array_sum($periodReturns), 4);
        }

        return $formatted;
    }

    private function getPeriodReturns(array $returns, string $period): array
    {
        $count = match ($period) {
            '1M' => 1,
            '3M' => 3,
            '6M' => 6,
            '1Y' => 12,
            'YTD' => min(12, count($returns)), // Assume current year
            default => count($returns),
        };

        return array_slice($returns, -$count);
    }
}
