<?php

namespace App\DataSource\Fallback;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class CachedFallbackSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;
    private array $staleCache = [];

    public function __construct()
    {
        // Pre-populate with stale cached data
        $this->staleCache = [
            'customer_1' => [
                'id' => 1,
                'name' => 'Stale Cached Customer',
                'email' => 'stale@example.com',
                'phone' => '+1111111111',
                'cached' => true
            ],
            'product_1' => [
                'id' => 1,
                'name' => 'Stale Cached Product',
                'price' => 8000.00,
                'cached' => true
            ]
        ];
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'product', 'risk']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        $key = $type . '_' . $id;
        return $this->staleCache[$key] ?? null;
    }

    public function getName(): string
    {
        return 'cached_fallback';
    }

    public function getPriority(): int
    {
        return 6;
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
