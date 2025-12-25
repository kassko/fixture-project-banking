<?php

namespace App\DataSource\External;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class MarketDataSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true;
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['product', 'risk']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Array with lists
        if ($type === 'product') {
            return [
                'market' => [
                    'productId' => $id,
                    'competitors' => [
                        ['name' => 'Competitor A', 'price' => 9500.00],
                        ['name' => 'Competitor B', 'price' => 10500.00],
                        ['name' => 'Competitor C', 'price' => 9800.00]
                    ],
                    'trends' => [
                        ['period' => 'Q1', 'demand' => 'high'],
                        ['period' => 'Q2', 'demand' => 'medium']
                    ]
                ]
            ];
        }

        if ($type === 'risk') {
            return [
                'marketRisk' => [
                    'entityId' => $id,
                    'indicators' => [
                        ['name' => 'volatility', 'value' => 15.5],
                        ['name' => 'beta', 'value' => 1.2],
                        ['name' => 'correlation', 'value' => 0.85]
                    ],
                    'forecast' => [
                        'outlook' => 'stable',
                        'confidence' => 0.75
                    ]
                ]
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'market_data';
    }

    public function getPriority(): int
    {
        return 12;
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
