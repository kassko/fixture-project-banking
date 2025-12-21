<?php

declare(strict_types=1);

namespace App\DataSource;

/**
 * Legacy policy data source returning array data.
 */
class LegacyPolicyDataSource
{
    /**
     * Get policy data by policy number.
     */
    public function getPolicyData(string $policyNumber): array
    {
        $policies = [
            'INS-2024-001' => [
                'policy_number' => 'INS-2024-001',
                'customer_id' => 'CUST001',
                'type' => 'life',
                'status' => 'active',
                'effective_date' => '2024-01-01',
                'expiration_date' => '2025-01-01',
                'premium' => [
                    'amount' => 1200.00,
                    'currency' => 'EUR',
                    'frequency' => 'annual',
                ],
                'coverage' => [
                    [
                        'type' => 'death_benefit',
                        'limit' => 500000.00,
                        'currency' => 'EUR',
                        'deductible' => [
                            'amount' => 0.00,
                            'type' => 'none',
                        ],
                        'exclusions' => ['suicide_first_year', 'war'],
                    ],
                    [
                        'type' => 'disability',
                        'limit' => 250000.00,
                        'currency' => 'EUR',
                        'deductible' => [
                            'amount' => 1000.00,
                            'type' => 'per_claim',
                        ],
                        'exclusions' => ['pre_existing_conditions'],
                    ],
                ],
                'beneficiaries' => [
                    [
                        'first_name' => 'Sophie',
                        'last_name' => 'Dupont',
                        'relationship' => 'spouse',
                        'percentage' => 60.00,
                        'is_primary' => true,
                    ],
                    [
                        'first_name' => 'Lucas',
                        'last_name' => 'Dupont',
                        'relationship' => 'child',
                        'percentage' => 40.00,
                        'is_primary' => false,
                    ],
                ],
                'underwriting' => [
                    'medical_exam_date' => '2023-12-15',
                    'health_score' => 90,
                    'occupation_risk' => 'low',
                    'lifestyle_factors' => [
                        'smoker' => false,
                        'alcohol_consumption' => 'moderate',
                        'exercise_frequency' => 'regular',
                    ],
                ],
            ],
            'INS-2024-002' => [
                'policy_number' => 'INS-2024-002',
                'customer_id' => 'CUST002',
                'type' => 'home',
                'status' => 'active',
                'effective_date' => '2024-02-01',
                'expiration_date' => '2025-02-01',
                'premium' => [
                    'amount' => 800.00,
                    'currency' => 'EUR',
                    'frequency' => 'annual',
                ],
                'coverage' => [
                    [
                        'type' => 'dwelling',
                        'limit' => 350000.00,
                        'currency' => 'EUR',
                        'deductible' => [
                            'amount' => 500.00,
                            'type' => 'per_claim',
                        ],
                        'exclusions' => ['flood', 'earthquake'],
                    ],
                    [
                        'type' => 'personal_property',
                        'limit' => 100000.00,
                        'currency' => 'EUR',
                        'deductible' => [
                            'amount' => 250.00,
                            'type' => 'per_claim',
                        ],
                        'exclusions' => ['wear_and_tear'],
                    ],
                ],
                'property_details' => [
                    'address' => '456 Avenue des Champs, Lyon 69001',
                    'year_built' => 2015,
                    'construction_type' => 'masonry',
                    'square_meters' => 120,
                    'security_features' => ['alarm', 'deadbolt'],
                ],
            ],
        ];
        
        return $policies[$policyNumber] ?? throw new \InvalidArgumentException("Policy not found: $policyNumber");
    }
    
    /**
     * Get all policies for a customer.
     */
    public function getPoliciesByCustomer(string $customerId): array
    {
        $allPolicies = [
            'INS-2024-001' => $this->getPolicyData('INS-2024-001'),
            'INS-2024-002' => $this->getPolicyData('INS-2024-002'),
        ];
        
        return array_filter($allPolicies, fn($policy) => $policy['customer_id'] === $customerId);
    }
}
