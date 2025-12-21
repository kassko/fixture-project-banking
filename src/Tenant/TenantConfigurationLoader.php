<?php

declare(strict_types=1);

namespace App\Tenant;

use Symfony\Component\Yaml\Yaml;

class TenantConfigurationLoader
{
    private array $configurations = [];

    public function __construct(
        private string $configPath
    ) {
    }

    public function load(string $tenantId): TenantConfiguration
    {
        if (isset($this->configurations[$tenantId])) {
            return $this->configurations[$tenantId];
        }

        $filePath = $this->configPath . '/tenant_' . $tenantId . '.yaml';
        
        if (!file_exists($filePath)) {
            // Return default configuration
            $config = $this->getDefaultConfig($tenantId);
        } else {
            $config = Yaml::parseFile($filePath);
        }

        $this->configurations[$tenantId] = new TenantConfiguration($tenantId, $config);
        
        return $this->configurations[$tenantId];
    }

    private function getDefaultConfig(string $tenantId): array
    {
        return [
            'tenant_id' => $tenantId,
            'name' => 'Default Tenant',
            'loan_settings' => [
                'min_amount' => 1000,
                'max_amount' => 500000,
                'min_duration' => 12,
                'max_duration' => 360,
                'base_rate' => 3.5,
            ],
            'insurance_settings' => [
                'available_types' => ['HOME', 'AUTO', 'LIFE'],
                'base_premium_rate' => 0.005,
            ],
        ];
    }
}
