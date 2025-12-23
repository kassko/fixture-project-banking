<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ReportConfiguration
{
    public function __construct(
        private string $scheduleId,
        private int $customerId,
        private string $reportType,
        private string $frequency,
        private string $format,
        private ?string $nextRunDate = null,
        private ?array $filters = null,
        private string $status = 'active'
    ) {
    }

    public function getScheduleId(): string
    {
        return $this->scheduleId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'schedule_id' => $this->scheduleId,
            'customer_id' => $this->customerId,
            'report_type' => $this->reportType,
            'frequency' => $this->frequency,
            'format' => $this->format,
            'next_run_date' => $this->nextRunDate,
            'filters' => $this->filters,
            'status' => $this->status,
        ];
    }
}
