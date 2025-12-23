<?php

declare(strict_types=1);

namespace App\Service\Portfolio;

class BenchmarkComparator
{
    private const BENCHMARKS = [
        'SP500' => ['name' => 'S&P 500', 'annual_return' => 0.10, 'volatility' => 0.15],
        'MSCI_WORLD' => ['name' => 'MSCI World', 'annual_return' => 0.08, 'volatility' => 0.14],
        'EURO_STOXX' => ['name' => 'Euro Stoxx 50', 'annual_return' => 0.07, 'volatility' => 0.16],
        'BONDS_AGGREGATE' => ['name' => 'Bloomberg Aggregate Bond', 'annual_return' => 0.04, 'volatility' => 0.05],
    ];

    public function compareWithBenchmark(array $performance, string $benchmarkIndex): array
    {
        if (!isset(self::BENCHMARKS[$benchmarkIndex])) {
            throw new \InvalidArgumentException('Invalid benchmark index');
        }

        $benchmark = self::BENCHMARKS[$benchmarkIndex];
        
        $portfolioReturn = $performance['annualized_return'];
        $portfolioVolatility = $performance['volatility'];
        $portfolioSharpe = $performance['sharpe_ratio'];

        $benchmarkReturn = $benchmark['annual_return'];
        $benchmarkVolatility = $benchmark['volatility'];
        $benchmarkSharpe = ($benchmarkReturn - 0.02) / $benchmarkVolatility;

        $alpha = $portfolioReturn - $benchmarkReturn;
        $beta = $this->estimateBeta($portfolioReturn, $portfolioVolatility, $benchmarkReturn, $benchmarkVolatility);

        return [
            'benchmark' => [
                'index' => $benchmarkIndex,
                'name' => $benchmark['name'],
                'return' => round($benchmarkReturn, 4),
                'volatility' => round($benchmarkVolatility, 4),
                'sharpe_ratio' => round($benchmarkSharpe, 4),
            ],
            'comparison' => [
                'alpha' => round($alpha, 4),
                'beta' => round($beta, 4),
                'return_difference' => round($portfolioReturn - $benchmarkReturn, 4),
                'volatility_difference' => round($portfolioVolatility - $benchmarkVolatility, 4),
                'sharpe_difference' => round($portfolioSharpe - $benchmarkSharpe, 4),
            ],
            'performance_rating' => $this->ratePerformance($alpha, $portfolioSharpe, $benchmarkSharpe),
            'insights' => $this->generateInsights($alpha, $beta, $portfolioSharpe, $benchmarkSharpe),
        ];
    }

    private function estimateBeta(
        float $portfolioReturn,
        float $portfolioVolatility,
        float $benchmarkReturn,
        float $benchmarkVolatility
    ): float {
        // Simplified beta estimation assuming correlation of 0.8
        $correlation = 0.8;
        return ($correlation * $portfolioVolatility) / $benchmarkVolatility;
    }

    private function ratePerformance(float $alpha, float $portfolioSharpe, float $benchmarkSharpe): string
    {
        if ($alpha > 0.02 && $portfolioSharpe > $benchmarkSharpe) {
            return 'EXCELLENT';
        } elseif ($alpha > 0 && $portfolioSharpe >= $benchmarkSharpe) {
            return 'GOOD';
        } elseif ($alpha > -0.02) {
            return 'AVERAGE';
        } else {
            return 'BELOW_AVERAGE';
        }
    }

    private function generateInsights(float $alpha, float $beta, float $portfolioSharpe, float $benchmarkSharpe): array
    {
        $insights = [];

        if ($alpha > 0) {
            $insights[] = sprintf(
                'Votre portefeuille surperforme le benchmark de %.2f%% par an.',
                $alpha * 100
            );
        } else {
            $insights[] = sprintf(
                'Votre portefeuille sous-performe le benchmark de %.2f%% par an.',
                abs($alpha) * 100
            );
        }

        if ($beta > 1.2) {
            $insights[] = 'Votre portefeuille est plus volatil que le marché (beta élevé).';
        } elseif ($beta < 0.8) {
            $insights[] = 'Votre portefeuille est moins volatil que le marché (beta faible).';
        }

        if ($portfolioSharpe > $benchmarkSharpe) {
            $insights[] = 'Vous obtenez un meilleur rendement ajusté du risque que le benchmark.';
        } else {
            $insights[] = 'Le rendement ajusté du risque pourrait être amélioré.';
        }

        return $insights;
    }

    public function getAvailableBenchmarks(): array
    {
        return array_map(function ($key, $benchmark) {
            return [
                'index' => $key,
                'name' => $benchmark['name'],
            ];
        }, array_keys(self::BENCHMARKS), self::BENCHMARKS);
    }
}
