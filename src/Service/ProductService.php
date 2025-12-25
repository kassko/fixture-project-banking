<?php

namespace App\Service;

use App\Model\Product\Product;
use App\Hydrator\ProductHydrator;
use App\Resolver\SourceResolver;
use App\Resolver\FallbackResolver;
use App\Resolver\ConflictResolver;
use App\Context\UserContext;

class ProductService
{
    private ProductHydrator $hydrator;
    private SourceResolver $sourceResolver;
    private FallbackResolver $fallbackResolver;
    private ConflictResolver $conflictResolver;

    public function __construct(
        ProductHydrator $hydrator,
        SourceResolver $sourceResolver,
        FallbackResolver $fallbackResolver,
        ConflictResolver $conflictResolver
    ) {
        $this->hydrator = $hydrator;
        $this->sourceResolver = $sourceResolver;
        $this->fallbackResolver = $fallbackResolver;
        $this->conflictResolver = $conflictResolver;
    }

    public function getProduct(int $id, ?UserContext $userContext = null): ?Product
    {
        $sources = $this->sourceResolver->resolve('product', $userContext);
        
        if (empty($sources)) {
            return null;
        }

        $data = $this->fallbackResolver->fetchWithFallback($sources, 'product', $id);
        
        if ($data === null) {
            return null;
        }

        return $this->hydrator->hydrate($data);
    }

    public function getEligibleProducts(int $customerId, ?UserContext $userContext = null): array
    {
        // Simulate product eligibility logic
        // In real scenario, would check customer profile, credit score, etc.
        
        $products = [];
        for ($i = 1; $i <= 3; $i++) {
            $product = $this->getProduct($i, $userContext);
            if ($product !== null) {
                $products[] = $product;
            }
        }

        return $products;
    }

    public function getPricing(
        int $id,
        ?UserContext $userContext = null,
        string $conflictStrategy = 'average'
    ): ?array {
        // Get pricing from multiple sources
        $sources = $this->sourceResolver->resolve('product', $userContext);
        
        $pricingData = [];
        foreach ($sources as $source) {
            $data = $source->fetchData('product', $id);
            if ($data !== null) {
                $pricingData[$source->getName()] = $data;
            }
        }

        if (empty($pricingData)) {
            return null;
        }

        // Resolve conflicts in pricing
        $resolvedData = $this->conflictResolver->resolve($pricingData, $conflictStrategy);
        
        return $resolvedData;
    }

    public function compareProducts(
        array $productIds,
        ?UserContext $userContext = null
    ): array {
        $comparison = [];
        
        foreach ($productIds as $id) {
            // Get from multiple sources and merge
            $sources = $this->sourceResolver->resolve('product', $userContext);
            
            $productData = [];
            foreach ($sources as $source) {
                $data = $source->fetchData('product', $id);
                if ($data !== null) {
                    $productData[$source->getName()] = $data;
                }
            }

            if (!empty($productData)) {
                // Merge data from all sources
                $merged = $this->conflictResolver->resolve($productData, 'merge');
                $comparison[$id] = $merged;
            }
        }

        return $comparison;
    }
}
