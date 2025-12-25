<?php

namespace App\DataSource\Primary;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class InternalApiSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true; // Simulating always available
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'product']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Flat array structure
        if ($type === 'customer') {
            return [
                'id' => $id,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1234567890',
                'customerType' => 'standard',
                'createdAt' => '2024-01-15 10:00:00',
                'updatedAt' => '2024-06-20 14:30:00',
                'createdBy' => 'system',
                'updatedBy' => 'admin'
            ];
        }

        if ($type === 'product') {
            return [
                'id' => $id,
                'name' => 'Standard Loan',
                'description' => 'Personal loan product',
                'price' => 10000.00,
                'productType' => 'loan',
                'isActive' => true
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'internal_api';
    }

    public function getPriority(): int
    {
        return 10;
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
