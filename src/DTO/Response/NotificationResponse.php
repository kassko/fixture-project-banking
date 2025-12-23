<?php

declare(strict_types=1);

namespace App\DTO\Response;

class NotificationResponse
{
    public function __construct(
        private string $notificationId,
        private int $customerId,
        private string $channel,
        private string $status,
        private string $subject,
        private string $message,
        private string $createdAt,
        private ?string $sentAt = null,
        private ?string $deliveredAt = null,
        private ?string $readAt = null,
        private ?array $metadata = null
    ) {
    }

    public function getNotificationId(): string
    {
        return $this->notificationId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        $result = [
            'notification_id' => $this->notificationId,
            'customer_id' => $this->customerId,
            'channel' => $this->channel,
            'status' => $this->status,
            'subject' => $this->subject,
            'message' => $this->message,
            'created_at' => $this->createdAt,
        ];

        if ($this->sentAt !== null) {
            $result['sent_at'] = $this->sentAt;
        }

        if ($this->deliveredAt !== null) {
            $result['delivered_at'] = $this->deliveredAt;
        }

        if ($this->readAt !== null) {
            $result['read_at'] = $this->readAt;
        }

        if ($this->metadata !== null) {
            $result['metadata'] = $this->metadata;
        }

        return $result;
    }
}
