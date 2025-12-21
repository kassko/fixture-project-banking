<?php

declare(strict_types=1);

namespace App\Tenant;

class TenantConfiguration
{
    public function __construct(
        private string $tenantId,
        private array $config
    ) {
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }
        
        return $value;
    }

    public function getAvailableProducts(): array
    {
        return $this->get('available_products', []);
    }

    public function getCountry(): string
    {
        return $this->get('country', 'FR');
    }

    public function getKycProvider(): string
    {
        return $this->get('kyc_provider', 'default');
    }
}
