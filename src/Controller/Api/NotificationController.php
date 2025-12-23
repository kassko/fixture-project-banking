<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\DTO\Request\NotificationRequest;
use App\Service\Notification\NotificationService;
use App\Service\Notification\PreferenceManager;
use App\Service\Notification\TemplateManager;
use App\Service\Notification\DeliveryTracker;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/v1/notifications', name: 'api_notifications_')]
#[OA\Tag(name: 'Notifications')]
class NotificationController extends AbstractController
{
    public function __construct(
        private NotificationService $notificationService,
        private PreferenceManager $preferenceManager,
        private TemplateManager $templateManager,
        private DeliveryTracker $deliveryTracker
    ) {
    }

    #[Route('/send', name: 'send', methods: ['POST'])]
    #[OA\Post(
        summary: 'Envoyer une notification',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'channel', 'subject', 'message'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'channel',
                        type: 'string',
                        enum: ['EMAIL', 'SMS', 'PUSH', 'IN_APP'],
                        example: 'EMAIL'
                    ),
                    new OA\Property(property: 'subject', type: 'string', example: 'Transaction Alert'),
                    new OA\Property(property: 'message', type: 'string', example: 'Your transaction has been completed.'),
                    new OA\Property(property: 'templateId', type: 'string', example: 'transaction_alert'),
                    new OA\Property(
                        property: 'templateVariables',
                        type: 'object',
                        example: ['amount' => '100.00', 'account_number' => '123456']
                    ),
                    new OA\Property(
                        property: 'metadata',
                        type: 'object',
                        example: ['priority' => 'high', 'category' => 'transaction']
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notification envoyée'),
            new OA\Response(response: 400, description: 'Données invalides'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function send(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $notificationRequest = NotificationRequest::fromArray($data);
            
            $response = $this->notificationService->sendNotification($notificationRequest);
            
            return $this->json($response->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/send/bulk', name: 'send_bulk', methods: ['POST'])]
    #[OA\Post(
        summary: 'Envoi groupé de notifications',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['notifications'],
                properties: [
                    new OA\Property(
                        property: 'notifications',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'customerId', type: 'integer', example: 1),
                                new OA\Property(property: 'channel', type: 'string', example: 'EMAIL'),
                                new OA\Property(property: 'subject', type: 'string', example: 'Message'),
                                new OA\Property(property: 'message', type: 'string', example: 'Hello')
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notifications envoyées'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function sendBulk(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['notifications']) || !is_array($data['notifications'])) {
                throw new \InvalidArgumentException('notifications array is required');
            }

            $results = $this->notificationService->sendBulk($data['notifications']);
            
            return $this->json([
                'results' => $results,
                'total_count' => count($results),
                'successful' => count(array_filter($results, fn($r) => !isset($r['error']))),
                'failed' => count(array_filter($results, fn($r) => isset($r['error']))),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{notificationId}', name: 'get', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir les détails d\'une notification',
        parameters: [
            new OA\Parameter(
                name: 'notificationId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'NOTIF-1-abc123'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Détails de la notification'),
            new OA\Response(response: 404, description: 'Notification non trouvée')
        ]
    )]
    public function getNotification(string $notificationId): JsonResponse
    {
        try {
            $notification = $this->notificationService->getNotification($notificationId);
            
            if (!$notification) {
                throw new \RuntimeException('Notification not found');
            }
            
            return $this->json($notification->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/customer/{customerId}', name: 'customer_history', methods: ['GET'])]
    #[OA\Get(
        summary: 'Historique des notifications d\'un client',
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
            new OA\Response(response: 200, description: 'Historique des notifications'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getCustomerHistory(int $customerId): JsonResponse
    {
        try {
            $notifications = $this->notificationService->getCustomerNotifications($customerId);
            
            return $this->json([
                'customer_id' => $customerId,
                'notifications' => array_map(fn($n) => $n->toArray(), $notifications),
                'total_count' => count($notifications),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/schedule', name: 'schedule', methods: ['POST'])]
    #[OA\Post(
        summary: 'Planifier une notification',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['customerId', 'channel', 'subject', 'message', 'scheduleAt'],
                properties: [
                    new OA\Property(property: 'customerId', type: 'integer', example: 1),
                    new OA\Property(property: 'channel', type: 'string', example: 'EMAIL'),
                    new OA\Property(property: 'subject', type: 'string', example: 'Scheduled Message'),
                    new OA\Property(property: 'message', type: 'string', example: 'This is scheduled.'),
                    new OA\Property(property: 'scheduleAt', type: 'string', format: 'date-time', example: '2024-12-25 10:00:00'),
                    new OA\Property(property: 'templateId', type: 'string', example: 'payment_reminder')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notification planifiée'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function schedule(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $notificationRequest = NotificationRequest::fromArray($data);
            
            $scheduled = $this->notificationService->scheduleNotification($notificationRequest);
            
            return $this->json($scheduled, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/schedule/{scheduleId}', name: 'cancel_schedule', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Annuler une notification planifiée',
        parameters: [
            new OA\Parameter(
                name: 'scheduleId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'SCHED-abc123'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Notification planifiée annulée'),
            new OA\Response(response: 404, description: 'Planification non trouvée')
        ]
    )]
    public function cancelSchedule(string $scheduleId): JsonResponse
    {
        try {
            $this->notificationService->cancelScheduled($scheduleId);
            
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

    #[Route('/preferences/{customerId}', name: 'get_preferences', methods: ['GET'])]
    #[OA\Get(
        summary: 'Obtenir les préférences d\'un client',
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
            new OA\Response(response: 200, description: 'Préférences du client'),
            new OA\Response(response: 404, description: 'Client non trouvé')
        ]
    )]
    public function getPreferences(int $customerId): JsonResponse
    {
        try {
            $preferences = $this->preferenceManager->getPreferences($customerId);
            
            return $this->json($preferences->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/preferences/{customerId}', name: 'update_preferences', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Mettre à jour les préférences',
        parameters: [
            new OA\Parameter(
                name: 'customerId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                example: 1
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'email_enabled', type: 'boolean', example: true),
                    new OA\Property(property: 'sms_enabled', type: 'boolean', example: true),
                    new OA\Property(property: 'push_enabled', type: 'boolean', example: false),
                    new OA\Property(property: 'in_app_enabled', type: 'boolean', example: true),
                    new OA\Property(
                        property: 'preferred_channels',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['EMAIL', 'IN_APP']
                    ),
                    new OA\Property(
                        property: 'quiet_hours',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'start', type: 'string', example: '22'),
                            new OA\Property(property: 'end', type: 'string', example: '08')
                        ]
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Préférences mises à jour'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function updatePreferences(int $customerId, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            $preferences = $this->preferenceManager->updatePreferences($customerId, $data);
            
            return $this->json($preferences->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/templates', name: 'get_templates', methods: ['GET'])]
    #[OA\Get(
        summary: 'Liste des templates disponibles',
        responses: [
            new OA\Response(response: 200, description: 'Liste des templates')
        ]
    )]
    public function getTemplates(): JsonResponse
    {
        try {
            $templates = $this->templateManager->getAllTemplates();
            
            return $this->json([
                'templates' => array_map(fn($t) => $t->toArray(), $templates),
                'total_count' => count($templates),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/templates', name: 'create_template', methods: ['POST'])]
    #[OA\Post(
        summary: 'Créer un nouveau template',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['name', 'description', 'channel', 'subject', 'body', 'variables', 'category'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Custom Template'),
                    new OA\Property(property: 'description', type: 'string', example: 'Template description'),
                    new OA\Property(property: 'channel', type: 'string', example: 'EMAIL'),
                    new OA\Property(property: 'subject', type: 'string', example: 'Subject {{variable}}'),
                    new OA\Property(property: 'body', type: 'string', example: 'Body with {{variable}}'),
                    new OA\Property(
                        property: 'variables',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['variable', 'customer_name']
                    ),
                    new OA\Property(property: 'category', type: 'string', example: 'marketing')
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Template créé'),
            new OA\Response(response: 400, description: 'Données invalides')
        ]
    )]
    public function createTemplate(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['name']) || !isset($data['description']) || !isset($data['channel']) ||
                !isset($data['subject']) || !isset($data['body']) || !isset($data['variables']) ||
                !isset($data['category'])) {
                throw new \InvalidArgumentException('All template fields are required');
            }

            $template = $this->templateManager->createTemplate(
                $data['name'],
                $data['description'],
                $data['channel'],
                $data['subject'],
                $data['body'],
                $data['variables'],
                $data['category']
            );
            
            return $this->json($template->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{notificationId}/status', name: 'get_status', methods: ['GET'])]
    #[OA\Get(
        summary: 'Statut de livraison',
        parameters: [
            new OA\Parameter(
                name: 'notificationId',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                example: 'NOTIF-1-abc123'
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Statut de livraison'),
            new OA\Response(response: 404, description: 'Notification non trouvée')
        ]
    )]
    public function getStatus(string $notificationId): JsonResponse
    {
        try {
            $status = $this->notificationService->getNotificationStatus($notificationId);
            
            if (!$status) {
                throw new \RuntimeException('Notification status not found');
            }
            
            return $this->json($status, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
