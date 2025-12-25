<?php

namespace App\DataSource\External;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class CreditBureauSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true; // Simulating credit bureau availability
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'risk']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Deep nested with lists
        if ($type === 'customer' || $type === 'risk') {
            return [
                'report' => [
                    'subject' => [
                        'id' => $id,
                        'name' => 'Bureau Subject'
                    ],
                    'scores' => [
                        ['type' => 'credit', 'value' => 750, 'source' => 'credit_bureau'],
                        ['type' => 'risk', 'value' => 25, 'source' => 'credit_bureau'],
                        ['type' => 'payment', 'value' => 90, 'source' => 'credit_bureau']
                    ],
                    'history' => [
                        'accounts' => [
                            [
                                'type' => 'credit_card',
                                'balance' => 2500.00,
                                'limit' => 10000.00,
                                'status' => 'good'
                            ],
                            [
                                'type' => 'mortgage',
                                'balance' => 150000.00,
                                'limit' => 200000.00,
                                'status' => 'good'
                            ]
                        ],
                        'inquiries' => [
                            ['date' => '2024-01-10', 'type' => 'hard'],
                            ['date' => '2024-03-15', 'type' => 'soft']
                        ]
                    ],
                    'factors' => [
                        'positive' => ['payment_history', 'account_age'],
                        'negative' => ['high_utilization']
                    ]
                ]
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'credit_bureau';
    }

    public function getPriority(): int
    {
        return 25;
    }

    public function setFallback(?DataSourceInterface $fallback): void
    {
        $this->fallback = $fallback;
    }

    public function getFallback(): ?DataSourceInterface
    {
        return $this->fallback;
    }
}
