<?php

declare(strict_types=1);

namespace App\Context;

class UnifiedContext
{
    public function __construct(
        public readonly TenantContext $tenant,
        public readonly BrandContext $brand,
        public readonly UserContext $user,
        public readonly SessionContext $session,
        public readonly TemporalContext $temporal,
        public readonly CampaignContext $campaign,
        public readonly ?object $features = null
    ) {
    }

    // Backward compatibility methods
    public function getTenantContext(): TenantContext
    {
        return $this->tenant;
    }

    public function getBrandContext(): BrandContext
    {
        return $this->brand;
    }

    public function getUserContext(): UserContext
    {
        return $this->user;
    }

    public function getSessionContext(): SessionContext
    {
        return $this->session;
    }

    public function getTemporalContext(): TemporalContext
    {
        return $this->temporal;
    }

    public function getCampaignContext(): CampaignContext
    {
        return $this->campaign;
    }
}
