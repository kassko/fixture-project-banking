<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

use App\Entity\Customer;

class RuleEvaluationContext
{
    public function __construct(
        public readonly Customer $customer,
        public readonly array $kycData,
        public readonly array $amlData,
        public readonly array $creditRating,
        public readonly $existingProducts,
        public readonly $tenantConfig,
        public readonly $brandConfig,
        public readonly $temporalContext,
        public readonly $campaignContext
    ) {
    }
}
