<?php

declare(strict_types=1);

namespace App\FeatureFlag\Strategy;

use App\FeatureFlag\FeatureFlagContext;

class BrandStrategy implements StrategyInterface
{
    public function isEnabled(array $config, FeatureFlagContext $context): bool
    {
        $allowedBrands = $config['allowed_brands'] ?? [];
        $brandId = $context->getBrandId();
        
        if (empty($allowedBrands)) {
            return true;
        }
        
        return in_array($brandId, $allowedBrands, true);
    }
}
