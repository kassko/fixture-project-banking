<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Brand\BrandConfigurationLoader;
use App\Brand\BrandResolver;
use App\Context\BrandContext;
use App\Context\CampaignContext;
use App\Context\SessionContext;
use App\Context\TenantContext;
use App\Context\TemporalContext;
use App\Context\UnifiedContext;
use App\Context\UserContext;
use App\DTO\Request\EligibilityRequest;
use App\FeatureFlag\FeatureFlagContext;
use App\FeatureFlag\FeatureFlagService;
use App\Service\Eligibility\ProductEligibilityService;
use App\Temporal\TemporalContextProvider;
use App\Tenant\TenantConfigurationLoader;
use App\Tenant\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/eligibility', name: 'api_eligibility_')]
#[OA\Tag(name: 'Eligibility')]
class EligibilityController extends AbstractController
{
    public function __construct(
        private ProductEligibilityService $eligibilityService,
        private FeatureFlagService $featureFlags,
        private TenantResolver $tenantResolver,
        private TenantConfigurationLoader $tenantConfigurationLoader,
        private BrandResolver $brandResolver,
        private BrandConfigurationLoader $brandConfigurationLoader,
        private TemporalContextProvider $temporalContextProvider
    ) {
    }

    #[Route('/products', name: 'products', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(
        required: ['customerId'],
        properties: [
            new OA\Property(property: 'customerId', type: 'integer', example: 1),
            new OA\Property(property: 'productCategories', type: 'array', items: new OA\Items(type: 'string'),
                example: ['SAVINGS', 'LOANS', 'INSURANCE', 'INVESTMENT']),
            new OA\Property(property: 'includeReasons', type: 'boolean', example: true),
        ]
    ))]
    #[OA\Parameter(
        name: 'X-Tenant-Id',
        in: 'header',
        required: false,
        schema: new OA\Schema(type: 'string'),
        example: 'banque_alpha'
    )]
    #[OA\Parameter(
        name: 'X-Brand-Id',
        in: 'header',
        required: false,
        schema: new OA\Schema(type: 'string'),
        example: 'premium_gold'
    )]
    #[OA\Response(
        response: 200,
        description: 'Liste des produits éligibles avec conditions'
    )]
    public function evaluateProducts(Request $request): JsonResponse
    {
        $context = $this->buildUnifiedContext($request);
        $data = json_decode($request->getContent(), true);

        $eligibilityRequest = new EligibilityRequest(
            customerId: $data['customerId'],
            productCategories: $data['productCategories'] ?? null,
            includeReasons: $data['includeReasons'] ?? true
        );

        // Utiliser le nouveau moteur si feature flag actif
        $featureFlagContext = new FeatureFlagContext(
            $context->tenant->getTenantId(),
            $context->brand->getBrandId(),
            null,
            $context->temporal->getCurrentDateTime()
        );

        if ($this->featureFlags->isEnabled('new_eligibility_engine', $featureFlagContext)) {
            $response = $this->eligibilityService->evaluateWithNewEngine($eligibilityRequest, $context);
        } else {
            $response = $this->eligibilityService->evaluate($eligibilityRequest, $context);
        }

        return $this->json([
            'eligible_products' => $response->getEligibleProducts(),
            'ineligible_products' => $response->getIneligibleProducts(),
            'recommendations' => $response->getRecommendations(),
            'evaluation_summary' => [
                'total_evaluated' => $response->getTotalEvaluated(),
                'eligible_count' => count($response->getEligibleProducts()),
                'rules_applied' => $response->getRulesApplied(),
            ],
            'context' => [
                'tenant' => $context->tenant->getTenantId(),
                'brand' => $context->brand->getBrandId(),
                'period' => $context->temporal->getCurrentPeriod(),
            ],
        ]);
    }

    private function buildUnifiedContext(Request $request): UnifiedContext
    {
        // Resolve tenant
        $tenantId = $this->tenantResolver->resolve($request) ?? 'default';
        $tenantConfig = $this->tenantConfigurationLoader->load($tenantId);
        $tenantContext = new TenantContext($tenantId, $tenantConfig->getConfig());

        // Resolve brand
        $brandId = $this->brandResolver->resolve($request) ?? 'standard';
        $brandConfig = $this->brandConfigurationLoader->load($brandId);
        $brandContext = new BrandContext($brandId, $brandConfig->getConfig());

        // User context (simplified)
        $userContext = new UserContext(null, 'guest', []);

        // Session context (simplified)
        $sessionContext = new SessionContext(uniqid('sess_', true), []);

        // Temporal context
        $temporalContext = $this->temporalContextProvider->createContext();

        // Campaign context (simplified)
        $campaignContext = new CampaignContext($this->getActiveCampaigns($temporalContext));

        return new UnifiedContext(
            $tenantContext,
            $brandContext,
            $userContext,
            $sessionContext,
            $temporalContext,
            $campaignContext
        );
    }

    private function getActiveCampaigns(TemporalContext $temporalContext): array
    {
        $campaigns = [];

        // Add end of year campaign if in promotion period
        if ($temporalContext->getPeriod() === 'end_of_year_promotion') {
            $campaigns['end_of_year'] = [
                'name' => 'Promotion de fin d\'année',
                'discount' => 0.15,
                'eligible_products' => ['SAVINGS_ACCOUNT', 'CHECKING_ACCOUNT'],
                'end_date' => '2024-12-31',
            ];
        }

        return $campaigns;
    }
}
