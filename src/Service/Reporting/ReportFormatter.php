<?php

declare(strict_types=1);

namespace App\Service\Reporting;

class ReportFormatter
{
    public function format(array $data, string $format, string $reportType): mixed
    {
        return match ($format) {
            'json' => $this->formatAsJson($data),
            'pdf' => $this->formatAsPdf($data, $reportType),
            'csv' => $this->formatAsCsv($data),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };
    }

    private function formatAsJson(array $data): array
    {
        // Data is already in array format, ready for JSON
        return $data;
    }

    private function formatAsPdf(array $data, string $reportType): array
    {
        // Simulate PDF generation
        // In a real implementation, this would use a PDF library
        return [
            'format' => 'pdf',
            'content' => 'base64_encoded_pdf_content_here',
            'filename' => sprintf('%s_report_%s.pdf', $reportType, date('Y-m-d')),
            'data_summary' => $this->extractSummary($data),
        ];
    }

    private function formatAsCsv(array $data): array
    {
        // Simulate CSV generation
        // In a real implementation, this would generate actual CSV content
        $csvRows = [];
        
        if (isset($data['transactions'])) {
            $csvRows[] = 'Date,Amount,Type,Description';
            foreach ($data['transactions'] as $tx) {
                $csvRows[] = sprintf(
                    '%s,%s,%s,%s',
                    $tx['date'],
                    $tx['amount'],
                    $tx['type'],
                    $tx['description']
                );
            }
        }
        
        return [
            'format' => 'csv',
            'content' => implode("\n", $csvRows),
            'filename' => sprintf('report_%s.csv', date('Y-m-d')),
        ];
    }

    private function extractSummary(array $data): array
    {
        $summary = [];
        
        if (isset($data['summary'])) {
            $summary = $data['summary'];
        }
        
        if (isset($data['transactions'])) {
            $summary['transaction_count'] = count($data['transactions']);
        }
        
        return $summary;
    }

    public function formatReportList(array $reports): array
    {
        return array_map(function ($report) {
            return [
                'report_id' => $report['report_id'],
                'report_type' => $report['report_type'],
                'format' => $report['format'],
                'generated_at' => $report['generated_at'],
            ];
        }, $reports);
    }
}
