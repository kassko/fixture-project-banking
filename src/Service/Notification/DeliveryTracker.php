<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Enum\NotificationStatus;

class DeliveryTracker
{
    private array $deliveryStatus = [];

    public function trackDelivery(string $notificationId, string $status, ?array $details = null): array
    {
        $timestamp = date('Y-m-d H:i:s');
        
        if (!isset($this->deliveryStatus[$notificationId])) {
            $this->deliveryStatus[$notificationId] = [
                'notification_id' => $notificationId,
                'history' => [],
            ];
        }

        $this->deliveryStatus[$notificationId]['history'][] = [
            'status' => $status,
            'timestamp' => $timestamp,
            'details' => $details,
        ];

        $this->deliveryStatus[$notificationId]['current_status'] = $status;
        $this->deliveryStatus[$notificationId]['last_updated'] = $timestamp;

        return $this->deliveryStatus[$notificationId];
    }

    public function getDeliveryStatus(string $notificationId): ?array
    {
        return $this->deliveryStatus[$notificationId] ?? null;
    }

    public function markAsDelivered(string $notificationId): array
    {
        return $this->trackDelivery($notificationId, NotificationStatus::DELIVERED->value, [
            'delivery_confirmed' => true,
        ]);
    }

    public function markAsFailed(string $notificationId, string $reason): array
    {
        return $this->trackDelivery($notificationId, NotificationStatus::FAILED->value, [
            'failure_reason' => $reason,
        ]);
    }

    public function markAsRead(string $notificationId): array
    {
        return $this->trackDelivery($notificationId, NotificationStatus::READ->value, [
            'read_confirmed' => true,
        ]);
    }

    public function getDeliveryMetrics(int $customerId): array
    {
        // Simulate delivery metrics for a customer
        // In production, this would aggregate from database
        
        $totalSent = rand(100, 500);
        $delivered = (int)($totalSent * 0.95);
        $failed = $totalSent - $delivered;
        $read = (int)($delivered * 0.70);

        return [
            'customer_id' => $customerId,
            'total_sent' => $totalSent,
            'delivered' => $delivered,
            'failed' => $failed,
            'read' => $read,
            'delivery_rate' => round(($delivered / $totalSent) * 100, 2),
            'read_rate' => round(($read / $delivered) * 100, 2),
            'calculated_at' => date('Y-m-d H:i:s'),
        ];
    }

    public function getChannelPerformance(): array
    {
        // Simulate channel performance metrics
        return [
            'EMAIL' => [
                'total_sent' => rand(1000, 5000),
                'delivered' => rand(900, 4500),
                'failed' => rand(50, 300),
                'avg_delivery_time' => rand(2, 10) . ' seconds',
                'delivery_rate' => rand(90, 98) . '%',
            ],
            'SMS' => [
                'total_sent' => rand(500, 2000),
                'delivered' => rand(475, 1900),
                'failed' => rand(10, 100),
                'avg_delivery_time' => rand(1, 5) . ' seconds',
                'delivery_rate' => rand(92, 99) . '%',
            ],
            'PUSH' => [
                'total_sent' => rand(800, 3000),
                'delivered' => rand(680, 2550),
                'failed' => rand(50, 450),
                'avg_delivery_time' => 'instant',
                'delivery_rate' => rand(85, 95) . '%',
            ],
            'IN_APP' => [
                'total_sent' => rand(600, 2500),
                'delivered' => rand(594, 2475),
                'failed' => rand(5, 25),
                'avg_delivery_time' => 'instant',
                'delivery_rate' => rand(98, 99) . '%',
            ],
        ];
    }
}
