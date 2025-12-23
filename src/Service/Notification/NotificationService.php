<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\DTO\Request\NotificationRequest;
use App\DTO\Response\NotificationResponse;
use App\Enum\NotificationStatus;
use App\Repository\CustomerRepository;

class NotificationService
{
    private const QUIET_HOURS_END_TIME = '08:00:00';
    
    private array $notifications = [];

    public function __construct(
        private CustomerRepository $customerRepository,
        private NotificationSender $notificationSender,
        private NotificationScheduler $notificationScheduler,
        private TemplateManager $templateManager,
        private PreferenceManager $preferenceManager,
        private DeliveryTracker $deliveryTracker
    ) {
    }

    public function sendNotification(NotificationRequest $request): NotificationResponse
    {
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Check if channel is enabled for customer
        if (!$this->preferenceManager->isChannelEnabled($request->getCustomerId(), $request->getChannel())) {
            throw new \RuntimeException("Channel {$request->getChannel()} is disabled for customer");
        }

        // Check quiet hours
        if ($this->preferenceManager->isInQuietHours($request->getCustomerId())) {
            // Schedule for later instead of sending immediately
            return $this->scheduleForLater($request);
        }

        // Prepare notification content
        $subject = $request->getSubject();
        $message = $request->getMessage();

        // If using template, render it
        if ($request->getTemplateId()) {
            $rendered = $this->templateManager->renderTemplate(
                $request->getTemplateId(),
                $request->getTemplateVariables() ?? []
            );
            $subject = $rendered['subject'];
            $message = $rendered['body'];
        }

        // Generate notification ID
        $notificationId = $this->generateNotificationId($request->getCustomerId());

        // Send notification
        $response = $this->notificationSender->send(
            $notificationId,
            $request->getCustomerId(),
            $request->getChannel(),
            $subject,
            $message,
            $request->getMetadata() ?? []
        );

        // Store notification
        $this->notifications[$notificationId] = $response;

        // Track delivery
        $this->deliveryTracker->trackDelivery(
            $notificationId,
            $response->getStatus(),
            ['initial_send' => true]
        );

        return $response;
    }

    public function sendBulk(array $requests): array
    {
        $results = [];

        foreach ($requests as $requestData) {
            try {
                $request = NotificationRequest::fromArray($requestData);
                $results[] = $this->sendNotification($request);
            } catch (\Exception $e) {
                $results[] = [
                    'error' => $e->getMessage(),
                    'customer_id' => $requestData['customerId'] ?? null,
                ];
            }
        }

        return $results;
    }

    public function getNotification(string $notificationId): ?NotificationResponse
    {
        return $this->notifications[$notificationId] ?? null;
    }

    public function getCustomerNotifications(int $customerId): array
    {
        return array_filter(
            $this->notifications,
            fn($notification) => $notification->getCustomerId() === $customerId
        );
    }

    public function scheduleNotification(NotificationRequest $request): array
    {
        if (!$request->getScheduleAt()) {
            throw new \InvalidArgumentException('scheduleAt is required for scheduling');
        }

        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        // Prepare notification content
        $subject = $request->getSubject();
        $message = $request->getMessage();

        // Schedule the notification
        $scheduled = $this->notificationScheduler->schedule(
            $request->getCustomerId(),
            $request->getChannel(),
            $subject,
            $message,
            $request->getScheduleAt(),
            $request->getTemplateId(),
            $request->getMetadata()
        );

        return $scheduled;
    }

    public function cancelScheduled(string $scheduleId): bool
    {
        $cancelled = $this->notificationScheduler->cancel($scheduleId);
        
        if (!$cancelled) {
            throw new \RuntimeException('Scheduled notification not found');
        }

        return true;
    }

    public function getNotificationStatus(string $notificationId): ?array
    {
        return $this->deliveryTracker->getDeliveryStatus($notificationId);
    }

    private function scheduleForLater(NotificationRequest $request): NotificationResponse
    {
        // Schedule for 8 AM next day (end of quiet hours)
        $tomorrow8am = date('Y-m-d ' . self::QUIET_HOURS_END_TIME, strtotime('+1 day'));
        
        $scheduled = $this->notificationScheduler->schedule(
            $request->getCustomerId(),
            $request->getChannel(),
            $request->getSubject(),
            $request->getMessage(),
            $tomorrow8am,
            $request->getTemplateId(),
            $request->getMetadata()
        );

        $notificationId = $scheduled['schedule_id'];

        // Return a pending response
        return new NotificationResponse(
            $notificationId,
            $request->getCustomerId(),
            $request->getChannel(),
            NotificationStatus::PENDING->value,
            $request->getSubject(),
            $request->getMessage(),
            date('Y-m-d H:i:s'),
            null,
            null,
            null,
            array_merge($request->getMetadata() ?? [], [
                'scheduled_for' => $tomorrow8am,
                'reason' => 'quiet_hours',
            ])
        );
    }

    private function generateNotificationId(int $customerId): string
    {
        return sprintf('NOTIF-%d-%s', $customerId, uniqid());
    }
}
