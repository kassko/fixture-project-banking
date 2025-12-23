<?php

declare(strict_types=1);

namespace App\DTO\Request;

class NotificationRequest
{
    public function __construct(
        private int $customerId,
        private string $channel,
        private string $subject,
        private string $message,
        private ?string $templateId = null,
        private ?array $templateVariables = null,
        private ?string $scheduleAt = null,
        private ?array $metadata = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public function getTemplateVariables(): ?array
    {
        return $this->templateVariables;
    }

    public function getScheduleAt(): ?string
    {
        return $this->scheduleAt;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['channel'] ?? '',
            $data['subject'] ?? '',
            $data['message'] ?? '',
            $data['templateId'] ?? null,
            $data['templateVariables'] ?? null,
            $data['scheduleAt'] ?? null,
            $data['metadata'] ?? null
        );
    }
}
