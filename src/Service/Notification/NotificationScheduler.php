<?php

declare(strict_types=1);

namespace App\Service\Notification;

class NotificationScheduler
{
    private array $scheduledNotifications = [];

    public function schedule(
        int $customerId,
        string $channel,
        string $subject,
        string $message,
        string $scheduleAt,
        ?string $templateId = null,
        ?array $metadata = null
    ): array {
        $scheduleId = uniqid('SCHED-', true);
        
        $scheduled = [
            'schedule_id' => $scheduleId,
            'customer_id' => $customerId,
            'channel' => $channel,
            'subject' => $subject,
            'message' => $message,
            'schedule_at' => $scheduleAt,
            'template_id' => $templateId,
            'status' => 'scheduled',
            'created_at' => date('Y-m-d H:i:s'),
            'metadata' => $metadata,
        ];

        $this->scheduledNotifications[$scheduleId] = $scheduled;

        return $scheduled;
    }

    public function cancel(string $scheduleId): bool
    {
        if (!isset($this->scheduledNotifications[$scheduleId])) {
            return false;
        }

        $this->scheduledNotifications[$scheduleId]['status'] = 'cancelled';
        return true;
    }

    public function getScheduled(string $scheduleId): ?array
    {
        return $this->scheduledNotifications[$scheduleId] ?? null;
    }

    public function getCustomerScheduled(int $customerId): array
    {
        return array_filter(
            $this->scheduledNotifications,
            fn($scheduled) => $scheduled['customer_id'] === $customerId
        );
    }

    public function getPendingNotifications(): array
    {
        $now = time();
        
        return array_filter(
            $this->scheduledNotifications,
            function ($scheduled) use ($now) {
                if ($scheduled['status'] !== 'scheduled') {
                    return false;
                }
                
                $scheduleTime = strtotime($scheduled['schedule_at']);
                return $scheduleTime <= $now;
            }
        );
    }
}
