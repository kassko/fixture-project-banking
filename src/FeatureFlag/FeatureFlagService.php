<?php

declare(strict_types=1);

namespace App\FeatureFlag;

use App\FeatureFlag\Strategy\BooleanStrategy;
use App\FeatureFlag\Strategy\PercentageRolloutStrategy;
use App\FeatureFlag\Strategy\TenantStrategy;
use App\FeatureFlag\Strategy\BrandStrategy;
use App\FeatureFlag\Strategy\DateRangeStrategy;
use Symfony\Component\Yaml\Yaml;

class FeatureFlagService
{
    private array $features = [];
    private array $strategies = [];

    public function __construct(string $configPath)
    {
        $this->initializeStrategies();
        $this->loadFeatures($configPath);
    }

    private function initializeStrategies(): void
    {
        $this->strategies = [
            'boolean' => new BooleanStrategy(),
            'percentage_rollout' => new PercentageRolloutStrategy(),
            'tenant' => new TenantStrategy(),
            'brand' => new BrandStrategy(),
            'date_range' => new DateRangeStrategy(),
        ];
    }

    private function loadFeatures(string $configPath): void
    {
        $filePath = $configPath . '/features.yaml';
        
        if (file_exists($filePath)) {
            $data = Yaml::parseFile($filePath);
            $this->features = $data['features'] ?? [];
        }
    }

    public function isEnabled(string $featureName, FeatureFlagContext $context): bool
    {
        if (!isset($this->features[$featureName])) {
            return false;
        }

        $feature = $this->features[$featureName];
        $strategyName = $feature['strategy'] ?? 'boolean';
        
        if (!isset($this->strategies[$strategyName])) {
            return false;
        }

        return $this->strategies[$strategyName]->isEnabled($feature, $context);
    }
}
