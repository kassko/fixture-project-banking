<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\CreditScoringRequest;
use App\Service\Credit\CreditScoringService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/credit', name: 'api_credit_')]
#[OA\Tag(name: 'Credit Scoring')]
class CreditScoringController extends AbstractController
{
    public function __construct(
        private CreditScoringService $creditScoringService
    ) {
    }

    #[Route('/score/{customerId}', name: 'score', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le score de crédit d\'un client',
        parameters: [
            new OA\Parameter(
                name: 'customerId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Score de crédit calculé'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getScore(int $customerId): JsonResponse
    {
        try {
            $score = $this->creditScoringService->getScore($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'credit_score' => $score,
                'scale' => '300-850',
                'calculated_at' => date('Y-m-d H:i:s'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/score/calculate', name: 'calculate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Calculer un score de crédit complet',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'criteriaToInclude',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['payment_history', 'credit_utilization', 'credit_history_length']
                    ),
                    new OA\Property(property: 'includeBreakdown', type: 'boolean', example: true),
                    new OA\Property(property: 'includeRecommendations', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Calcul de score de crédit complet'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function calculate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $scoringRequest = CreditScoringRequest::fromArray($data);
            
            $response = $this->creditScoringService->calculateScore($scoringRequest);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/score/{customerId}/breakdown', name: 'breakdown', methods: ['GET'])]
    #[OA\Get(
        summary: 'Détail des critères de scoring d\'un client',
        parameters: [
            new OA\Parameter(
                name: 'customerId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détail des critères de scoring'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getBreakdown(int $customerId): JsonResponse
    {
        try {
            $breakdown = $this->creditScoringService->getBreakdown($customerId);
            
            return $this->json($breakdown, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/score/simulate', name: 'simulate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Simuler l\'impact de changements sur le score',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'changes'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'changes',
                        type: 'object',
                        example: ['used_credit' => 2000, 'late_payments' => 0]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Simulation de l\'impact sur le score'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function simulate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['customerId']) || !isset($data['changes'])) {
                throw new \InvalidArgumentException('customerId and changes are required');
            }

            $simulation = $this->creditScoringService->simulateImpact(
                $data['customerId'],
                $data['changes']
            );
            
            return $this->json([
                'customer_id' => $data['customerId'],
                'simulation' => $simulation,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/score/{customerId}/recommendations', name: 'recommendations', methods: ['GET'])]
    #[OA\Get(
        summary: 'Recommandations d\'amélioration du score',
        parameters: [
            new OA\Parameter(
                name: 'customerId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Recommandations d\'amélioration du score de crédit'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getRecommendations(int $customerId): JsonResponse
    {
        try {
            $recommendations = $this->creditScoringService->getRecommendations($customerId);
            
            return $this->json($recommendations, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
