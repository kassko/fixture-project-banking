<?php

namespace App\Hydrator;

use App\Hydrator\Contract\HydratorInterface;
use App\Model\Product\Product;
use App\Model\Product\LoanProduct;
use App\Model\Product\InsuranceProduct;
use DateTime;

class ProductHydrator implements HydratorInterface
{
    public function hydrate(array $data, ?object $object = null): object
    {
        // Flatten nested data structures
        $flatData = $this->flattenData($data);
        
        // Determine product type
        $productType = $flatData['productType'] ?? $flatData['type'] ?? 'generic';
        
        if ($object === null) {
            $object = match($productType) {
                'loan' => new LoanProduct(),
                'insurance' => new InsuranceProduct(),
                default => new Product(),
            };
        }
        
        // Hydrate common fields
        if (isset($flatData['id'])) {
            $object->setId((int) $flatData['id']);
        }
        
        if (isset($flatData['name'])) {
            $object->setName($flatData['name']);
        }
        
        if (isset($flatData['description'])) {
            $object->setDescription($flatData['description']);
        }
        
        if (isset($flatData['price'])) {
            $object->setPrice((float) $flatData['price']);
        }
        
        if (isset($flatData['basePrice'])) {
            $object->setPrice((float) $flatData['basePrice']);
        }
        
        if (isset($flatData['isActive'])) {
            $object->setIsActive((bool) $flatData['isActive']);
        }
        
        // Hydrate timestamps
        if (isset($flatData['createdAt'])) {
            $object->setCreatedAt($this->parseDateTime($flatData['createdAt']));
        }
        
        if (isset($flatData['updatedAt'])) {
            $object->setUpdatedAt($this->parseDateTime($flatData['updatedAt']));
        }
        
        // Hydrate type-specific fields
        if ($object instanceof LoanProduct) {
            if (isset($flatData['interestRate'])) {
                $object->setInterestRate((float) $flatData['interestRate']);
            }
            if (isset($flatData['termMonths'])) {
                $object->setTermMonths((int) $flatData['termMonths']);
            }
            if (isset($flatData['maxAmount'])) {
                $object->setMaxAmount((float) $flatData['maxAmount']);
            }
            if (isset($flatData['minAmount'])) {
                $object->setMinAmount((float) $flatData['minAmount']);
            }
        }
        
        if ($object instanceof InsuranceProduct) {
            if (isset($flatData['premium'])) {
                $object->setPremium((float) $flatData['premium']);
            }
            if (isset($flatData['coverageAmount'])) {
                $object->setCoverageAmount((float) $flatData['coverageAmount']);
            }
            if (isset($flatData['coverageType'])) {
                $object->setCoverageType($flatData['coverageType']);
            }
            if (isset($flatData['policyTermYears'])) {
                $object->setPolicyTermYears((int) $flatData['policyTermYears']);
            }
        }
        
        return $object;
    }

    public function extract(object $object): array
    {
        if (!$object instanceof Product) {
            throw new \InvalidArgumentException('Object must be instance of Product');
        }
        
        $data = [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'description' => $object->getDescription(),
            'price' => $object->getPrice(),
            'productType' => $object->getProductType(),
            'isActive' => $object->isActive(),
        ];
        
        if ($object instanceof LoanProduct) {
            $data['interestRate'] = $object->getInterestRate();
            $data['termMonths'] = $object->getTermMonths();
            $data['maxAmount'] = $object->getMaxAmount();
            $data['minAmount'] = $object->getMinAmount();
        }
        
        if ($object instanceof InsuranceProduct) {
            $data['premium'] = $object->getPremium();
            $data['coverageAmount'] = $object->getCoverageAmount();
            $data['coverageType'] = $object->getCoverageType();
            $data['policyTermYears'] = $object->getPolicyTermYears();
        }
        
        return $data;
    }

    private function flattenData(array $data, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value) && !$this->isAssociativeArray($value)) {
                // Skip numeric arrays (lists)
                continue;
            } elseif (is_array($value)) {
                // Recursively flatten nested arrays
                $flattened = $this->flattenData($value, $newKey);
                foreach ($flattened as $flatKey => $flatValue) {
                    // Use the last part of the key if it doesn't conflict
                    $simplifiedKey = substr($flatKey, strrpos($flatKey, '.') + 1);
                    if (!isset($result[$simplifiedKey])) {
                        $result[$simplifiedKey] = $flatValue;
                    }
                    $result[$flatKey] = $flatValue;
                }
            } else {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

    private function isAssociativeArray(array $arr): bool
    {
        if (empty($arr)) return true;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    private function parseDateTime($value): ?DateTime
    {
        if ($value instanceof DateTime) {
            return $value;
        }
        
        if (is_string($value)) {
            try {
                return new DateTime($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }
}
