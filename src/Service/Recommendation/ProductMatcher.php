<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

class ProductMatcher
{
    private const PRODUCT_CATALOG = [
        [
            'code' => 'SAVINGS_PREMIUM',
            'name' => 'Compte Épargne Premium',
            'category' => 'SAVINGS',
            'interest_rate' => 2.5,
            'target_customer_types' => ['premium', 'vip'],
            'min_income' => 50000,
            'min_age' => 18,
            'max_age' => 100,
            'features' => ['Taux bonifié', 'Conseiller dédié', 'Carte premium gratuite'],
        ],
        [
            'code' => 'SAVINGS_STANDARD',
            'name' => 'Livret Épargne',
            'category' => 'SAVINGS',
            'interest_rate' => 1.5,
            'target_customer_types' => ['individual', 'premium'],
            'min_income' => 0,
            'min_age' => 18,
            'max_age' => 100,
            'features' => ['Disponibilité immédiate', 'Taux compétitif'],
        ],
        [
            'code' => 'LOAN_HOME',
            'name' => 'Prêt Immobilier',
            'category' => 'LOANS',
            'interest_rate' => 3.5,
            'target_customer_types' => ['individual', 'premium', 'vip'],
            'min_income' => 30000,
            'min_age' => 21,
            'max_age' => 65,
            'features' => ['Taux fixe', 'Assurance incluse', 'Modulation possible'],
        ],
        [
            'code' => 'INVESTMENT_BALANCED',
            'name' => 'Assurance Vie Équilibrée',
            'category' => 'INVESTMENT',
            'interest_rate' => 4.0,
            'target_customer_types' => ['individual', 'premium', 'vip'],
            'min_income' => 25000,
            'min_age' => 25,
            'max_age' => 75,
            'features' => ['Diversification', 'Fiscalité avantageuse', 'Gestion pilotée'],
        ],
        [
            'code' => 'INVESTMENT_AGGRESSIVE',
            'name' => 'PEA Actions',
            'category' => 'INVESTMENT',
            'interest_rate' => 6.0,
            'target_customer_types' => ['premium', 'vip'],
            'min_income' => 50000,
            'min_age' => 25,
            'max_age' => 70,
            'features' => ['Performance élevée', 'Avantage fiscal', 'Investissement actions'],
        ],
        [
            'code' => 'INSURANCE_HOME',
            'name' => 'Assurance Habitation',
            'category' => 'INSURANCE',
            'interest_rate' => 0.0,
            'target_customer_types' => ['individual', 'premium', 'vip'],
            'min_income' => 0,
            'min_age' => 18,
            'max_age' => 100,
            'features' => ['Couverture complète', 'Assistance 24/7', 'Protection juridique'],
        ],
        [
            'code' => 'CREDIT_CARD_GOLD',
            'name' => 'Carte Gold',
            'category' => 'PAYMENT',
            'interest_rate' => 0.0,
            'target_customer_types' => ['premium', 'vip'],
            'min_income' => 40000,
            'min_age' => 21,
            'max_age' => 100,
            'features' => ['Assurances voyages', 'Concierge', 'Cashback 1%'],
        ],
    ];

    public function findMatchingProducts(
        array $customerProfile,
        ?array $productCategories = null
    ): array {
        $products = self::PRODUCT_CATALOG;
        
        // Filter by categories if specified
        if ($productCategories !== null) {
            $products = array_filter($products, function ($product) use ($productCategories) {
                return in_array($product['category'], $productCategories, true);
            });
        }
        
        // Filter products based on customer eligibility
        $matchingProducts = [];
        foreach ($products as $product) {
            if ($this->isEligible($product, $customerProfile)) {
                $matchingProducts[] = $product;
            }
        }
        
        return $matchingProducts;
    }

    private function isEligible(array $product, array $customerProfile): bool
    {
        // Check customer type
        if (!in_array($customerProfile['customer_type'], $product['target_customer_types'], true)) {
            return false;
        }
        
        // Check minimum income
        if (isset($product['min_income']) && $customerProfile['annual_income'] < $product['min_income']) {
            return false;
        }
        
        // Check age range
        if (isset($product['min_age']) && $customerProfile['age'] < $product['min_age']) {
            return false;
        }
        
        if (isset($product['max_age']) && $customerProfile['age'] > $product['max_age']) {
            return false;
        }
        
        return true;
    }

    public function getProductByCode(string $code): ?array
    {
        foreach (self::PRODUCT_CATALOG as $product) {
            if ($product['code'] === $code) {
                return $product;
            }
        }
        
        return null;
    }
}
