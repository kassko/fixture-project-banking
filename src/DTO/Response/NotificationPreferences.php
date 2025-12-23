<?php

declare(strict_types=1);

namespace App\DTO\Response;

class NotificationPreferences
{
    public function __construct(
        private int $customerId,
        private bool $emailEnabled,
        private bool $smsEnabled,
        private bool $pushEnabled,
        private bool $inAppEnabled,
        private array $preferredChannels,
        private ?array $quietHours = null,
        private ?array $categoryPreferences = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function isEmailEnabled(): bool
    {
        return $this->emailEnabled;
    }

    public function isSmsEnabled(): bool
    {
        return $this->smsEnabled;
    }

    public function isPushEnabled(): bool
    {
        return $this->pushEnabled;
    }

    public function isInAppEnabled(): bool
    {
        return $this->inAppEnabled;
    }

    public function getPreferredChannels(): array
    {
        return $this->preferredChannels;
    }

    public function getQuietHours(): ?array
    {
        return $this->quietHours;
    }

    public function getCategoryPreferences(): ?array
    {
        return $this->categoryPreferences;
    }

    public function toArray(): array
    {
        $result = [
            'customer_id' => $this->customerId,
            'email_enabled' => $this->emailEnabled,
            'sms_enabled' => $this->smsEnabled,
            'push_enabled' => $this->pushEnabled,
            'in_app_enabled' => $this->inAppEnabled,
            'preferred_channels' => $this->preferredChannels,
        ];

        if ($this->quietHours !== null) {
            $result['quiet_hours'] = $this->quietHours;
        }

        if ($this->categoryPreferences !== null) {
            $result['category_preferences'] = $this->categoryPreferences;
        }

        return $result;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customer_id'] ?? 0,
            $data['email_enabled'] ?? true,
            $data['sms_enabled'] ?? true,
            $data['push_enabled'] ?? true,
            $data['in_app_enabled'] ?? true,
            $data['preferred_channels'] ?? ['EMAIL', 'IN_APP'],
            $data['quiet_hours'] ?? null,
            $data['category_preferences'] ?? null
        );
    }
}
