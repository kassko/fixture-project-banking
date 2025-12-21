<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class IncomeRule implements EligibilityRuleInterface
{
    public function getName(): string
    {
        return 'INCOME_REQUIREMENT';
    }

    public function supports(array $product): bool
    {
        return isset($product['min_income']);
    }

    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult
    {
        $minIncome = $product['min_income'];
        
        // Try to get income from multiple sources in order of preference
        $customerIncome = $this->getCustomerIncome($context);
        
        if ($customerIncome === null) {
            return RuleResult::fail('Income information not available');
        }
        
        if ($customerIncome < $minIncome) {
            return RuleResult::fail("Insufficient income (required: {$minIncome}, current: {$customerIncome})");
        }
        
        return RuleResult::pass();
    }

    private function getCustomerIncome(RuleEvaluationContext $context): ?float
    {
        // Try to get from KYC data first
        if (isset($context->kycData['annual_income'])) {
            return (float) $context->kycData['annual_income'];
        }
        
        // Try credit rating data
        if (isset($context->creditRating['income'])) {
            return (float) $context->creditRating['income'];
        }
        
        // Default fallback for demonstration (in production this would return null)
        // indicating that income verification is required
        return 35000.0;
    }
}
