<?php

namespace App\DataSource\Legacy;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class LegacyDatabaseSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true; // Simulating legacy DB availability
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'product']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Flat structure mimicking old database
        if ($type === 'customer') {
            return [
                'CUST_ID' => $id,
                'CUST_NAME' => 'Legacy Customer',
                'CUST_EMAIL' => 'legacy@example.com',
                'CUST_PHONE' => '+5555555555',
                'CUST_TYPE' => 'STD',
                'CREATE_DATE' => '2020-05-10',
                'UPDATE_DATE' => '2024-01-01'
            ];
        }

        if ($type === 'product') {
            return [
                'PROD_ID' => $id,
                'PROD_NAME' => 'Legacy Product',
                'PROD_DESC' => 'Old product from legacy system',
                'PROD_PRICE' => 7500.00,
                'PROD_STATUS' => 'A'
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'legacy_database';
    }

    public function getPriority(): int
    {
        return 8;
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
