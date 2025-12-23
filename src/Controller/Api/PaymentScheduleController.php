<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\PaymentScheduleRequest;
use App\Service\Payment\PaymentSchedulingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/payments/schedule', name: 'api_payment_schedule_')]
#[OA\Tag(name: 'Payment Scheduling')]
class PaymentScheduleController extends AbstractController
{
    public function __construct(
        private PaymentSchedulingService $paymentSchedulingService
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un nouveau paiement planifié',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'amount', 'frequency', 'startDate'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 500.00),
                    new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                    new OA\Property(
                        property: 'frequency',
                        type: 'string',
                        enum: ['DAILY', 'WEEKLY', 'BIWEEKLY', 'MONTHLY', 'QUARTERLY', 'SEMIANNUAL', 'ANNUAL'],
                        example: 'MONTHLY'
                    ),
                    new OA\Property(property: 'startDate', type: 'string', format: 'date', example: '2024-01-15'),
                    new OA\Property(property: 'endDate', type: 'string', format: 'date', example: '2024-12-31'),
                    new OA\Property(property: 'occurrences', type: 'integer', example: 12),
                    new OA\Property(property: 'type', type: 'string', example: 'RECURRING'),
                    new OA\Property(property: 'description', type: 'string', example: 'Monthly rent payment')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Paiement planifié créé avec succès'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $scheduleRequest = PaymentScheduleRequest::fromArray($data);
            
            $response = $this->paymentSchedulingService->createSchedule($scheduleRequest);
            
            return $this->json($response->toArray(), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{scheduleId}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir les détails d\'un paiement planifié',
        parameters: [
            new OA\Parameter(
                name: 'scheduleId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'SCH-123456'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails du paiement planifié'),
            new OA\Response(response: 404, description: 'Paiement planifié non trouvé')
        ]
    )]
    public function get(string $scheduleId): JsonResponse
    {
        $schedule = $this->paymentSchedulingService->getSchedule($scheduleId);
        
        if (!$schedule) {
            return $this->json([
                'error' => 'Payment schedule not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return $this->json($schedule->toArray(), Response::HTTP_OK);
    }

    #[Route('/{scheduleId}', name: 'update', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Modifier un paiement planifié',
        parameters: [
            new OA\Parameter(
                name: 'scheduleId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'SCH-123456'
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'amount', 'frequency', 'startDate'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 600.00),
                    new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                    new OA\Property(
                        property: 'frequency',
                        type: 'string',
                        enum: ['DAILY', 'WEEKLY', 'BIWEEKLY', 'MONTHLY', 'QUARTERLY', 'SEMIANNUAL', 'ANNUAL'],
                        example: 'MONTHLY'
                    ),
                    new OA\Property(property: 'startDate', type: 'string', format: 'date', example: '2024-02-01'),
                    new OA\Property(property: 'endDate', type: 'string', format: 'date', example: '2024-12-31'),
                    new OA\Property(property: 'occurrences', type: 'integer', example: 11)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Paiement planifié mis à jour'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Paiement planifié non trouvé')
        ]
    )]
    public function update(string $scheduleId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $scheduleRequest = PaymentScheduleRequest::fromArray($data);
            
            $response = $this->paymentSchedulingService->updateSchedule($scheduleId, $scheduleRequest);
            
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

    #[Route('/{scheduleId}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Annuler un paiement planifié',
        parameters: [
            new OA\Parameter(
                name: 'scheduleId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'SCH-123456'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paiement planifié annulé'),
            new OA\Response(response: 404, description: 'Paiement planifié non trouvé')
        ]
    )]
    public function delete(string $scheduleId): JsonResponse
    {
        try {
            $result = $this->paymentSchedulingService->cancelSchedule($scheduleId);
            
            return $this->json($result, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/customer/{customerId}', name: 'by_customer', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des paiements planifiés d\'un client',
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
            new OA\Response(response: 200, description: 'Liste des paiements planifiés du client')
        ]
    )]
    public function getByCustomer(int $customerId): JsonResponse
    {
        $schedules = $this->paymentSchedulingService->getCustomerSchedules($customerId);
        
        return $this->json([
            'customer_id' => $customerId,
            'schedules' => array_map(fn($schedule) => $schedule->toArray(), $schedules),
            'total_schedules' => count($schedules),
        ], Response::HTTP_OK);
    }

    #[Route('/simulate', name: 'simulate', methods: ['POST'])]
    #[OA\Post(
        summary: 'Simuler un calendrier de paiements',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'amount', 'frequency', 'startDate'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'amount', type: 'number', example: 500.00),
                    new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                    new OA\Property(
                        property: 'frequency',
                        type: 'string',
                        enum: ['DAILY', 'WEEKLY', 'BIWEEKLY', 'MONTHLY', 'QUARTERLY', 'SEMIANNUAL', 'ANNUAL'],
                        example: 'MONTHLY'
                    ),
                    new OA\Property(property: 'startDate', type: 'string', format: 'date', example: '2024-01-15'),
                    new OA\Property(property: 'endDate', type: 'string', format: 'date', example: '2024-12-31'),
                    new OA\Property(property: 'occurrences', type: 'integer', example: 12)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Simulation du calendrier de paiements'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function simulate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $scheduleRequest = PaymentScheduleRequest::fromArray($data);
            
            $simulation = $this->paymentSchedulingService->simulateSchedule($scheduleRequest);
            
            return $this->json($simulation, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
