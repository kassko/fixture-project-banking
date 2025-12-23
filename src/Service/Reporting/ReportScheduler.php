<?php

declare(strict_types=1);

namespace App\Service\Reporting;

use App\DTO\Response\ReportConfiguration;

class ReportScheduler
{
    // NOTE: In-memory storage for demonstration purposes only.
    // In production, this should be replaced with a proper repository.
    private array $schedules = [];

    public function scheduleReport(
        int $customerId,
        string $reportType,
        string $frequency,
        string $format,
        ?array $filters = null
    ): ReportConfiguration {
        $scheduleId = $this->generateScheduleId($customerId, $reportType);
        $nextRunDate = $this->calculateNextRunDate($frequency);
        
        $config = new ReportConfiguration(
            $scheduleId,
            $customerId,
            $reportType,
            $frequency,
            $format,
            $nextRunDate,
            $filters,
            'active'
        );

        $this->schedules[$scheduleId] = $config;
        
        return $config;
    }

    public function getSchedule(string $scheduleId): ?ReportConfiguration
    {
        return $this->schedules[$scheduleId] ?? null;
    }

    public function cancelSchedule(string $scheduleId): bool
    {
        if (isset($this->schedules[$scheduleId])) {
            unset($this->schedules[$scheduleId]);
            return true;
        }
        
        return false;
    }

    public function getCustomerSchedules(int $customerId): array
    {
        return array_filter($this->schedules, function ($config) use ($customerId) {
            return $config->getCustomerId() === $customerId;
        });
    }

    private function generateScheduleId(int $customerId, string $reportType): string
    {
        return sprintf(
            'SCH-%d-%s-%s',
            $customerId,
            strtoupper(substr($reportType, 0, 3)),
            uniqid()
        );
    }

    private function calculateNextRunDate(string $frequency): string
    {
        $interval = match ($frequency) {
            'daily' => '+1 day',
            'weekly' => '+1 week',
            'monthly' => '+1 month',
            'quarterly' => '+3 months',
            'yearly' => '+1 year',
            default => '+1 month',
        };
        
        return date('Y-m-d H:i:s', strtotime($interval));
    }
}
