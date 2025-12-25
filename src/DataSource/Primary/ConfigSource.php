<?php

namespace App\DataSource\Primary;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class ConfigSource implements DataSourceInterface, FallbackAwareInterface
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
        // Nested array structure
        if ($type === 'product') {
            return [
                'product' => [
                    'identity' => [
                        'id' => $id,
                        'name' => 'Config Product'
                    ],
                    'details' => [
                        'description' => 'Product from config',
                        'price' => 5000.00,
                        'isActive' => true
                    ]
                ]
            ];
        }

        if ($type === 'risk') {
            return [
                'risk' => [
                    'profile' => [
                        'customerId' => $id,
                        'level' => 'medium'
                    ],
                    'metrics' => [
                        'score' => 70.0,
                        'factors' => ['income', 'credit_history']
                    ]
                ]
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'config';
    }

    public function getPriority(): int
    {
        return 5;
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
