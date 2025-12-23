<?php

declare(strict_types=1);

namespace App\Service\Claims;

use DateTimeImmutable;

class SlaCalculator
{
    private const SLA_TARGETS = [
        'INSURANCE_CLAIM' => [
            'first_response_hours' => 24,
            'resolution_days' => 15,
        ],
        'COMPLAINT' => [
            'first_response_hours' => 48,
            'resolution_days' => 10,
        ],
        'SERVICE_REQUEST' => [
            'first_response_hours' => 72,
            'resolution_days' => 5,
        ],
        'GENERAL' => [
            'first_response_hours' => 48,
            'resolution_days' => 7,
        ],
    ];

    public function calculateSlaMetrics(
        string $claimType,
        string $createdAt,
        ?string $firstResponseAt = null,
        ?string $resolvedAt = null
    ): array {
        $created = new DateTimeImmutable($createdAt);
        $now = new DateTimeImmutable();
        
        $targets = self::SLA_TARGETS[$claimType] ?? self::SLA_TARGETS['GENERAL'];
        
        // Calculate first response SLA
        $firstResponseSla = $this->calculateFirstResponseSla(
            $created,
            $firstResponseAt ? new DateTimeImmutable($firstResponseAt) : null,
            $targets['first_response_hours']
        );
        
        // Calculate resolution SLA
        $resolutionSla = $this->calculateResolutionSla(
            $created,
            $resolvedAt ? new DateTimeImmutable($resolvedAt) : null,
            $now,
            $targets['resolution_days']
        );
        
        return [
            'first_response' => $firstResponseSla,
            'resolution' => $resolutionSla,
            'overall_status' => $this->determineOverallStatus($firstResponseSla, $resolutionSla),
        ];
    }

    private function calculateFirstResponseSla(
        DateTimeImmutable $created,
        ?DateTimeImmutable $firstResponse,
        int $targetHours
    ): array {
        $targetDate = $created->modify("+{$targetHours} hours");
        
        if ($firstResponse === null) {
            $now = new DateTimeImmutable();
            $hoursElapsed = $this->calculateHoursDifference($created, $now);
            $isBreached = $now > $targetDate;
            
            return [
                'status' => $isBreached ? 'breached' : 'pending',
                'target_hours' => $targetHours,
                'hours_elapsed' => $hoursElapsed,
                'hours_remaining' => max(0, $targetHours - $hoursElapsed),
                'is_met' => false,
            ];
        }
        
        $hoursElapsed = $this->calculateHoursDifference($created, $firstResponse);
        $isMet = $firstResponse <= $targetDate;
        
        return [
            'status' => $isMet ? 'met' : 'breached',
            'target_hours' => $targetHours,
            'actual_hours' => $hoursElapsed,
            'is_met' => $isMet,
        ];
    }

    private function calculateResolutionSla(
        DateTimeImmutable $created,
        ?DateTimeImmutable $resolved,
        DateTimeImmutable $now,
        int $targetDays
    ): array {
        $targetDate = $created->modify("+{$targetDays} days");
        
        if ($resolved === null) {
            $daysElapsed = $created->diff($now)->days;
            $isBreached = $now > $targetDate;
            
            return [
                'status' => $isBreached ? 'breached' : 'on_track',
                'target_days' => $targetDays,
                'days_elapsed' => $daysElapsed,
                'days_remaining' => max(0, $targetDays - $daysElapsed),
                'is_met' => false,
            ];
        }
        
        $daysElapsed = $created->diff($resolved)->days;
        $isMet = $resolved <= $targetDate;
        
        return [
            'status' => $isMet ? 'met' : 'breached',
            'target_days' => $targetDays,
            'actual_days' => $daysElapsed,
            'is_met' => $isMet,
        ];
    }

    private function calculateHoursDifference(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        $diff = $from->diff($to);
        return ($diff->days * 24) + $diff->h;
    }

    private function determineOverallStatus(array $firstResponseSla, array $resolutionSla): string
    {
        if ($firstResponseSla['status'] === 'breached' || $resolutionSla['status'] === 'breached') {
            return 'at_risk';
        }
        
        if ($firstResponseSla['is_met'] && $resolutionSla['is_met']) {
            return 'compliant';
        }
        
        return 'on_track';
    }
}
