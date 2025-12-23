<?php

declare(strict_types=1);

namespace App\Service\Reporting;

use App\DTO\Response\ReportResponse;

class ReportGenerator
{
    public function __construct(
        private DataAggregator $dataAggregator,
        private ReportFormatter $reportFormatter
    ) {
    }

    public function generateReport(
        int $customerId,
        string $reportType,
        string $format,
        ?array $dateRange,
        ?array $filters
    ): ReportResponse {
        // Aggregate data based on report type
        $data = match ($reportType) {
            'financial_summary' => $this->generateFinancialSummary($customerId, $dateRange),
            'transaction_history' => $this->generateTransactionHistory($customerId, $dateRange, $filters),
            'account_statement' => $this->generateAccountStatement($customerId, $dateRange),
            'balance_sheet' => $this->generateBalanceSheet($customerId, $dateRange),
            default => throw new \InvalidArgumentException("Unknown report type: {$reportType}"),
        };

        // Format the data
        $formattedData = $this->reportFormatter->format($data, $format, $reportType);

        // Generate report ID
        $reportId = $this->generateReportId($customerId, $reportType);

        $metadata = [
            'date_range' => $dateRange,
            'filters' => $filters,
            'page_count' => $format === 'pdf' ? rand(1, 10) : null,
        ];

        return new ReportResponse(
            $reportId,
            $customerId,
            $reportType,
            $format,
            $formattedData,
            date('Y-m-d H:i:s'),
            $metadata
        );
    }

    private function generateFinancialSummary(int $customerId, ?array $dateRange): array
    {
        $financialData = $this->dataAggregator->aggregateFinancialData($customerId, $dateRange);
        
        return [
            'summary' => [
                'total_income' => $financialData['income'],
                'total_expenses' => $financialData['expenses'],
                'net_cash_flow' => $financialData['net_cash_flow'],
                'current_balance' => $financialData['balance'],
            ],
            'breakdown' => [
                'income_sources' => [
                    ['category' => 'Salary', 'amount' => $financialData['income'] * 0.8],
                    ['category' => 'Investments', 'amount' => $financialData['income'] * 0.2],
                ],
                'expense_categories' => [
                    ['category' => 'Housing', 'amount' => $financialData['expenses'] * 0.4],
                    ['category' => 'Food', 'amount' => $financialData['expenses'] * 0.3],
                    ['category' => 'Other', 'amount' => $financialData['expenses'] * 0.3],
                ],
            ],
        ];
    }

    private function generateTransactionHistory(int $customerId, ?array $dateRange, ?array $filters): array
    {
        $aggregatedData = $this->dataAggregator->aggregateCustomerData($customerId, $dateRange, $filters);
        
        return [
            'transactions' => $aggregatedData['transactions'],
            'summary' => $aggregatedData['summary'],
        ];
    }

    private function generateAccountStatement(int $customerId, ?array $dateRange): array
    {
        $aggregatedData = $this->dataAggregator->aggregateCustomerData($customerId, $dateRange, null);
        
        return [
            'account_info' => $aggregatedData['accounts'][0] ?? [],
            'transactions' => $aggregatedData['transactions'],
            'opening_balance' => rand(1000, 5000),
            'closing_balance' => $aggregatedData['summary']['total_balance'] ?? 0,
        ];
    }

    private function generateBalanceSheet(int $customerId, ?array $dateRange): array
    {
        $financialData = $this->dataAggregator->aggregateFinancialData($customerId, $dateRange);
        
        return [
            'assets' => [
                'cash' => $financialData['balance'],
                'investments' => rand(10000, 50000),
                'total_assets' => $financialData['balance'] + rand(10000, 50000),
            ],
            'liabilities' => [
                'loans' => rand(5000, 20000),
                'credit_cards' => rand(1000, 5000),
                'total_liabilities' => rand(6000, 25000),
            ],
            'equity' => [
                'net_worth' => rand(15000, 60000),
            ],
        ];
    }

    private function generateReportId(int $customerId, string $reportType): string
    {
        return sprintf(
            'RPT-%d-%s-%s',
            $customerId,
            strtoupper(substr($reportType, 0, 3)),
            date('YmdHis')
        );
    }
}
