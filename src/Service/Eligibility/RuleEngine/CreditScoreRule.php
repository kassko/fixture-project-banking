<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class CreditScoreRule implements EligibilityRuleInterface
{
    public function getName(): string
    {
        return 'CREDIT_SCORE';
    }

    public function supports(array $product): bool
    {
        return isset($product['min_credit_score']);
    }

    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult
    {
        $minScore = $product['min_credit_score'];
        $currentScore = $context->creditRating['score'] ?? 0;
        
        if ($currentScore < $minScore) {
            return RuleResult::fail("Insufficient credit score (required: {$minScore}, current: {$currentScore})");
        }
        
        return RuleResult::pass();
    }
}
