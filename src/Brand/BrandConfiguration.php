<?php

declare(strict_types=1);

namespace App\Brand;

class BrandConfiguration
{
    public function __construct(
        private string $brandId,
        private array $config
    ) {
    }

    public function getBrandId(): string
    {
        return $this->brandId;
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

    public function getIncludedProducts(): array
    {
        return $this->get('included_products', []);
    }

    public function getSegment(): string
    {
        return $this->get('type', 'standard');
    }

    public function getWelcomeOffer(): ?array
    {
        return $this->get('welcome_offer');
    }
}
