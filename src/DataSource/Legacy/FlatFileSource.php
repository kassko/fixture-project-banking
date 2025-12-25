<?php

namespace App\DataSource\Legacy;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class FlatFileSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true; // Simulating file availability
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'product', 'risk']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Flat structure from CSV-like source
        if ($type === 'customer') {
            return [
                'id' => $id,
                'name' => 'File Customer',
                'email' => 'file@example.com',
                'phone' => '+7777777777',
                'type' => 'standard'
            ];
        }

        if ($type === 'product') {
            return [
                'id' => $id,
                'name' => 'File Product',
                'description' => 'Product from flat file',
                'price' => 6000.00
            ];
        }

        if ($type === 'risk') {
            return [
                'customerId' => $id,
                'riskLevel' => 'low',
                'score' => 80
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'flat_file';
    }

    public function getPriority(): int
    {
        return 3;
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
