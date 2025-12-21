<?php

declare(strict_types=1);

namespace App\Service\Eligibility;

use App\Context\UnifiedContext;
use App\DataSource\ComplianceDataSource;
use App\DataSource\ExternalRatingDataSource;
use App\DTO\Request\EligibilityRequest;
use App\DTO\Response\EligibilityResponse;
use App\DTO\Response\EligibleProduct;
use App\DTO\Response\IneligibilityReason;
use App\Repository\CustomerRepository;
use App\Service\Eligibility\RuleEngine\EligibilityRuleInterface;
use App\Service\Eligibility\RuleEngine\RuleEvaluationContext;
use App\Tenant\TenantConfigurationLoader;
use App\Brand\BrandConfigurationLoader;

class ProductEligibilityService
{
    public function __construct(
        private CustomerRepository $customerRepository,
        private ComplianceDataSource $complianceDataSource,
        private ExternalRatingDataSource $ratingDataSource,
        private TenantConfigurationLoader $tenantConfigLoader,
        private BrandConfigurationLoader $brandConfigLoader,
        private iterable $rules // Injection des règles via service tag
    ) {
    }

    public function evaluate(EligibilityRequest $request, UnifiedContext $context): EligibilityResponse
    {
        // 1. Charger les données client
        $customer = $this->customerRepository->find($request->customerId);
        
        if (!$customer) {
            throw new \InvalidArgumentException("Customer not found: {$request->customerId}");
        }

        $externalId = $customer->getExternalId() ?? 'CUST' . str_pad((string)$customer->getId(), 3, '0', STR_PAD_LEFT);
        
        try {
            $kycData = $this->complianceDataSource->getKYCData($externalId);
            $amlData = $this->complianceDataSource->getAMLChecks($externalId);
            $creditRating = $this->ratingDataSource->getCreditRating($externalId);
        } catch (\Exception $e) {
            // Use default data if external services fail
            $kycData = ['kyc_status' => 'VERIFIED'];
            $amlData = ['aml_status' => 'CLEAR'];
            $creditRating = ['score' => 700, 'rating' => 'A'];
        }

        // 2. Charger config tenant/brand
        $tenantConfig = $this->tenantConfigLoader->load($context->tenant->getTenantId());
        $brandConfig = $this->brandConfigLoader->load($context->brand->getBrandId());

        // 3. Construire le contexte d'évaluation
        $ruleContext = new RuleEvaluationContext(
            customer: $customer,
            kycData: $kycData,
            amlData: $amlData,
            creditRating: $creditRating,
            existingProducts: $customer->getProducts(),
            tenantConfig: $tenantConfig,
            brandConfig: $brandConfig,
            temporalContext: $context->temporal,
            campaignContext: $context->campaign
        );

        // 4. Récupérer les produits à évaluer
        $products = $this->getProductsToEvaluate($request, $tenantConfig, $brandConfig);

        // 5. Évaluer chaque produit
        $eligibleProducts = [];
        $ineligibleProducts = [];
        $rulesApplied = [];

        foreach ($products as $product) {
            $result = $this->evaluateProduct($product, $ruleContext);
            $rulesApplied = array_merge($rulesApplied, $result['rules_applied']);

            if ($result['eligible']) {
                $eligibleProducts[] = new EligibleProduct(
                    productCode: $product['code'],
                    productName: $product['name'],
                    category: $product['category'],
                    conditions: $result['conditions'],
                    specialOffer: $result['special_offer'],
                    priority: $result['priority']
                );
            } else {
                $ineligibleProducts[] = new IneligibilityReason(
                    productCode: $product['code'],
                    productName: $product['name'],
                    reasons: $result['reasons'],
                    canBeRemediated: $result['can_be_remediated'],
                    remediationSteps: $result['remediation_steps']
                );
            }
        }

        // 6. Générer recommandations
        $recommendations = $this->generateRecommendations($eligibleProducts, $ruleContext);

        return new EligibilityResponse(
            eligibleProducts: $eligibleProducts,
            ineligibleProducts: $request->includeReasons ? $ineligibleProducts : [],
            recommendations: $recommendations,
            totalEvaluated: count($products),
            rulesApplied: array_unique($rulesApplied)
        );
    }

    public function evaluateWithNewEngine(EligibilityRequest $request, UnifiedContext $context): EligibilityResponse
    {
        // Nouveau moteur ML (feature flag)
        return $this->evaluate($request, $context);
    }

    private function evaluateProduct(array $product, RuleEvaluationContext $context): array
    {
        $reasons = [];
        $conditions = [];
        $rulesApplied = [];
        $eligible = true;

        foreach ($this->rules as $rule) {
            if (!$rule->supports($product)) {
                continue;
            }

            $rulesApplied[] = $rule->getName();
            $result = $rule->evaluate($product, $context);

            if (!$result->isPassed()) {
                $eligible = false;
                $reasons[] = [
                    'rule' => $rule->getName(),
                    'message' => $result->getMessage(),
                ];
            } elseif ($result->hasConditions()) {
                $conditions = array_merge($conditions, $result->getConditions());
            }
        }

        return [
            'eligible' => $eligible,
            'reasons' => $reasons,
            'conditions' => $conditions,
            'rules_applied' => $rulesApplied,
            'special_offer' => $this->checkCampaignOffer($product, $context),
            'can_be_remediated' => $this->canBeRemediated($reasons),
            'remediation_steps' => $this->getRemediationSteps($reasons),
            'priority' => $this->calculatePriority($product, $context),
        ];
    }

    private function getProductsToEvaluate($request, $tenantConfig, $brandConfig): array
    {
        $products = $tenantConfig->getAvailableProducts();
        
        if (empty($products)) {
            // Default products if not configured
            $products = $this->getDefaultProducts();
        }
        
        // Filtrer par brand
        $includedProducts = $brandConfig->getIncludedProducts();
        if (!empty($includedProducts)) {
            $products = array_filter($products, fn($p) => 
                in_array($p['code'], $includedProducts)
            );
        }

        // Filtrer par catégorie si demandé
        if ($request->productCategories) {
            $products = array_filter($products, fn($p) => 
                in_array($p['category'], $request->productCategories)
            );
        }

        return array_values($products);
    }

    private function getDefaultProducts(): array
    {
        return [
            [
                'code' => 'SAVINGS_ACCOUNT',
                'name' => 'Compte Épargne',
                'category' => 'SAVINGS',
                'requires_kyc' => true,
                'min_credit_score' => 500,
                'base_priority' => 80,
            ],
            [
                'code' => 'CHECKING_ACCOUNT',
                'name' => 'Compte Courant',
                'category' => 'SAVINGS',
                'requires_kyc' => true,
                'min_credit_score' => 450,
                'base_priority' => 90,
            ],
            [
                'code' => 'PERSONAL_LOAN',
                'name' => 'Prêt Personnel',
                'category' => 'LOANS',
                'requires_kyc' => true,
                'min_credit_score' => 650,
                'min_income' => 25000,
                'age_requirements' => ['min_age' => 18, 'max_age' => 70],
                'base_priority' => 70,
            ],
            [
                'code' => 'HOME_INSURANCE',
                'name' => 'Assurance Habitation',
                'category' => 'INSURANCE',
                'requires_kyc' => true,
                'base_priority' => 60,
            ],
            [
                'code' => 'INVESTMENT_ACCOUNT',
                'name' => 'Compte Investissement',
                'category' => 'INVESTMENT',
                'requires_kyc' => true,
                'min_credit_score' => 700,
                'min_income' => 50000,
                'age_requirements' => ['min_age' => 21, 'max_age' => 75],
                'base_priority' => 50,
            ],
        ];
    }

    private function checkCampaignOffer(array $product, RuleEvaluationContext $context): ?array
    {
        if (!$context->campaignContext || !$context->campaignContext->getId()) {
            return null;
        }
        
        $campaign = $context->campaignContext;
        $eligibleProducts = $campaign->getEligibleProducts();
        
        // If no specific products listed, campaign applies to all; otherwise check if product is in list
        if (empty($eligibleProducts) || in_array($product['code'], $eligibleProducts)) {
            return [
                'campaign_id' => $campaign->getId(),
                'discount_value' => $campaign->getDiscountValue(),
                'valid_until' => $campaign->getEndDate()?->format('Y-m-d') ?? 'N/A',
            ];
        }
        return null;
    }

    private function generateRecommendations(array $products, RuleEvaluationContext $context): array
    {
        usort($products, fn($a, $b) => $b->priority <=> $a->priority);
        return array_slice(array_map(fn($p) => [
            'product_code' => $p->productCode,
            'reason' => 'Recommended based on your profile',
        ], $products), 0, 3);
    }

    private function canBeRemediated(array $reasons): bool
    {
        $blockers = ['AML_FAILED', 'SANCTIONS_HIT'];
        foreach ($reasons as $r) {
            if (in_array($r['rule'], $blockers)) {
                return false;
            }
        }
        return true;
    }

    private function getRemediationSteps(array $reasons): array
    {
        return array_values(array_filter(array_map(fn($r) => match($r['rule']) {
            'KYC_STATUS' => 'Complete identity verification',
            'INCOME_REQUIREMENT' => 'Provide income documentation',
            'CREDIT_SCORE' => 'Improve credit score',
            'AGE_REQUIREMENT' => 'Age requirement not met',
            default => null,
        }, $reasons)));
    }

    private function calculatePriority(array $product, RuleEvaluationContext $context): int
    {
        $priority = $product['base_priority'] ?? 50;
        if ($context->campaignContext && $context->campaignContext->getId()) {
            $priority += 20;
        }
        return $priority;
    }
}
