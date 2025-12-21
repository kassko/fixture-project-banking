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
use App\DTO\Request\OnboardingJourneyRequest;
use App\FeatureFlag\FeatureFlagService;
use App\Service\Onboarding\OnboardingJourneyService;
use App\Temporal\TemporalContextProvider;
use App\Tenant\TenantConfigurationLoader;
use App\Tenant\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/onboarding', name: 'api_onboarding_')]
#[OA\Tag(name: 'Onboarding')]
class OnboardingController extends AbstractController
{
    public function __construct(
        private OnboardingJourneyService $onboardingService,
        private FeatureFlagService $featureFlags,
        private TenantResolver $tenantResolver,
        private TenantConfigurationLoader $tenantConfigurationLoader,
        private BrandResolver $brandResolver,
        private BrandConfigurationLoader $brandConfigurationLoader,
        private TemporalContextProvider $temporalContextProvider
    ) {
    }

    #[Route('/journey', name: 'journey', methods: ['POST'])]
    #[OA\RequestBody(content: new OA\JsonContent(
        required: ['customerType', 'targetProduct'],
        properties: [
            new OA\Property(property: 'customerId', type: 'integer', example: null, description: 'Null pour nouveau client'),
            new OA\Property(property: 'customerType', type: 'string', enum: ['INDIVIDUAL', 'CORPORATE', 'PREMIUM']),
            new OA\Property(property: 'targetProduct', type: 'string', example: 'CHECKING_ACCOUNT'),
            new OA\Property(property: 'channel', type: 'string', enum: ['WEB', 'MOBILE', 'AGENCY'], example: 'WEB'),
            new OA\Property(property: 'campaignCode', type: 'string', example: 'SUMMER2024'),
            new OA\Property(property: 'existingDocuments', type: 'array', items: new OA\Items(type: 'string')),
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
    #[OA\Response(response: 200, description: 'Parcours d\'onboarding personnalisé')]
    public function getJourney(Request $request): JsonResponse
    {
        $context = $this->buildUnifiedContext($request);
        $data = json_decode($request->getContent(), true);

        $journeyRequest = new OnboardingJourneyRequest(
            customerId: $data['customerId'] ?? null,
            customerType: $data['customerType'],
            targetProduct: $data['targetProduct'],
            channel: $data['channel'] ?? 'WEB',
            campaignCode: $data['campaignCode'] ?? null,
            existingDocuments: $data['existingDocuments'] ?? []
        );

        $response = $this->onboardingService->buildJourney($journeyRequest, $context);

        return $this->json([
            'journey_id' => $response->getJourneyId(),
            'steps' => $response->getSteps(),
            'required_documents' => $response->getRequiredDocuments(),
            'welcome_offers' => $response->getWelcomeOffers(),
            'estimated_completion_time' => $response->getEstimatedCompletionTime(),
            'dedicated_advisor' => $response->getDedicatedAdvisor(),
            'context' => [
                'tenant' => $context->tenant->getTenantId(),
                'brand' => $context->brand->getBrandId(),
                'campaign' => $context->campaign?->getId(),
                'channel' => $journeyRequest->channel,
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

        // Add summer campaign if applicable
        if ($temporalContext->getPeriod() === 'summer_promotion') {
            $campaigns['summer2024'] = [
                'name' => 'Promotion d\'été 2024',
                'discount' => 0.10,
                'eligible_products' => ['CHECKING_ACCOUNT'],
                'end_date' => '2024-08-31',
                'welcome_offer' => [
                    'code' => 'SUMMER2024',
                    'name' => 'Offre de bienvenue été',
                    'description' => 'Bonus de bienvenue exceptionnel',
                    'value' => 100.0,
                    'valid_days' => 60,
                ],
            ];
        }

        return $campaigns;
    }
}
