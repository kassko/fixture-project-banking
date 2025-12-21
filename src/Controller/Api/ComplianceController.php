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
use App\DTO\Request\ComplianceCheckRequest;
use App\Service\Compliance\ComplianceCheckService;
use App\Temporal\TemporalContextProvider;
use App\Tenant\TenantConfigurationLoader;
use App\Tenant\TenantResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/compliance', name: 'api_compliance_')]
#[OA\Tag(name: 'Compliance')]
class ComplianceController extends AbstractController
{
    public function __construct(
        private ComplianceCheckService $complianceCheckService,
        private TenantResolver $tenantResolver,
        private TenantConfigurationLoader $tenantConfigurationLoader,
        private BrandResolver $brandResolver,
        private BrandConfigurationLoader $brandConfigurationLoader,
        private TemporalContextProvider $temporalContextProvider
    ) {
    }

    #[Route('/check', name: 'check', methods: ['POST'])]
    #[OA\Post(
        summary: 'Lancer une vérification de conformité complète',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'checkTypes',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        nullable: true,
                        example: ['KYC', 'AML', 'REGULATORY'],
                        description: 'Types de vérifications: KYC, AML, REGULATORY'
                    ),
                    new OA\Property(property: 'includeRecommendations', type: 'boolean', example: true),
                    new OA\Property(
                        property: 'transactionIds',
                        type: 'array',
                        items: new OA\Items(type: 'integer'),
                        nullable: true,
                        example: [101, 102, 103]
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
                example: 'premium_gold'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Vérification de conformité effectuée avec succès'
            ),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function check(Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Parse request
            $data = json_decode($request->getContent(), true);
            $complianceRequest = ComplianceCheckRequest::fromArray($data);

            // Execute compliance check
            $response = $this->complianceCheckService->check($complianceRequest, $context);

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

    #[Route('/status/{customerId}', name: 'status', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le statut de conformité d\'un client',
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
                description: 'Statut de conformité du client'
            ),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getStatus(int $customerId, Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Get status
            $status = $this->complianceCheckService->getStatus($customerId, $context);

            return $this->json($status, Response::HTTP_OK);
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

    #[Route('/kyc/verify', name: 'kyc_verify', methods: ['POST'])]
    #[OA\Post(
        summary: 'Vérification KYC spécifique',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1)
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
                description: 'Vérification KYC effectuée'
            ),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function verifyKyc(Request $request): JsonResponse
    {
        try {
            // Build unified context
            $context = $this->buildUnifiedContext($request);

            // Parse request
            $data = json_decode($request->getContent(), true);
            $customerId = $data['customerId'] ?? null;

            if (!$customerId) {
                return $this->json([
                    'error' => 'customerId is required'
                ], Response::HTTP_BAD_REQUEST);
            }

            // Execute KYC verification
            $result = $this->complianceCheckService->verifyKyc($customerId, $context);

            return $this->json($result, Response::HTTP_OK);
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

        // Add compliance-related campaigns if applicable
        if ($temporalContext->getPeriod() === 'end_of_year_promotion') {
            $campaigns['compliance_review'] = [
                'name' => 'Révision de conformité annuelle',
                'free_compliance_check' => true,
            ];
        }

        return $campaigns;
    }
}
