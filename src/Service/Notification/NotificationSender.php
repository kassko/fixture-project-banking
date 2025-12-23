<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\DTO\Response\NotificationResponse;
use App\Enum\NotificationChannel;
use App\Enum\NotificationStatus;

class NotificationSender
{
    public function send(
        string $notificationId,
        int $customerId,
        string $channel,
        string $subject,
        string $message,
        array $metadata = []
    ): NotificationResponse {
        // Simulate sending notification via different channels
        $success = $this->sendViaChannel($channel, $customerId, $subject, $message, $metadata);
        
        $status = $success ? NotificationStatus::SENT->value : NotificationStatus::FAILED->value;
        $sentAt = $success ? date('Y-m-d H:i:s') : null;

        return new NotificationResponse(
            $notificationId,
            $customerId,
            $channel,
            $status,
            $subject,
            $message,
            date('Y-m-d H:i:s'),
            $sentAt,
            null,
            null,
            $metadata
        );
    }

    public function sendBulk(array $notifications): array
    {
        $results = [];

        foreach ($notifications as $notification) {
            $results[] = $this->send(
                $notification['notificationId'] ?? uniqid('NOTIF-', true),
                $notification['customerId'],
                $notification['channel'],
                $notification['subject'],
                $notification['message'],
                $notification['metadata'] ?? []
            );
        }

        return $results;
    }

    private function sendViaChannel(
        string $channel,
        int $customerId,
        string $subject,
        string $message,
        array $metadata
    ): bool {
        // Simulate sending logic for different channels
        // In production, this would integrate with email services, SMS gateways, etc.
        
        return match ($channel) {
            NotificationChannel::EMAIL->value => $this->sendEmail($customerId, $subject, $message, $metadata),
            NotificationChannel::SMS->value => $this->sendSms($customerId, $message, $metadata),
            NotificationChannel::PUSH->value => $this->sendPush($customerId, $subject, $message, $metadata),
            NotificationChannel::IN_APP->value => $this->sendInApp($customerId, $subject, $message, $metadata),
            default => false,
        };
    }

    private function sendEmail(int $customerId, string $subject, string $message, array $metadata): bool
    {
        // Simulate email sending (90% success rate)
        return rand(1, 10) <= 9;
    }

    private function sendSms(int $customerId, string $message, array $metadata): bool
    {
        // Simulate SMS sending (95% success rate)
        return rand(1, 20) <= 19;
    }

    private function sendPush(int $customerId, string $subject, string $message, array $metadata): bool
    {
        // Simulate push notification (85% success rate)
        return rand(1, 20) <= 17;
    }

    private function sendInApp(int $customerId, string $subject, string $message, array $metadata): bool
    {
        // Simulate in-app notification (99% success rate - always works if app is available)
        return rand(1, 100) <= 99;
    }
}
