<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\FraudDetectionRequest;
use App\Service\Fraud\FraudDetectionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/fraud', name: 'api_fraud_')]
#[OA\Tag(name: 'Fraud Detection')]
class FraudController extends AbstractController
{
    public function __construct(
        private FraudDetectionService $fraudDetectionService
    ) {
    }

    #[Route('/analyze', name: 'analyze', methods: ['POST'])]
    #[OA\Post(
        summary: 'Analyser une transaction pour fraude',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['transactionId', 'customerId', 'amount', 'merchantCategory'],
                properties: [
                    new OA\Property(property: 'transactionId', type: 'integer', example: 12345),
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 1500.50),
                    new OA\Property(property: 'merchantCategory', type: 'string', example: 'RETAIL'),
                    new OA\Property(property: 'location', type: 'string', example: 'Paris'),
                    new OA\Property(
                        property: 'additionalData',
                        type: 'object',
                        example: ['merchant_name' => 'Store XYZ', 'card_present' => true]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Résultat de l\'analyse de fraude'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Transaction ou client non trouvé')
        ]
    )]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $fraudRequest = FraudDetectionRequest::fromArray($data);
            
            $response = $this->fraudDetectionService->analyzeTransaction($fraudRequest);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/score/{transactionId}', name: 'score', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le score de fraude d\'une transaction',
        parameters: [
            new OA\Parameter(
                name: 'transactionId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 12345
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Score de fraude calculé'),
            new OA\Response(response: 404, description: 'Transaction non trouvée')
        ]
    )]
    public function getScore(int $transactionId): JsonResponse
    {
        try {
            $score = $this->fraudDetectionService->getScore($transactionId);
            
            return $this->json([
                'transaction_id' => $transactionId,
                'fraud_score' => $score,
                'scale' => '0-100 (higher is more suspicious)',
                'calculated_at' => date('Y-m-d H:i:s'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/alerts/{customerId}', name: 'alerts', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des alertes de fraude d\'un client',
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
            new OA\Response(response: 200, description: 'Liste des alertes de fraude'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getAlerts(int $customerId): JsonResponse
    {
        try {
            $alerts = $this->fraudDetectionService->getCustomerAlerts($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'alerts' => array_map(fn($alert) => $alert->toArray(), $alerts),
                'total_count' => count($alerts),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/report', name: 'report', methods: ['POST'])]
    #[OA\Post(
        summary: 'Signaler une fraude suspectée',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['transactionId', 'customerId', 'reason'],
                properties: [
                    new OA\Property(property: 'transactionId', type: 'integer', example: 12345),
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'reason', type: 'string', example: 'Unauthorized transaction')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rapport de fraude créé'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function report(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['transactionId']) || !isset($data['customerId']) || !isset($data['reason'])) {
                throw new \InvalidArgumentException('transactionId, customerId and reason are required');
            }

            $report = $this->fraudDetectionService->reportFraud(
                $data['transactionId'],
                $data['customerId'],
                $data['reason']
            );
            
            return $this->json($report, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/patterns/{customerId}', name: 'patterns', methods: ['GET'])]
    #[OA\Get(
        summary: 'Analyser les patterns de comportement d\'un client',
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
            new OA\Response(response: 200, description: 'Analyse des patterns de comportement'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getPatterns(int $customerId): JsonResponse
    {
        try {
            $patterns = $this->fraudDetectionService->getPatterns($customerId);
            
            return $this->json($patterns, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/alerts/{alertId}/resolve', name: 'resolve_alert', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Résoudre une alerte de fraude',
        parameters: [
            new OA\Parameter(
                name: 'alertId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 123
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['resolution'],
                properties: [
                    new OA\Property(property: 'resolution', type: 'string', example: 'False positive - customer verified transaction')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Alerte résolue'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Alerte non trouvée')
        ]
    )]
    public function resolveAlert(int $alertId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['resolution'])) {
                throw new \InvalidArgumentException('resolution is required');
            }

            $result = $this->fraudDetectionService->resolveAlert($alertId, $data['resolution']);
            
            return $this->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
