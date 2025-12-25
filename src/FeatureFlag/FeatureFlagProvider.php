<?php

namespace App\FeatureFlag;

use Symfony\Component\Yaml\Yaml;

class FeatureFlagProvider
{
    private array $flags = [];

    public function __construct(string $configPath)
    {
        $this->loadFlags($configPath);
    }

    private function loadFlags(string $configPath): void
    {
        if (!file_exists($configPath)) {
            return;
        }

        $config = Yaml::parseFile($configPath);
        
        if (isset($config['feature_flags'])) {
            foreach ($config['feature_flags'] as $name => $settings) {
                $this->flags[$name] = $settings['enabled'] ?? false;
            }
        }
    }

    public function isEnabled(string $flagName): bool
    {
        return $this->flags[$flagName] ?? false;
    }

    public function getAllFlags(): array
    {
        return $this->flags;
    }

    public function setFlag(string $flagName, bool $enabled): void
    {
        $this->flags[$flagName] = $enabled;
    }
}
