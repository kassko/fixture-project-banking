<?php

declare(strict_types=1);

namespace App\DTO\Request;

class ReportRequest
{
    public function __construct(
        private int $customerId,
        private string $reportType,
        private string $format = 'json',
        private ?array $dateRange = null,
        private ?array $filters = null,
        private ?string $templateId = null
    ) {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getReportType(): string
    {
        return $this->reportType;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getDateRange(): ?array
    {
        return $this->dateRange;
    }

    public function getFilters(): ?array
    {
        return $this->filters;
    }

    public function getTemplateId(): ?string
    {
        return $this->templateId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['customerId'] ?? 0,
            $data['reportType'] ?? 'financial_summary',
            $data['format'] ?? 'json',
            $data['dateRange'] ?? null,
            $data['filters'] ?? null,
            $data['templateId'] ?? null
        );
    }
}
