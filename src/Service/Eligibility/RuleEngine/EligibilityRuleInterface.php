<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

interface EligibilityRuleInterface
{
    /**
     * Get the name of this rule.
     */
    public function getName(): string;

    /**
     * Check if this rule supports the given product.
     */
    public function supports(array $product): bool;

    /**
     * Evaluate the rule for a product.
     */
    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult;
}
