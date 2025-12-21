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
use App\DTO\Request\InsuranceQuoteRequest;
use App\DTO\Request\LoanSimulationRequest;
use App\FeatureFlag\FeatureFlagContext;
use App\FeatureFlag\FeatureFlagService;
use App\Service\Simulation\InsuranceQuoteService;
use App\Service\Simulation\LoanSimulationService;
use App\Tenant\TenantConfigurationLoader;
use App\Tenant\TenantResolver;
use App\Temporal\TemporalContextProvider;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/simulation', name: 'api_simulation_')]
#[OA\Tag(name: 'Simulations')]
class SimulationController extends AbstractController
{
    public function __construct(
        private TenantResolver $tenantResolver,
        private TenantConfigurationLoader $tenantConfigurationLoader,
        private BrandResolver $brandResolver,
        private BrandConfigurationLoader $brandConfigurationLoader,
        private TemporalContextProvider $temporalContextProvider,
        private FeatureFlagService $featureFlagService,
        private LoanSimulationService $loanSimulationService,
        private InsuranceQuoteService $insuranceQuoteService
    ) {
    }

    #[Route('/loan', name: 'loan', methods: ['POST'])]
    #[OA\Post(
        summary: 'Simulation de prêt avec logique multi-tenant/multi-brand',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 50000),
                    new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                    new OA\Property(property: 'purpose', type: 'string', example: 'HOME'),
                    new OA\Property(property: 'preferredDuration', type: 'integer', example: 60)
                ]
            )
        ),
        parameters: [
            new OA\Parameter(
                name: 'X-Tenant-Id',
                in: 'header',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'banque_alpha'
            ),
            new OA\Parameter(
                name: 'X-Brand-Id',
                in: 'header',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'premium_gold'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Simulation réussie avec plusieurs scénarios'
            ),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 403, description: 'Feature non activée')
        ]
    )]
    public function simulateLoan(Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Check feature flag
            $featureFlagContext = new FeatureFlagContext(
                $context->getTenantContext()->getTenantId(),
                $context->getBrandContext()->getBrandId(),
                null,
                $context->getTemporalContext()->getCurrentDateTime()
            );

            if (!$this->featureFlagService->isEnabled('loan_simulation', $featureFlagContext)) {
                return $this->json([
                    'error' => 'Loan simulation feature is not enabled'
                ], Response::HTTP_FORBIDDEN);
            }

            // Parse request
            $data = json_decode($request->getContent(), true);
            $loanRequest = LoanSimulationRequest::fromArray($data);

            // Execute simulation
            $response = $this->loanSimulationService->simulate($loanRequest, $context);

            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/insurance-quote', name: 'insurance_quote', methods: ['POST'])]
    #[OA\Post(
        summary: 'Devis assurance avec logique multi-tenant/multi-brand',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'insuranceType', type: 'string', example: 'HOME'),
                    new OA\Property(
                        property: 'assetDetails',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'value', type: 'number', example: 250000),
                            new OA\Property(property: 'yearBuilt', type: 'integer', example: 1990)
                        ]
                    )
                ]
            )
        ),
        parameters: [
            new OA\Parameter(
                name: 'X-Tenant-Id',
                in: 'header',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'banque_alpha'
            ),
            new OA\Parameter(
                name: 'X-Brand-Id',
                in: 'header',
                required: false,
                schema: new OA\Schema(type: 'string'),
                example: 'standard'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Devis généré avec succès'
            ),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 403, description: 'Feature non activée')
        ]
    )]
    public function getInsuranceQuote(Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Check feature flag
            $featureFlagContext = new FeatureFlagContext(
                $context->getTenantContext()->getTenantId(),
                $context->getBrandContext()->getBrandId(),
                null,
                $context->getTemporalContext()->getCurrentDateTime()
            );

            if (!$this->featureFlagService->isEnabled('insurance_quote', $featureFlagContext)) {
                return $this->json([
                    'error' => 'Insurance quote feature is not enabled'
                ], Response::HTTP_FORBIDDEN);
            }

            // Parse request
            $data = json_decode($request->getContent(), true);
            $quoteRequest = InsuranceQuoteRequest::fromArray($data);

            // Execute quote generation
            $response = $this->insuranceQuoteService->getQuote($quoteRequest, $context);

            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
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

        // Campaign context (simplified - would normally fetch from database)
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
                'applicable_products' => ['HOME', 'AUTO'],
            ];
        }

        return $campaigns;
    }
}
