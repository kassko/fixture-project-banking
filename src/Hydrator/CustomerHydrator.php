<?php

namespace App\Hydrator;

use App\Hydrator\Contract\HydratorInterface;
use App\Model\Customer\Customer;
use App\Model\Customer\PremiumCustomer;
use App\Model\Customer\CorporateCustomer;
use App\Model\Common\Address;
use DateTime;

class CustomerHydrator implements HydratorInterface
{
    public function hydrate(array $data, ?object $object = null): object
    {
        // Flatten nested data structures
        $flatData = $this->flattenData($data);
        
        // Determine customer type
        $customerType = $flatData['customerType'] ?? $flatData['type'] ?? 'standard';
        
        if ($object === null) {
            $object = match($customerType) {
                'premium' => new PremiumCustomer(),
                'corporate' => new CorporateCustomer(),
                default => new Customer(),
            };
        }
        
        // Hydrate common fields
        if (isset($flatData['id'])) {
            $object->setId((int) $flatData['id']);
        }
        
        if (isset($flatData['name'])) {
            $object->setName($flatData['name']);
        }
        
        if (isset($flatData['email'])) {
            $object->setEmail($flatData['email']);
        }
        
        if (isset($flatData['phone'])) {
            $object->setPhone($flatData['phone']);
        }
        
        // Hydrate timestamps
        if (isset($flatData['createdAt'])) {
            $object->setCreatedAt($this->parseDateTime($flatData['createdAt']));
        }
        
        if (isset($flatData['updatedAt'])) {
            $object->setUpdatedAt($this->parseDateTime($flatData['updatedAt']));
        }
        
        // Hydrate audit fields
        if (isset($flatData['createdBy'])) {
            $object->setCreatedBy($flatData['createdBy']);
        }
        
        if (isset($flatData['updatedBy'])) {
            $object->setUpdatedBy($flatData['updatedBy']);
        }
        
        // Hydrate address if present
        if (isset($flatData['street']) || isset($flatData['city'])) {
            $address = new Address();
            if (isset($flatData['street'])) $address->setStreet($flatData['street']);
            if (isset($flatData['city'])) $address->setCity($flatData['city']);
            if (isset($flatData['postalCode'])) $address->setPostalCode($flatData['postalCode']);
            if (isset($flatData['country'])) $address->setCountry($flatData['country']);
            $object->setAddress($address);
        }
        
        // Hydrate type-specific fields
        if ($object instanceof PremiumCustomer) {
            if (isset($flatData['annualRevenue'])) {
                $object->setAnnualRevenue((float) $flatData['annualRevenue']);
            }
            if (isset($flatData['loyaltyPoints'])) {
                $object->setLoyaltyPoints((int) $flatData['loyaltyPoints']);
            }
            if (isset($flatData['accountManager'])) {
                $object->setAccountManager($flatData['accountManager']);
            }
        }
        
        if ($object instanceof CorporateCustomer) {
            if (isset($flatData['companyName'])) {
                $object->setCompanyName($flatData['companyName']);
            }
            if (isset($flatData['registrationNumber'])) {
                $object->setRegistrationNumber($flatData['registrationNumber']);
            }
            if (isset($flatData['employeeCount'])) {
                $object->setEmployeeCount((int) $flatData['employeeCount']);
            }
            if (isset($flatData['industry'])) {
                $object->setIndustry($flatData['industry']);
            }
        }
        
        return $object;
    }

    public function extract(object $object): array
    {
        if (!$object instanceof Customer) {
            throw new \InvalidArgumentException('Object must be instance of Customer');
        }
        
        return $object->toArray();
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
