<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class AgeRule implements EligibilityRuleInterface
{
    public function getName(): string
    {
        return 'AGE_REQUIREMENT';
    }

    public function supports(array $product): bool
    {
        return isset($product['age_requirements']);
    }

    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult
    {
        $requirements = $product['age_requirements'];
        $birthDate = $context->customer->getBirthDate();
        
        if (!$birthDate) {
            return RuleResult::fail('Birth date not provided');
        }

        $age = $birthDate->diff(new \DateTimeImmutable())->y;
        
        if (isset($requirements['min_age']) && $age < $requirements['min_age']) {
            return RuleResult::fail("Minimum age requirement not met (required: {$requirements['min_age']}, current: {$age})");
        }
        
        if (isset($requirements['max_age']) && $age > $requirements['max_age']) {
            return RuleResult::fail("Maximum age exceeded (max: {$requirements['max_age']}, current: {$age})");
        }
        
        return RuleResult::pass();
    }
}
