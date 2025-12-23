<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\ReportRequest;
use App\Service\Reporting\ReportingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/reports', name: 'api_reports_')]
#[OA\Tag(name: 'Reporting')]
class ReportingController extends AbstractController
{
    public function __construct(
        private ReportingService $reportingService
    ) {
    }

    #[Route('/generate', name: 'generate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Générer un nouveau rapport',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'reportType'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'reportType',
                        type: 'string',
                        enum: ['financial_summary', 'transaction_history', 'account_statement', 'balance_sheet'],
                        example: 'financial_summary'
                    ),
                    new OA\Property(
                        property: 'format',
                        type: 'string',
                        enum: ['json', 'pdf', 'csv'],
                        example: 'json'
                    ),
                    new OA\Property(
                        property: 'dateRange',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'start', type: 'string', format: 'date', example: '2024-01-01'),
                            new OA\Property(property: 'end', type: 'string', format: 'date', example: '2024-12-31')
                        ]
                    ),
                    new OA\Property(
                        property: 'filters',
                        type: 'object',
                        example: ['account_type' => 'checking', 'min_amount' => 100]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rapport généré avec succès'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function generate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $reportRequest = ReportRequest::fromArray($data);
            
            $response = $this->reportingService->generateReport($reportRequest);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{reportId}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir un rapport généré',
        parameters: [
            new OA\Parameter(
                name: 'reportId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'RPT-1-FIN-20241223140530'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails du rapport'),
            new OA\Response(response: 404, description: 'Rapport non trouvé')
        ]
    )]
    public function getReport(string $reportId): JsonResponse
    {
        try {
            $report = $this->reportingService->getReport($reportId);
            
            if (!$report) {
                throw new \RuntimeException('Report not found');
            }
            
            return $this->json($report->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/customer/{customerId}', name: 'customer_reports', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des rapports d\'un client',
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
            new OA\Response(response: 200, description: 'Liste des rapports du client'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getCustomerReports(int $customerId): JsonResponse
    {
        try {
            $reports = $this->reportingService->getCustomerReports($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'reports' => $reports,
                'total_count' => count($reports),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/schedule', name: 'schedule', methods: ['POST'])]
    #[OA\Post(
        summary: 'Planifier un rapport récurrent',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'reportType', 'frequency', 'format'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'reportType',
                        type: 'string',
                        enum: ['financial_summary', 'transaction_history', 'account_statement', 'balance_sheet'],
                        example: 'financial_summary'
                    ),
                    new OA\Property(
                        property: 'frequency',
                        type: 'string',
                        enum: ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'],
                        example: 'monthly'
                    ),
                    new OA\Property(
                        property: 'format',
                        type: 'string',
                        enum: ['json', 'pdf', 'csv'],
                        example: 'pdf'
                    ),
                    new OA\Property(
                        property: 'filters',
                        type: 'object',
                        example: ['account_type' => 'checking']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rapport planifié avec succès'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function schedule(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['customerId']) || !isset($data['reportType']) || 
                !isset($data['frequency']) || !isset($data['format'])) {
                throw new \InvalidArgumentException('customerId, reportType, frequency and format are required');
            }

            $config = $this->reportingService->scheduleReport(
                $data['customerId'],
                $data['reportType'],
                $data['frequency'],
                $data['format'],
                $data['filters'] ?? null
            );
            
            return $this->json($config->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/templates', name: 'templates', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des templates de rapports disponibles',
        responses: [
            new OA\Response(response: 200, description: 'Liste des templates de rapports')
        ]
    )]
    public function getTemplates(): JsonResponse
    {
        try {
            $templates = $this->reportingService->getAvailableTemplates();
            
            return $this->json([
                'templates' => $templates,
                'total_count' => count($templates),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/schedule/{scheduleId}', name: 'cancel_schedule', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Annuler un rapport planifié',
        parameters: [
            new OA\Parameter(
                name: 'scheduleId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'SCH-1-FIN-abc123'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Planification annulée'),
            new OA\Response(response: 404, description: 'Planification non trouvée')
        ]
    )]
    public function cancelSchedule(string $scheduleId): JsonResponse
    {
        try {
            $this->reportingService->cancelSchedule($scheduleId);
            
            return $this->json([
                'schedule_id' => $scheduleId,
                'status' => 'cancelled',
                'cancelled_at' => date('Y-m-d H:i:s'),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
