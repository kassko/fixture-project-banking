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
use App\DTO\Request\ConsolidationRequest;
use App\Service\Consolidation\AccountConsolidationService;
use App\Temporal\TemporalContextProvider;
use App\Tenant\TenantConfigurationLoader;
use App\Tenant\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/consolidation', name: 'api_consolidation_')]
#[OA\Tag(name: 'Consolidation')]
class ConsolidationController extends AbstractController
{
    public function __construct(
        private AccountConsolidationService $consolidationService,
        private TenantResolver $tenantResolver,
        private TenantConfigurationLoader $tenantConfigurationLoader,
        private BrandResolver $brandResolver,
        private BrandConfigurationLoader $brandConfigurationLoader,
        private TemporalContextProvider $temporalContextProvider
    ) {
    }

    #[Route('/accounts', name: 'accounts', methods: ['POST'])]
    #[OA\Post(
        summary: 'Consolider les comptes d\'un client',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'accountIds', type: 'array', items: new OA\Items(type: 'integer'), nullable: true, example: [1, 2, 3]),
                    new OA\Property(property: 'includeInactiveAccounts', type: 'boolean', example: false),
                    new OA\Property(property: 'consolidationType', type: 'string', example: 'ALL', description: 'ALL, CHECKING, SAVINGS, INVESTMENT, LOAN')
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
                description: 'Consolidation réussie avec données agrégées'
            ),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function consolidateAccounts(Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Parse request
            $data = json_decode($request->getContent(), true);
            $consolidationRequest = ConsolidationRequest::fromArray($data);

            // Execute consolidation
            $response = $this->consolidationService->consolidate($consolidationRequest, $context);

            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/summary/{customerId}', name: 'summary', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le résumé consolidé d\'un client',
        parameters: [
            new OA\Parameter(
                name: 'customerId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
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
            new OA\Response(
                response: 200,
                description: 'Résumé consolidé du client'
            ),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getSummary(int $customerId, Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Get summary
            $summary = $this->consolidationService->getSummary($customerId, $context);

            return $this->json($summary, Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
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
                'eligible_products' => ['CONSOLIDATION_ACCOUNT'],
            ];
        }

        return $campaigns;
    }
}
