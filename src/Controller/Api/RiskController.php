<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\RiskAssessmentRequest;
use App\Service\Risk\RiskAssessmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/risk', name: 'api_risk_')]
#[OA\Tag(name: 'Risk Assessment')]
class RiskController extends AbstractController
{
    public function __construct(
        private RiskAssessmentService $riskAssessmentService
    ) {
    }

    #[Route('/assess', name: 'assess', methods: ['POST'])]
    #[OA\Post(
        summary: 'Lancer une évaluation de risque complète',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'includeFactors',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['credit_score', 'income_stability', 'debt_ratio']
                    ),
                    new OA\Property(property: 'generateReport', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Évaluation de risque complète'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function assess(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $riskRequest = RiskAssessmentRequest::fromArray($data);
            
            $response = $this->riskAssessmentService->assess($riskRequest);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/score/{customerId}', name: 'score', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le score de risque d\'un client',
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
            new OA\Response(response: 200, description: 'Score de risque calculé'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getScore(int $customerId): JsonResponse
    {
        try {
            $score = $this->riskAssessmentService->getScore($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'risk_score' => $score,
                'scale' => '0-100 (higher is better)',
                'calculated_at' => date('Y-m-d H:i:s'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/report/{customerId}', name: 'report', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le rapport de risque détaillé d\'un client',
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
            new OA\Response(response: 200, description: 'Rapport de risque détaillé'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getReport(int $customerId): JsonResponse
    {
        try {
            $request = new RiskAssessmentRequest($customerId, null, true);
            $response = $this->riskAssessmentService->assess($request);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/factors/{customerId}', name: 'factors', methods: ['GET'])]
    #[OA\Get(
        summary: 'Analyser les facteurs de risque d\'un client',
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
            new OA\Response(response: 200, description: 'Analyse des facteurs de risque'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getFactors(int $customerId): JsonResponse
    {
        try {
            $factors = $this->riskAssessmentService->getFactors($customerId);
            
            return $this->json($factors, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/classification/{customerId}', name: 'classification', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir la classification de risque d\'un client',
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
            new OA\Response(response: 200, description: 'Classification de risque avec détails'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getClassification(int $customerId): JsonResponse
    {
        try {
            $classification = $this->riskAssessmentService->getClassification($customerId);
            
            return $this->json($classification, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
