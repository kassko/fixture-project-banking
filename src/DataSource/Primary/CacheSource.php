<?php

namespace App\DataSource\Primary;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class CacheSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;
    private array $cache = [];

    public function __construct()
    {
        // Pre-populate cache with some data
        $this->cache = [
            'customer_1' => [
                'id' => 1,
                'name' => 'Cached Customer',
                'email' => 'cached@example.com',
                'phone' => '+9876543210'
            ],
            'risk_1' => [
                'customerId' => 1,
                'riskLevel' => 'low',
                'riskScore' => 85.5
            ]
        ];
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'risk']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        $key = $type . '_' . $id;
        return $this->cache[$key] ?? null;
    }

    public function getName(): string
    {
        return 'cache';
    }

    public function getPriority(): int
    {
        return 20;
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
