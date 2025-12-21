<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class GeoRestrictionRule implements EligibilityRuleInterface
{
    public function getName(): string
    {
        return 'GEO_RESTRICTION';
    }

    public function supports(array $product): bool
    {
        return isset($product['allowed_countries']) || isset($product['restricted_countries']);
    }

    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult
    {
        $country = $context->tenantConfig->getCountry() ?? 'FR';
        
        if (isset($product['allowed_countries'])) {
            if (!in_array($country, $product['allowed_countries'])) {
                return RuleResult::fail("Product not available in country: {$country}");
            }
        }
        
        if (isset($product['restricted_countries'])) {
            if (in_array($country, $product['restricted_countries'])) {
                return RuleResult::fail("Product restricted in country: {$country}");
            }
        }
        
        return RuleResult::pass();
    }
}
