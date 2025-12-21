<?php

declare(strict_types=1);

namespace App\DataSource;

/**
 * Legacy customer data source returning array data.
 * Simulates data from a legacy system.
 */
class LegacyCustomerDataSource
{
    /**
     * Get customer data by ID.
     * Returns nested array structure representing complex legacy data.
     */
    public function getCustomerData(string $customerId): array
    {
        // Simulate different customers
        $customers = [
            'CUST001' => [
                'id' => 'CUST001',
                'personal_info' => [
                    'first_name' => 'Jean',
                    'last_name' => 'Dupont',
                    'birth_date' => '1985-03-15',
                    'gender' => 'M',
                    'nationality' => 'FR',
                    'contact' => [
                        'email' => 'jean.dupont@email.com',
                        'phones' => [
                            ['type' => 'mobile', 'number' => '+33612345678', 'primary' => true],
                            ['type' => 'home', 'number' => '+33145678900', 'primary' => false],
                        ],
                        'address' => [
                            'street' => '123 Rue de la Paix',
                            'city' => 'Paris',
                            'postal_code' => '75001',
                            'country' => 'FR',
                            'state' => 'Île-de-France',
                            'geo' => [
                                'lat' => 48.8566,
                                'lng' => 2.3522,
                            ],
                        ],
                    ],
                ],
                'accounts' => [
                    [
                        'account_number' => 'FR7612345678901234567890123',
                        'type' => 'checking',
                        'balance' => 15000.50,
                        'currency' => 'EUR',
                        'opened_date' => '2020-01-15',
                        'status' => 'active',
                    ],
                    [
                        'account_number' => 'FR7698765432109876543210987',
                        'type' => 'savings',
                        'balance' => 50000.00,
                        'currency' => 'EUR',
                        'opened_date' => '2020-01-15',
                        'status' => 'active',
                    ],
                ],
                'risk_profile' => [
                    'score' => 72,
                    'category' => 'moderate',
                    'last_assessment' => '2024-01-15',
                    'factors' => [
                        'income_stability' => 'high',
                        'debt_ratio' => 0.25,
                        'payment_history' => 'excellent',
                        'credit_utilization' => 0.30,
                    ],
                ],
                'preferences' => [
                    'communication' => ['email', 'sms'],
                    'language' => 'fr',
                    'paperless' => true,
                ],
                'metadata' => [
                    'customer_since' => '2020-01-15',
                    'segment' => 'retail',
                    'relationship_manager' => null,
                ],
            ],
            'CUST002' => [
                'id' => 'CUST002',
                'personal_info' => [
                    'first_name' => 'Marie',
                    'last_name' => 'Martin',
                    'birth_date' => '1990-07-22',
                    'gender' => 'F',
                    'nationality' => 'FR',
                    'contact' => [
                        'email' => 'marie.martin@email.com',
                        'phones' => [
                            ['type' => 'mobile', 'number' => '+33687654321', 'primary' => true],
                        ],
                        'address' => [
                            'street' => '456 Avenue des Champs',
                            'city' => 'Lyon',
                            'postal_code' => '69001',
                            'country' => 'FR',
                            'state' => 'Auvergne-Rhône-Alpes',
                            'geo' => [
                                'lat' => 45.7640,
                                'lng' => 4.8357,
                            ],
                        ],
                    ],
                ],
                'accounts' => [
                    [
                        'account_number' => 'FR7611111111112222222222222',
                        'type' => 'checking',
                        'balance' => 8500.75,
                        'currency' => 'EUR',
                        'opened_date' => '2021-06-10',
                        'status' => 'active',
                    ],
                ],
                'risk_profile' => [
                    'score' => 85,
                    'category' => 'low',
                    'last_assessment' => '2024-02-01',
                    'factors' => [
                        'income_stability' => 'very_high',
                        'debt_ratio' => 0.15,
                        'payment_history' => 'excellent',
                        'credit_utilization' => 0.20,
                    ],
                ],
                'preferences' => [
                    'communication' => ['email'],
                    'language' => 'fr',
                    'paperless' => true,
                ],
                'metadata' => [
                    'customer_since' => '2021-06-10',
                    'segment' => 'premium',
                    'relationship_manager' => 'Sophie Dubois',
                ],
            ],
        ];
        
        return $customers[$customerId] ?? throw new \InvalidArgumentException("Customer not found: $customerId");
    }
    
    /**
     * Get all customers.
     */
    public function getAllCustomers(): array
    {
        return [
            $this->getCustomerData('CUST001'),
            $this->getCustomerData('CUST002'),
        ];
    }
}
