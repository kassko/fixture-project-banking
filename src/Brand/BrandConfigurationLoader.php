<?php

declare(strict_types=1);

namespace App\Brand;

use Symfony\Component\Yaml\Yaml;

class BrandConfigurationLoader
{
    private array $configurations = [];

    public function __construct(
        private string $configPath
    ) {
    }

    public function load(string $brandId): BrandConfiguration
    {
        if (isset($this->configurations[$brandId])) {
            return $this->configurations[$brandId];
        }

        $filePath = $this->configPath . '/brand_' . $brandId . '.yaml';
        
        if (!file_exists($filePath)) {
            // Return default configuration
            $config = $this->getDefaultConfig($brandId);
        } else {
            $config = Yaml::parseFile($filePath);
        }

        $this->configurations[$brandId] = new BrandConfiguration($brandId, $config);
        
        return $this->configurations[$brandId];
    }

    private function getDefaultConfig(string $brandId): array
    {
        return [
            'brand_id' => $brandId,
            'name' => 'Default Brand',
            'type' => 'standard',
            'rate_adjustment' => 0.0,
            'fee_multiplier' => 1.0,
            'service_quality' => 'standard',
        ];
    }
}
