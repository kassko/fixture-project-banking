<?php

declare(strict_types=1);

namespace App\Service\Recommendation;

use App\DTO\Request\RecommendationRequest;
use App\DTO\Response\RecommendationResponse;
use App\Repository\CustomerRepository;

class RecommendationEngine
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private CustomerProfileAnalyzer $profileAnalyzer,
        private ProductMatcher $productMatcher,
        private RelevanceScoreCalculator $scoreCalculator
    ) {
    }

    public function generateRecommendations(RecommendationRequest $request, array $context = []): RecommendationResponse
    {
        // Get customer
        $customer = $this->customerRepository->find($request->customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Analyze customer profile
        $customerProfile = $this->profileAnalyzer->analyzeProfile($customer);

        // Find matching products
        $matchingProducts = $this->productMatcher->findMatchingProducts(
            $customerProfile,
            $request->productCategories
        );

        // Calculate relevance scores
        $recommendations = [];
        foreach ($matchingProducts as $product) {
            $score = $this->scoreCalculator->calculateScore($product, $customerProfile, $context);
            
            $recommendations[] = [
                'product_code' => $product['code'],
                'product_name' => $product['name'],
                'category' => $product['category'],
                'relevance_score' => round($score, 2),
                'features' => $product['features'],
                'interest_rate' => $product['interest_rate'] ?? null,
                'recommendation_reason' => $this->buildRecommendationReason($product, $customerProfile, $score),
            ];
        }

        // Rank recommendations by score
        $recommendations = $this->scoreCalculator->rankRecommendations($recommendations);

        // Generate optimization suggestions if requested
        $optimizationSuggestions = null;
        if ($request->includeOptimization) {
            $optimizationSuggestions = $this->generateOptimizationSuggestions($customerProfile, $recommendations);
        }

        return new RecommendationResponse(
            $request->customerId,
            $recommendations,
            $customerProfile,
            $optimizationSuggestions
        );
    }

    private function buildRecommendationReason(array $product, array $customerProfile, float $score): string
    {
        $reasons = [];

        // High relevance score
        if ($score >= 80) {
            $reasons[] = 'Excellente adéquation avec votre profil';
        } elseif ($score >= 65) {
            $reasons[] = 'Bien adapté à vos besoins';
        }

        // Life stage matching
        if (isset($customerProfile['life_stage'])) {
            $stageReasons = [
                'young_professional' => 'Idéal pour démarrer votre parcours financier',
                'family_building' => 'Adapté à vos objectifs familiaux',
                'wealth_accumulation' => 'Optimise votre patrimoine',
                'retirement_planning' => 'Sécurise votre retraite',
            ];
            
            if (isset($stageReasons[$customerProfile['life_stage']])) {
                $reasons[] = $stageReasons[$customerProfile['life_stage']];
            }
        }

        // Financial goals alignment
        $goalAlignments = [
            'SAVINGS' => ['savings', 'credit_building'],
            'INVESTMENT' => ['investment_growth', 'investment_start', 'wealth_preservation'],
            'LOANS' => ['home_ownership'],
            'INSURANCE' => ['insurance', 'healthcare'],
        ];

        if (isset($goalAlignments[$product['category']])) {
            foreach ($goalAlignments[$product['category']] as $goal) {
                if (in_array($goal, $customerProfile['financial_goals'] ?? [], true)) {
                    $reasons[] = 'Correspond à vos objectifs financiers';
                    break;
                }
            }
        }

        return implode('. ', $reasons) ?: 'Produit recommandé';
    }

    private function generateOptimizationSuggestions(array $customerProfile, array $recommendations): array
    {
        $suggestions = [];

        // Diversification suggestion
        $categories = array_unique(array_column($recommendations, 'category'));
        if (count($categories) < 3) {
            $suggestions[] = [
                'type' => 'diversification',
                'title' => 'Diversifiez vos placements',
                'description' => 'Nous recommandons d\'explorer des produits dans différentes catégories pour optimiser votre portefeuille.',
                'priority' => 'medium',
            ];
        }

        // Savings rate suggestion
        if ($customerProfile['customer_type'] !== 'vip' && $customerProfile['annual_income'] > 50000) {
            $suggestions[] = [
                'type' => 'upgrade',
                'title' => 'Éligibilité Premium',
                'description' => 'Votre profil vous permet d\'accéder à des produits premium avec des avantages exclusifs.',
                'priority' => 'high',
            ];
        }

        // Risk profile optimization
        if ($customerProfile['risk_profile'] === 'conservative' && $customerProfile['age'] < 40) {
            $suggestions[] = [
                'type' => 'risk_adjustment',
                'title' => 'Optimisez votre profil de risque',
                'description' => 'Avec votre horizon d\'investissement long terme, vous pourriez bénéficier de placements plus dynamiques.',
                'priority' => 'low',
            ];
        }

        // Life stage optimization
        if ($customerProfile['life_stage'] === 'family_building') {
            $hasInsurance = false;
            foreach ($recommendations as $rec) {
                if ($rec['category'] === 'INSURANCE') {
                    $hasInsurance = true;
                    break;
                }
            }
            
            if (!$hasInsurance) {
                $suggestions[] = [
                    'type' => 'protection',
                    'title' => 'Protection familiale',
                    'description' => 'Pensez à protéger votre famille avec une assurance adaptée à votre situation.',
                    'priority' => 'high',
                ];
            }
        }

        return $suggestions;
    }
}
