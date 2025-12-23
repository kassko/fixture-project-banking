<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\ClaimRequest;
use App\DTO\Request\ClaimStatusUpdate;
use App\Service\Claims\ClaimManagementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/claims', name: 'api_claims_')]
#[OA\Tag(name: 'Claims')]
class ClaimController extends AbstractController
{
    public function __construct(
        private ClaimManagementService $claimService
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des réclamations',
        responses: [
            new OA\Response(response: 200, description: 'Liste des réclamations retournée')
        ]
    )]
    public function list(): JsonResponse
    {
        return $this->json([
            'message' => 'Use GET /api/v1/claims/customer/{customerId} to retrieve claims for a specific customer'
        ], Response::HTTP_OK);
    }

    #[Route('/{claimId}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir les détails d\'une réclamation',
        parameters: [
            new OA\Parameter(name: 'claimId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Réclamation trouvée'),
            new OA\Response(response: 404, description: 'Réclamation non trouvée')
        ]
    )]
    public function get(int $claimId): JsonResponse
    {
        $claim = $this->claimService->getClaim($claimId);
        
        if (!$claim) {
            return $this->json([
                'error' => 'Claim not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($claim->toArray(), Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer une nouvelle réclamation',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'type', 'description', 'incidentDate'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'type', type: 'string', enum: ['INSURANCE_CLAIM', 'COMPLAINT', 'SERVICE_REQUEST', 'GENERAL'], example: 'COMPLAINT'),
                    new OA\Property(property: 'description', type: 'string', example: 'Réclamation concernant les frais bancaires'),
                    new OA\Property(property: 'incidentDate', type: 'string', format: 'date', example: '2024-01-15'),
                    new OA\Property(property: 'policyId', type: 'integer', example: 123),
                    new OA\Property(property: 'amount', type: 'number', example: 150.50),
                    new OA\Property(property: 'attachments', type: 'array', items: new OA\Items(type: 'string'))
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Réclamation créée avec succès'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $claimRequest = ClaimRequest::fromArray($data);
            
            $response = $this->claimService->createClaim($claimRequest);
            
            return $this->json($response->toArray(), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{claimId}/status', name: 'update_status', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Mettre à jour le statut d\'une réclamation',
        parameters: [
            new OA\Parameter(name: 'claimId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['status'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', enum: ['OPEN', 'IN_PROGRESS', 'PENDING_INFO', 'ESCALATED', 'RESOLVED', 'REJECTED', 'CLOSED']),
                    new OA\Property(property: 'comment', type: 'string', example: 'Mise à jour du statut'),
                    new OA\Property(property: 'assignedTo', type: 'string', example: 'agent@bank.com')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Statut mis à jour'),
            new OA\Response(response: 400, description: 'Transition invalide'),
            new OA\Response(response: 404, description: 'Réclamation non trouvée')
        ]
    )]
    public function updateStatus(int $claimId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $statusUpdate = ClaimStatusUpdate::fromArray($data);
            
            $response = $this->claimService->updateClaimStatus($claimId, $statusUpdate);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/customer/{customerId}', name: 'by_customer', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des réclamations d\'un client',
        parameters: [
            new OA\Parameter(name: 'customerId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Liste des réclamations retournée')
        ]
    )]
    public function getByCustomer(int $customerId): JsonResponse
    {
        $claims = $this->claimService->getCustomerClaims($customerId);
        
        return $this->json([
            'customer_id' => $customerId,
            'claims' => array_map(fn($claim) => $claim->toArray(), $claims),
            'total_claims' => count($claims),
        ], Response::HTTP_OK);
    }

    #[Route('/{claimId}/sla', name: 'sla_metrics', methods: ['GET'])]
    #[OA\Get(
        summary: 'Vérifier le respect des SLA pour une réclamation',
        parameters: [
            new OA\Parameter(name: 'claimId', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Métriques SLA retournées'),
            new OA\Response(response: 404, description: 'Réclamation non trouvée')
        ]
    )]
    public function getSlaMetrics(int $claimId): JsonResponse
    {
        try {
            $slaMetrics = $this->claimService->getClaimSlaMetrics($claimId);
            
            return $this->json([
                'claim_id' => $claimId,
                'sla_metrics' => $slaMetrics,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
