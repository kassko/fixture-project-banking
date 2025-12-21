<?php

declare(strict_types=1);

namespace App\FeatureFlag\Strategy;

use App\FeatureFlag\FeatureFlagContext;

class PercentageRolloutStrategy implements StrategyInterface
{
    public function isEnabled(array $config, FeatureFlagContext $context): bool
    {
        $percentage = $config['percentage'] ?? 0;
        
        if ($percentage >= 100) {
            return true;
        }
        
        if ($percentage <= 0) {
            return false;
        }
        
        // Use user ID for consistent rollout
        $userId = $context->getUserId();
        if ($userId === null) {
            return false;
        }
        
        // Hash-based distribution
        $hash = crc32((string) $userId);
        return ($hash % 100) < $percentage;
    }
}
