<?php

declare(strict_types=1);

namespace App\Service\Reporting;

use App\DTO\Request\ReportRequest;
use App\DTO\Response\ReportResponse;
use App\DTO\Response\ReportConfiguration;
use App\Repository\CustomerRepository;

class ReportingService
{
    private array $reports = [];

    public function __construct(
        private CustomerRepository $customerRepository,
        private ReportGenerator $reportGenerator,
        private ReportScheduler $reportScheduler,
        private ReportFormatter $reportFormatter
    ) {
    }

    public function generateReport(ReportRequest $request): ReportResponse
    {
        $customer = $this->customerRepository->find($request->getCustomerId());
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        $report = $this->reportGenerator->generateReport(
            $request->getCustomerId(),
            $request->getReportType(),
            $request->getFormat(),
            $request->getDateRange(),
            $request->getFilters()
        );

        // Store the report
        $this->reports[$report->getReportId()] = $report;

        return $report;
    }

    public function getReport(string $reportId): ?ReportResponse
    {
        return $this->reports[$reportId] ?? null;
    }

    public function getCustomerReports(int $customerId): array
    {
        $customerReports = array_filter($this->reports, function ($report) use ($customerId) {
            return $report->getCustomerId() === $customerId;
        });

        return $this->reportFormatter->formatReportList(
            array_map(fn($r) => $r->toArray(), $customerReports)
        );
    }

    public function scheduleReport(
        int $customerId,
        string $reportType,
        string $frequency,
        string $format,
        ?array $filters = null
    ): ReportConfiguration {
        $customer = $this->customerRepository->find($customerId);
        
        if (!$customer) {
            throw new \RuntimeException('Customer not found');
        }

        return $this->reportScheduler->scheduleReport(
            $customerId,
            $reportType,
            $frequency,
            $format,
            $filters
        );
    }

    public function getSchedules(int $customerId): array
    {
        return $this->reportScheduler->getCustomerSchedules($customerId);
    }

    public function cancelSchedule(string $scheduleId): bool
    {
        $cancelled = $this->reportScheduler->cancelSchedule($scheduleId);
        
        if (!$cancelled) {
            throw new \RuntimeException('Schedule not found');
        }

        return true;
    }

    public function getAvailableTemplates(): array
    {
        return [
            [
                'template_id' => 'financial_summary',
                'name' => 'Financial Summary Report',
                'description' => 'Complete overview of financial status',
                'supported_formats' => ['json', 'pdf', 'csv'],
            ],
            [
                'template_id' => 'transaction_history',
                'name' => 'Transaction History Report',
                'description' => 'Detailed list of all transactions',
                'supported_formats' => ['json', 'csv'],
            ],
            [
                'template_id' => 'account_statement',
                'name' => 'Account Statement',
                'description' => 'Monthly account statement',
                'supported_formats' => ['json', 'pdf'],
            ],
            [
                'template_id' => 'balance_sheet',
                'name' => 'Balance Sheet',
                'description' => 'Assets, liabilities, and equity overview',
                'supported_formats' => ['json', 'pdf'],
            ],
        ];
    }
}
