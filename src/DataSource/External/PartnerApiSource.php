<?php

namespace App\DataSource\External;

use App\DataSource\Contract\DataSourceInterface;
use App\DataSource\Contract\FallbackAwareInterface;

class PartnerApiSource implements DataSourceInterface, FallbackAwareInterface
{
    private ?DataSourceInterface $fallback = null;

    public function isAvailable(): bool
    {
        return true; // Simulating partner API availability
    }

    public function supports(string $type): bool
    {
        return in_array($type, ['customer', 'product', 'risk']);
    }

    public function fetchData(string $type, int $id): ?array
    {
        // Deep nested structure
        if ($type === 'customer') {
            return [
                'customer' => [
                    'identity' => [
                        'id' => $id,
                        'name' => 'Partner Customer',
                        'type' => 'premium'
                    ],
                    'contact' => [
                        'email' => 'partner@example.com',
                        'phone' => '+1122334455',
                        'address' => [
                            'street' => '123 Partner St',
                            'city' => 'Partner City',
                            'postalCode' => '12345',
                            'country' => 'USA'
                        ]
                    ],
                    'metadata' => [
                        'accountManager' => 'Jane Smith',
                        'annualRevenue' => 500000.00
                    ]
                ]
            ];
        }

        if ($type === 'product') {
            return [
                'product' => [
                    'info' => [
                        'id' => $id,
                        'name' => 'Partner Product'
                    ],
                    'pricing' => [
                        'basePrice' => 15000.00,
                        'discount' => 0.10
                    ]
                ]
            ];
        }

        if ($type === 'risk') {
            return [
                'assessment' => [
                    'subject' => ['customerId' => $id],
                    'evaluation' => [
                        'score' => 78.5,
                        'level' => 'medium-low'
                    ]
                ]
            ];
        }

        return null;
    }

    public function getName(): string
    {
        return 'partner_api';
    }

    public function getPriority(): int
    {
        return 15;
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
