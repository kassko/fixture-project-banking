<?php

declare(strict_types=1);

namespace App\FeatureFlag\Strategy;

use App\FeatureFlag\FeatureFlagContext;

class BooleanStrategy implements StrategyInterface
{
    public function isEnabled(array $config, FeatureFlagContext $context): bool
    {
        return $config['enabled'] ?? false;
    }
}
