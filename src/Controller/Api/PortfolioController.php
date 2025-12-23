<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\PortfolioAnalysisRequest;
use App\Service\Portfolio\PortfolioAnalysisService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/portfolio', name: 'api_portfolio_')]
#[OA\Tag(name: 'Portfolio Analysis')]
class PortfolioController extends AbstractController
{
    public function __construct(
        private PortfolioAnalysisService $portfolioAnalysisService
    ) {
    }

    #[Route('/{customerId}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir le portefeuille d\'un client',
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
            new OA\Response(response: 200, description: 'Détails du portefeuille'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getPortfolio(int $customerId): JsonResponse
    {
        try {
            $portfolio = $this->portfolioAnalysisService->getPortfolio($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'portfolio' => $portfolio,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/analyze', name: 'analyze', methods: ['POST'])]
    #[OA\Post(
        summary: 'Lancer une analyse de portefeuille',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'assetTypes',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['STOCKS', 'BONDS', 'REAL_ESTATE']
                    ),
                    new OA\Property(property: 'benchmarkIndex', type: 'string', example: 'SP500'),
                    new OA\Property(property: 'includeOptimization', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Analyse de portefeuille complète'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function analyze(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $analysisRequest = PortfolioAnalysisRequest::fromArray($data);
            
            $response = $this->portfolioAnalysisService->analyzePortfolio($analysisRequest);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{customerId}/performance', name: 'performance', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir les performances d\'un portefeuille',
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
            new OA\Response(response: 200, description: 'Métriques de performance du portefeuille'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getPerformance(int $customerId): JsonResponse
    {
        try {
            $performance = $this->portfolioAnalysisService->getPerformance($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'performance' => $performance,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{customerId}/diversification', name: 'diversification', methods: ['GET'])]
    #[OA\Get(
        summary: 'Analyse de diversification d\'un portefeuille',
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
            new OA\Response(response: 200, description: 'Analyse de diversification'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getDiversification(int $customerId): JsonResponse
    {
        try {
            $diversification = $this->portfolioAnalysisService->getDiversification($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'diversification' => $diversification,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/{customerId}/allocation', name: 'allocation', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir l\'allocation d\'actifs recommandée',
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
            new OA\Response(response: 200, description: 'Allocation d\'actifs et recommandations'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getAllocation(int $customerId): JsonResponse
    {
        try {
            $allocation = $this->portfolioAnalysisService->getAllocation($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'allocation' => $allocation,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/optimize', name: 'optimize', methods: ['POST'])]
    #[OA\Post(
        summary: 'Suggérer une optimisation d\'allocation',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'assetTypes',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['STOCKS', 'BONDS']
                    ),
                    new OA\Property(property: 'benchmarkIndex', type: 'string', example: 'SP500')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Suggestions d\'optimisation'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function optimize(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $data['includeOptimization'] = true;
            
            $analysisRequest = PortfolioAnalysisRequest::fromArray($data);
            $response = $this->portfolioAnalysisService->analyzePortfolio($analysisRequest);
            
            return $this->json([
                'customer_id' => $response->getCustomerId(),
                'optimization_suggestions' => $response->getOptimizationSuggestions(),
                'allocation' => $response->getAllocation(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
