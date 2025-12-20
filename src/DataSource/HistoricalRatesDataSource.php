<?php

declare(strict_types=1);

namespace App\DataSource;

/**
 * Historical interest rates data source.
 * Simulates historical rate data from an external financial data provider.
 */
class HistoricalRatesDataSource
{
    /**
     * Get historical interest rates for a specific product type.
     */
    public function getHistoricalRates(string $productType, string $startDate, string $endDate): array
    {
        // Simulate historical data
        $ratesByProduct = [
            'mortgage' => [
                ['date' => '2024-01-01', 'rate' => 3.75, 'type' => 'fixed_30y'],
                ['date' => '2024-01-15', 'rate' => 3.80, 'type' => 'fixed_30y'],
                ['date' => '2024-02-01', 'rate' => 3.70, 'type' => 'fixed_30y'],
                ['date' => '2024-02-15', 'rate' => 3.65, 'type' => 'fixed_30y'],
                ['date' => '2024-03-01', 'rate' => 3.60, 'type' => 'fixed_30y'],
            ],
            'savings' => [
                ['date' => '2024-01-01', 'rate' => 2.50, 'type' => 'standard'],
                ['date' => '2024-01-15', 'rate' => 2.55, 'type' => 'standard'],
                ['date' => '2024-02-01', 'rate' => 2.60, 'type' => 'standard'],
                ['date' => '2024-02-15', 'rate' => 2.65, 'type' => 'standard'],
                ['date' => '2024-03-01', 'rate' => 2.70, 'type' => 'standard'],
            ],
            'auto_loan' => [
                ['date' => '2024-01-01', 'rate' => 5.25, 'type' => 'new_car_60m'],
                ['date' => '2024-01-15', 'rate' => 5.30, 'type' => 'new_car_60m'],
                ['date' => '2024-02-01', 'rate' => 5.20, 'type' => 'new_car_60m'],
                ['date' => '2024-02-15', 'rate' => 5.15, 'type' => 'new_car_60m'],
                ['date' => '2024-03-01', 'rate' => 5.10, 'type' => 'new_car_60m'],
            ],
        ];
        
        $rates = $ratesByProduct[$productType] ?? throw new \InvalidArgumentException("Product type not found: $productType");
        
        // Filter by date range (simplified)
        return array_filter($rates, function ($rate) use ($startDate, $endDate) {
            return $rate['date'] >= $startDate && $rate['date'] <= $endDate;
        });
    }
    
    /**
     * Get current interest rate for a product type.
     */
    public function getCurrentRate(string $productType): array
    {
        $currentRates = [
            'mortgage' => [
                'product_type' => 'mortgage',
                'rate' => 3.60,
                'type' => 'fixed_30y',
                'effective_date' => '2024-03-01',
                'apr' => 3.75,
                'points' => 0.5,
            ],
            'savings' => [
                'product_type' => 'savings',
                'rate' => 2.70,
                'type' => 'standard',
                'effective_date' => '2024-03-01',
                'apy' => 2.73,
                'minimum_balance' => 1000.00,
            ],
            'auto_loan' => [
                'product_type' => 'auto_loan',
                'rate' => 5.10,
                'type' => 'new_car_60m',
                'effective_date' => '2024-03-01',
                'apr' => 5.25,
                'term_months' => 60,
            ],
            'credit_card' => [
                'product_type' => 'credit_card',
                'rate' => 18.99,
                'type' => 'variable',
                'effective_date' => '2024-03-01',
                'apr' => 18.99,
                'grace_period_days' => 25,
            ],
        ];
        
        return $currentRates[$productType] ?? throw new \InvalidArgumentException("Product type not found: $productType");
    }
    
    /**
     * Get rate comparison across multiple products.
     */
    public function getRateComparison(): array
    {
        return [
            'comparison_date' => '2024-03-01',
            'products' => [
                'mortgage' => $this->getCurrentRate('mortgage'),
                'savings' => $this->getCurrentRate('savings'),
                'auto_loan' => $this->getCurrentRate('auto_loan'),
                'credit_card' => $this->getCurrentRate('credit_card'),
            ],
            'market_context' => [
                'central_bank_rate' => 4.50,
                'inflation_rate' => 2.80,
                'yield_curve' => [
                    '3m' => 5.20,
                    '1y' => 4.90,
                    '5y' => 4.20,
                    '10y' => 4.00,
                    '30y' => 4.10,
                ],
            ],
        ];
    }
}
