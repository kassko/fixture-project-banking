<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class KycStatusRule implements EligibilityRuleInterface
{
    public function getName(): string
    {
        return 'KYC_STATUS';
    }

    public function supports(array $product): bool
    {
        return isset($product['requires_kyc']) && $product['requires_kyc'] === true;
    }

    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult
    {
        $kycStatus = $context->kycData['kyc_status'] ?? 'UNKNOWN';
        
        if ($kycStatus !== 'VERIFIED') {
            return RuleResult::fail("KYC verification required (current status: {$kycStatus})");
        }
        
        return RuleResult::pass();
    }
}
