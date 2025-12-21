<?php

declare(strict_types=1);

namespace App\FeatureFlag\Strategy;

use App\FeatureFlag\FeatureFlagContext;

interface StrategyInterface
{
    public function isEnabled(array $config, FeatureFlagContext $context): bool;
}
