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
        
        // Simulate income data - in real app would come from customer data
        $customerIncome = 35000; // Default simulated income
        
        if ($customerIncome < $minIncome) {
            return RuleResult::fail("Insufficient income (required: {$minIncome}, current: {$customerIncome})");
        }
        
        return RuleResult::pass();
    }
}
