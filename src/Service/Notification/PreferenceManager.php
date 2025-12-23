<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\DTO\Response\NotificationPreferences;

class PreferenceManager
{
    private array $preferences = [];

    public function getPreferences(int $customerId): NotificationPreferences
    {
        if (!isset($this->preferences[$customerId])) {
            // Return default preferences if not set
            return $this->createDefaultPreferences($customerId);
        }

        return $this->preferences[$customerId];
    }

    public function updatePreferences(int $customerId, array $data): NotificationPreferences
    {
        $current = $this->getPreferences($customerId);

        $preferences = new NotificationPreferences(
            $customerId,
            $data['email_enabled'] ?? $current->isEmailEnabled(),
            $data['sms_enabled'] ?? $current->isSmsEnabled(),
            $data['push_enabled'] ?? $current->isPushEnabled(),
            $data['in_app_enabled'] ?? $current->isInAppEnabled(),
            $data['preferred_channels'] ?? $current->getPreferredChannels(),
            $data['quiet_hours'] ?? $current->getQuietHours(),
            $data['category_preferences'] ?? $current->getCategoryPreferences()
        );

        $this->preferences[$customerId] = $preferences;

        return $preferences;
    }

    public function isChannelEnabled(int $customerId, string $channel): bool
    {
        $preferences = $this->getPreferences($customerId);

        return match ($channel) {
            'EMAIL' => $preferences->isEmailEnabled(),
            'SMS' => $preferences->isSmsEnabled(),
            'PUSH' => $preferences->isPushEnabled(),
            'IN_APP' => $preferences->isInAppEnabled(),
            default => false,
        };
    }

    public function isInQuietHours(int $customerId): bool
    {
        $preferences = $this->getPreferences($customerId);
        $quietHours = $preferences->getQuietHours();

        if (!$quietHours) {
            return false;
        }

        $currentHour = (int)date('H');
        $startHour = (int)($quietHours['start'] ?? 0);
        $endHour = (int)($quietHours['end'] ?? 0);

        if ($startHour < $endHour) {
            return $currentHour >= $startHour && $currentHour < $endHour;
        } else {
            // Quiet hours spanning midnight
            return $currentHour >= $startHour || $currentHour < $endHour;
        }
    }

    public function isCategoryEnabled(int $customerId, string $category): bool
    {
        $preferences = $this->getPreferences($customerId);
        $categoryPreferences = $preferences->getCategoryPreferences();

        if (!$categoryPreferences) {
            return true; // All categories enabled by default
        }

        return $categoryPreferences[$category] ?? true;
    }

    private function createDefaultPreferences(int $customerId): NotificationPreferences
    {
        $preferences = new NotificationPreferences(
            $customerId,
            true, // email enabled
            true, // sms enabled
            true, // push enabled
            true, // in-app enabled
            ['EMAIL', 'IN_APP'], // preferred channels
            ['start' => '22', 'end' => '08'], // quiet hours: 10 PM to 8 AM
            [
                'transaction' => true,
                'security' => true,
                'marketing' => false,
                'statement' => true,
                'payment' => true,
            ]
        );

        $this->preferences[$customerId] = $preferences;

        return $preferences;
    }
}
