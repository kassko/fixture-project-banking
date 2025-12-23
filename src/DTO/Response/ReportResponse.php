<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ReportResponse
{
    public function __construct(
        private string $reportId,
        private int $customerId,
        private string $reportType,
        private string $format,
        private array $data,
        private string $generatedAt,
        private ?array $metadata = null
    ) {
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'report_id' => $this->reportId,
            'customer_id' => $this->customerId,
            'report_type' => $this->reportType,
            'format' => $this->format,
            'data' => $this->data,
            'generated_at' => $this->generatedAt,
            'metadata' => $this->metadata,
        ];
    }
}
