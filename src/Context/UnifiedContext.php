<?php

declare(strict_types=1);

namespace App\Context;

class UnifiedContext
{
    public function __construct(
        private TenantContext $tenantContext,
        private BrandContext $brandContext,
        private UserContext $userContext,
        private SessionContext $sessionContext,
        private TemporalContext $temporalContext,
        private CampaignContext $campaignContext
    ) {
    }

    public function getTenantContext(): TenantContext
    {
        return $this->tenantContext;
    }

    public function getBrandContext(): BrandContext
    {
        return $this->brandContext;
    }

    public function getUserContext(): UserContext
    {
        return $this->userContext;
    }

    public function getSessionContext(): SessionContext
    {
        return $this->sessionContext;
    }

    public function getTemporalContext(): TemporalContext
    {
        return $this->temporalContext;
    }

    public function getCampaignContext(): CampaignContext
    {
        return $this->campaignContext;
    }
}
