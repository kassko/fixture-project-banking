<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\InsurancePolicy;
use Doctrine\ORM\EntityManagerInterface;
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
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des sinistres',
        responses: [
            new OA\Response(response: 200, description: 'Liste des sinistres retournée')
        ]
    )]
    public function list(): JsonResponse
    {
        // Simplified implementation - in real app, would query Claim entity
        return $this->json([
            'claims' => [],
            'message' => 'Claims feature not yet implemented'
        ], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Détail d\'un sinistre',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sinistre trouvé'),
            new OA\Response(response: 404, description: 'Sinistre non trouvé')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        return $this->json([
            'id' => $id,
            'message' => 'Claim details feature not yet implemented'
        ], Response::HTTP_OK);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Déclarer un sinistre',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'policyId', type: 'integer'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'incidentDate', type: 'string', format: 'date')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Sinistre créé'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $policy = $this->entityManager->getRepository(InsurancePolicy::class)->find($data['policyId'] ?? 0);
        
        if (!$policy) {
            return $this->json(['error' => 'Policy not found'], Response::HTTP_NOT_FOUND);
        }
        
        // Simplified implementation - in real app, would create Claim entity
        return $this->json([
            'message' => 'Claim created (simplified)',
            'policyId' => $policy->getId(),
            'description' => $data['description'] ?? '',
            'incidentDate' => $data['incidentDate'] ?? date('Y-m-d')
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}/status', name: 'update_status', methods: ['PATCH'])]
    #[OA\Patch(
        summary: 'Mettre à jour le statut d\'un sinistre',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'status', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Statut mis à jour'),
            new OA\Response(response: 404, description: 'Sinistre non trouvé')
        ]
    )]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        return $this->json([
            'id' => $id,
            'status' => $data['status'] ?? 'pending',
            'message' => 'Status update feature not yet implemented'
        ], Response::HTTP_OK);
    }
}
