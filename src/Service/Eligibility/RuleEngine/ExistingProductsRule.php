<?php

declare(strict_types=1);

namespace App\Service\Eligibility\RuleEngine;

class ExistingProductsRule implements EligibilityRuleInterface
{
    public function getName(): string
    {
        return 'EXISTING_PRODUCTS';
    }

    public function supports(array $product): bool
    {
        return isset($product['incompatible_with']);
    }

    public function evaluate(array $product, RuleEvaluationContext $context): RuleResult
    {
        $incompatibleProducts = $product['incompatible_with'];
        $existingProducts = $context->existingProducts;
        
        foreach ($existingProducts as $existingProduct) {
            $productType = method_exists($existingProduct, 'getProductType') 
                ? $existingProduct->getProductType() 
                : 'UNKNOWN';
            
            if (in_array($productType, $incompatibleProducts)) {
                return RuleResult::fail("Incompatible with existing product: {$productType}");
            }
        }
        
        return RuleResult::pass();
    }
}
