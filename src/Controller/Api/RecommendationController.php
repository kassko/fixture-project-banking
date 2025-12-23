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
use App\DTO\Request\RecommendationRequest;
use App\Service\Recommendation\RecommendationEngine;
use App\Temporal\TemporalContextProvider;
use App\Tenant\TenantConfigurationLoader;
use App\Tenant\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/recommendations', name: 'api_recommendations_')]
#[OA\Tag(name: 'Recommendations')]
class RecommendationController extends AbstractController
{
    public function __construct(
        private RecommendationEngine $recommendationEngine,
        private TenantResolver $tenantResolver,
        private TenantConfigurationLoader $tenantConfigurationLoader,
        private BrandResolver $brandResolver,
        private BrandConfigurationLoader $brandConfigurationLoader,
        private TemporalContextProvider $temporalContextProvider
    ) {
    }

    #[Route('/{customerId}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir les recommandations pour un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
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
            new OA\Response(response: 200, description: 'Recommandations générées'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function get(int $customerId, Request $request): JsonResponse
    {
        try {
            $context = $this->buildContext($request);
            
            $recommendationRequest = new RecommendationRequest(
                customerId: $customerId,
                productCategories: null,
                context: null,
                includeOptimization: false
            );

            $response = $this->recommendationEngine->generateRecommendations(
                $recommendationRequest,
                $this->buildRecommendationContext($context)
            );

            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/generate', name: 'generate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Générer des recommandations personnalisées',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'productCategories',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['SAVINGS', 'INVESTMENT']
                    ),
                    new OA\Property(property: 'context', type: 'string', example: 'retirement_planning'),
                    new OA\Property(property: 'includeOptimization', type: 'boolean', example: true)
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
            new OA\Response(response: 200, description: 'Recommandations générées'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function generate(Request $request): JsonResponse
    {
        try {
            $context = $this->buildContext($request);
            $data = json_decode($request->getContent(), true);
            
            $recommendationRequest = RecommendationRequest::fromArray($data);

            $response = $this->recommendationEngine->generateRecommendations(
                $recommendationRequest,
                $this->buildRecommendationContext($context)
            );

            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{customerId}/products', name: 'products', methods: ['GET'])]
    #[OA\Get(
        summary: 'Recommandations de produits pour un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(
                name: 'category',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['SAVINGS', 'LOANS', 'INVESTMENT', 'INSURANCE', 'PAYMENT'])
            ),
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
            new OA\Response(response: 200, description: 'Recommandations de produits'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function products(int $customerId, Request $request): JsonResponse
    {
        try {
            $context = $this->buildContext($request);
            $category = $request->query->get('category');
            
            $recommendationRequest = new RecommendationRequest(
                customerId: $customerId,
                productCategories: $category ? [$category] : null,
                context: null,
                includeOptimization: false
            );

            $response = $this->recommendationEngine->generateRecommendations(
                $recommendationRequest,
                $this->buildRecommendationContext($context)
            );

            return $this->json([
                'customer_id' => $customerId,
                'category_filter' => $category,
                'product_recommendations' => $response->getRecommendations(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{customerId}/optimization', name: 'optimization', methods: ['GET'])]
    #[OA\Get(
        summary: 'Suggestions d\'optimisation pour un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
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
            new OA\Response(response: 200, description: 'Suggestions d\'optimisation'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function optimization(int $customerId, Request $request): JsonResponse
    {
        try {
            $context = $this->buildContext($request);
            
            $recommendationRequest = new RecommendationRequest(
                customerId: $customerId,
                productCategories: null,
                context: null,
                includeOptimization: true
            );

            $response = $this->recommendationEngine->generateRecommendations(
                $recommendationRequest,
                $this->buildRecommendationContext($context)
            );

            return $this->json([
                'customer_id' => $customerId,
                'customer_profile' => $response->getCustomerProfile(),
                'optimization_suggestions' => $response->getOptimizationSuggestions(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function buildContext(Request $request): UnifiedContext
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

    private function buildRecommendationContext(UnifiedContext $context): array
    {
        return [
            'tenant' => $context->getTenantContext()->getTenantId(),
            'brand' => $context->getBrandContext()->getBrandId(),
            'period' => $context->getTemporalContext()->getPeriod(),
            'active_campaigns' => $context->getCampaignContext()->getCampaigns(),
        ];
    }

    private function getActiveCampaigns(TemporalContext $temporalContext): array
    {
        $campaigns = [];

        // Add end of year campaign if in promotion period
        if ($temporalContext->getPeriod() === 'end_of_year_promotion') {
            $campaigns['end_of_year'] = [
                'name' => 'Promotion de fin d\'année',
                'discount' => 0.15,
                'eligible_products' => ['SAVINGS_PREMIUM', 'INVESTMENT_BALANCED'],
                'end_date' => '2024-12-31',
            ];
        }

        return $campaigns;
    }
}
