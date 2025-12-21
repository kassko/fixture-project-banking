<?php

declare(strict_types=1);

namespace App\DataSource;

/**
 * External rating data source returning array data.
 * Simulates data from an external credit rating agency.
 */
class ExternalRatingDataSource
{
    /**
     * Get credit rating for a customer.
     */
    public function getCreditRating(string $customerId): array
    {
        $ratings = [
            'CUST001' => [
                'customer_id' => 'CUST001',
                'rating' => 'A',
                'score' => 720,
                'agency' => 'CreditScore France',
                'rating_date' => '2024-01-15',
                'outlook' => 'stable',
                'factors' => [
                    'payment_history' => [
                        'score' => 95,
                        'weight' => 0.35,
                        'status' => 'excellent',
                    ],
                    'credit_utilization' => [
                        'score' => 80,
                        'weight' => 0.30,
                        'percentage' => 30,
                    ],
                    'credit_age' => [
                        'score' => 70,
                        'weight' => 0.15,
                        'years' => 4,
                    ],
                    'credit_mix' => [
                        'score' => 85,
                        'weight' => 0.10,
                        'diversity' => 'good',
                    ],
                    'new_credit' => [
                        'score' => 90,
                        'weight' => 0.10,
                        'inquiries_last_year' => 1,
                    ],
                ],
                'trade_lines' => [
                    [
                        'type' => 'credit_card',
                        'opened' => '2020-01-15',
                        'limit' => 10000.00,
                        'balance' => 3000.00,
                        'status' => 'current',
                    ],
                    [
                        'type' => 'mortgage',
                        'opened' => '2021-06-01',
                        'limit' => 250000.00,
                        'balance' => 230000.00,
                        'status' => 'current',
                    ],
                ],
                'public_records' => [],
                'inquiries' => [
                    [
                        'date' => '2023-12-10',
                        'type' => 'soft',
                        'requestor' => 'Auto Insurance Quote',
                    ],
                ],
            ],
            'CUST002' => [
                'customer_id' => 'CUST002',
                'rating' => 'AA',
                'score' => 780,
                'agency' => 'CreditScore France',
                'rating_date' => '2024-02-01',
                'outlook' => 'positive',
                'factors' => [
                    'payment_history' => [
                        'score' => 100,
                        'weight' => 0.35,
                        'status' => 'perfect',
                    ],
                    'credit_utilization' => [
                        'score' => 90,
                        'weight' => 0.30,
                        'percentage' => 20,
                    ],
                    'credit_age' => [
                        'score' => 60,
                        'weight' => 0.15,
                        'years' => 3,
                    ],
                    'credit_mix' => [
                        'score' => 80,
                        'weight' => 0.10,
                        'diversity' => 'good',
                    ],
                    'new_credit' => [
                        'score' => 100,
                        'weight' => 0.10,
                        'inquiries_last_year' => 0,
                    ],
                ],
                'trade_lines' => [
                    [
                        'type' => 'credit_card',
                        'opened' => '2021-06-10',
                        'limit' => 15000.00,
                        'balance' => 3000.00,
                        'status' => 'current',
                    ],
                ],
                'public_records' => [],
                'inquiries' => [],
            ],
        ];
        
        return $ratings[$customerId] ?? throw new \InvalidArgumentException("Rating not found for customer: $customerId");
    }
}
