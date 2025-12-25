<?php

namespace App\FeatureFlag;

use App\Context\FeatureFlagContext;
use App\Context\UserContext;

class FeatureFlagService
{
    private FeatureFlagProvider $provider;

    public function __construct(FeatureFlagProvider $provider)
    {
        $this->provider = $provider;
    }

    public function createContext(?UserContext $userContext = null): FeatureFlagContext
    {
        $flags = $this->provider->getAllFlags();
        
        // Apply user-specific flag overrides if needed
        if ($userContext !== null) {
            // Example: Admins might have all features enabled
            if ($userContext->isAdmin()) {
                // Keep flags as configured
            }
        }
        
        return new FeatureFlagContext($flags);
    }

    public function isEnabled(string $flagName, ?UserContext $userContext = null): bool
    {
        $context = $this->createContext($userContext);
        return $context->isEnabled($flagName);
    }

    public function getProvider(): FeatureFlagProvider
    {
        return $this->provider;
    }
}
