<?php

declare(strict_types=1);

namespace App\FeatureFlag\Strategy;

use App\FeatureFlag\FeatureFlagContext;

class TenantStrategy implements StrategyInterface
{
    public function isEnabled(array $config, FeatureFlagContext $context): bool
    {
        $allowedTenants = $config['allowed_tenants'] ?? [];
        $tenantId = $context->getTenantId();
        
        if (empty($allowedTenants)) {
            return true;
        }
        
        return in_array($tenantId, $allowedTenants, true);
    }
}
