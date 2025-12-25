<?php

namespace App\Service;

use App\Model\Customer\Customer;
use App\Hydrator\CustomerHydrator;
use App\Resolver\SourceResolver;
use App\Resolver\FallbackResolver;
use App\Resolver\DataMaskingResolver;
use App\Context\UserContext;
use App\Context\FeatureFlagContext;

class CustomerService
{
    private CustomerHydrator $hydrator;
    private SourceResolver $sourceResolver;
    private FallbackResolver $fallbackResolver;
    private DataMaskingResolver $maskingResolver;

    public function __construct(
        CustomerHydrator $hydrator,
        SourceResolver $sourceResolver,
        FallbackResolver $fallbackResolver,
        DataMaskingResolver $maskingResolver
    ) {
        $this->hydrator = $hydrator;
        $this->sourceResolver = $sourceResolver;
        $this->fallbackResolver = $fallbackResolver;
        $this->maskingResolver = $maskingResolver;
    }

    public function getCustomer(
        int $id,
        ?UserContext $userContext = null,
        ?FeatureFlagContext $featureFlagContext = null
    ): ?Customer {
        // Get primary source
        $sources = $this->sourceResolver->resolve('customer', $userContext);
        
        if (empty($sources)) {
            return null;
        }

        // Fetch data with fallback
        $data = $this->fallbackResolver->fetchWithFallback($sources, 'customer', $id);
        
        if ($data === null) {
            return null;
        }

        // Apply masking if contexts provided
        if ($userContext && $featureFlagContext) {
            $data = $this->maskingResolver->maskData($data, $userContext, $featureFlagContext, 'customer');
        }

        // Hydrate to object
        return $this->hydrator->hydrate($data);
    }

    public function getCustomerFull(
        int $id,
        ?UserContext $userContext = null,
        ?FeatureFlagContext $featureFlagContext = null
    ): array {
        // Get all available sources
        $sources = $this->sourceResolver->resolve('customer', $userContext, ['multi' => true]);
        
        $allData = [];
        foreach ($sources as $source) {
            $data = $source->fetchData('customer', $id);
            if ($data !== null) {
                $allData[$source->getName()] = $data;
            }
        }

        return $allData;
    }

    public function getCustomerProfile(
        int $id,
        UserContext $userContext,
        FeatureFlagContext $featureFlagContext
    ): ?array {
        $customer = $this->getCustomer($id, $userContext, $featureFlagContext);
        
        if ($customer === null) {
            return null;
        }

        return $this->hydrator->extract($customer);
    }
}
