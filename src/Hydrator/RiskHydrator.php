<?php

namespace App\Hydrator;

use App\Hydrator\Contract\HydratorInterface;
use App\Model\Risk\RiskProfile;
use App\Model\Risk\RiskScore;
use App\Model\Risk\RiskAssessment;
use DateTime;

class RiskHydrator implements HydratorInterface
{
    public function hydrate(array $data, ?object $object = null): object
    {
        // Flatten nested data structures
        $flatData = $this->flattenData($data);
        
        // Determine what type of risk object to create
        if ($object === null) {
            if (isset($flatData['scoreType']) || isset($data['scoreType'])) {
                $object = new RiskScore();
            } elseif (isset($flatData['recommendation']) || isset($data['recommendation'])) {
                $object = new RiskAssessment();
            } else {
                $object = new RiskProfile();
            }
        }
        
        // Hydrate common fields
        if (isset($flatData['id'])) {
            $object->setId((int) $flatData['id']);
        }
        
        if (isset($flatData['customerId'])) {
            $object->setCustomerId((int) $flatData['customerId']);
        }
        
        // Hydrate timestamps
        if (isset($flatData['createdAt'])) {
            $object->setCreatedAt($this->parseDateTime($flatData['createdAt']));
        }
        
        if (isset($flatData['updatedAt'])) {
            $object->setUpdatedAt($this->parseDateTime($flatData['updatedAt']));
        }
        
        // Hydrate type-specific fields
        if ($object instanceof RiskProfile) {
            if (isset($flatData['riskLevel']) || isset($flatData['level'])) {
                $object->setRiskLevel($flatData['riskLevel'] ?? $flatData['level']);
            }
            
            if (isset($flatData['riskScore']) || isset($flatData['score'])) {
                $object->setRiskScore((float) ($flatData['riskScore'] ?? $flatData['score']));
            }
            
            // Handle risk factors (can be array)
            if (isset($data['factors']) && is_array($data['factors'])) {
                $object->setRiskFactors($data['factors']);
            } elseif (isset($data['riskFactors']) && is_array($data['riskFactors'])) {
                $object->setRiskFactors($data['riskFactors']);
            }
        }
        
        if ($object instanceof RiskScore) {
            if (isset($flatData['scoreType']) || isset($flatData['type'])) {
                $object->setScoreType($flatData['scoreType'] ?? $flatData['type']);
            }
            
            if (isset($flatData['value'])) {
                $object->setValue((int) $flatData['value']);
            }
            
            if (isset($flatData['source'])) {
                $object->setSource($flatData['source']);
            }
        }
        
        if ($object instanceof RiskAssessment) {
            if (isset($flatData['recommendation'])) {
                $object->setRecommendation($flatData['recommendation']);
            }
            
            if (isset($flatData['approved'])) {
                $object->setApproved((bool) $flatData['approved']);
            }
            
            // Hydrate audit fields
            if (isset($flatData['createdBy'])) {
                $object->setCreatedBy($flatData['createdBy']);
            }
            
            if (isset($flatData['updatedBy'])) {
                $object->setUpdatedBy($flatData['updatedBy']);
            }
            
            // Handle scores array
            if (isset($data['scores']) && is_array($data['scores'])) {
                foreach ($data['scores'] as $scoreData) {
                    $score = new RiskScore();
                    if (isset($scoreData['type'])) $score->setScoreType($scoreData['type']);
                    if (isset($scoreData['value'])) $score->setValue((int) $scoreData['value']);
                    if (isset($scoreData['source'])) $score->setSource($scoreData['source']);
                    $object->addScore($score);
                }
            }
        }
        
        return $object;
    }

    public function extract(object $object): array
    {
        if ($object instanceof RiskScore) {
            return [
                'id' => $object->getId(),
                'customerId' => $object->getCustomerId(),
                'scoreType' => $object->getScoreType(),
                'value' => $object->getValue(),
                'source' => $object->getSource(),
            ];
        }
        
        if ($object instanceof RiskAssessment) {
            return $object->toArray();
        }
        
        if ($object instanceof RiskProfile) {
            return $object->toArray();
        }
        
        throw new \InvalidArgumentException('Object must be instance of RiskProfile, RiskScore, or RiskAssessment');
    }

    private function flattenData(array $data, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($data as $key => $value) {
            $newKey = $prefix ? $prefix . '.' . $key : $key;
            
            if (is_array($value) && !$this->isAssociativeArray($value)) {
                // Skip numeric arrays (lists) - handle them separately
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
