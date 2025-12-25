<?php

namespace App\DataSource\Fallback;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class DefaultValuesSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true; // Always available as last resort
    }

    public function supports(string $type): bool
    {
        return true; // Supports all types with defaults
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Default values for any type
        if ($type === 'customer') {
            return [
                'id' => $id,
                'name' => 'Default Customer',
                'email' => 'default@example.com',
                'phone' => '+0000000000',
                'customerType' => 'standard'
            ];
        }

        if ($type === 'product') {
            return [
                'id' => $id,
                'name' => 'Default Product',
                'description' => 'Default product description',
                'price' => 0.00,
                'isActive' => false
            ];
        }

        if ($type === 'risk') {
            return [
                'customerId' => $id,
                'riskLevel' => 'unknown',
                'riskScore' => 50.0
            ];
        }

        return [
            'id' => $id,
            'type' => $type,
            'status' => 'default'
        ];
    }

    public function getName(): string
    {
        return 'default_values';
    }

    public function getPriority(): int
    {
        return 1; // Lowest priority
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
