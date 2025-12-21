<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

use App\Brand\BrandConfiguration;
use App\Context\CampaignContext;
use App\Context\TemporalContext;
use App\Entity\Customer;
use App\Tenant\TenantConfiguration;

class RuleEvaluationContext
{
    public function __construct(
        public readonly Customer $customer,
        public readonly array $kycData,
        public readonly array $amlData,
        public readonly array $creditRating,
        public readonly array $existingProducts,
        public readonly TenantConfiguration $tenantConfig,
        public readonly BrandConfiguration $brandConfig,
        public readonly TemporalContext $temporalContext,
        public readonly CampaignContext $campaignContext
    ) {
    }
}
