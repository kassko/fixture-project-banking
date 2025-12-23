<?php

declare(strict_types=1);

namespace App\Service\Payment;

use DateTimeImmutable;

class RecurrenceManager
{
    public function calculateNextOccurrence(DateTimeImmutable $currentDate, string $frequency): DateTimeImmutable
    {
        return match ($frequency) {
            'DAILY' => $currentDate->modify('+1 day'),
            'WEEKLY' => $currentDate->modify('+1 week'),
            'BIWEEKLY' => $currentDate->modify('+2 weeks'),
            'MONTHLY' => $currentDate->modify('+1 month'),
            'QUARTERLY' => $currentDate->modify('+3 months'),
            'SEMIANNUAL' => $currentDate->modify('+6 months'),
            'ANNUAL' => $currentDate->modify('+1 year'),
            default => throw new \InvalidArgumentException("Invalid frequency: $frequency"),
        };
    }

    public function generateOccurrences(
        DateTimeImmutable $startDate,
        string $frequency,
        ?DateTimeImmutable $endDate = null,
        ?int $maxOccurrences = null
    ): array {
        $occurrences = [];
        $currentDate = $startDate;
        $count = 0;

        while (true) {
            // Add current occurrence
            $occurrences[] = $currentDate;
            $count++;

            // Check termination conditions
            if ($maxOccurrences !== null && $count >= $maxOccurrences) {
                break;
            }

            // Calculate next occurrence
            $nextDate = $this->calculateNextOccurrence($currentDate, $frequency);

            // Check end date
            if ($endDate !== null && $nextDate > $endDate) {
                break;
            }

            $currentDate = $nextDate;

            // Safety limit to prevent infinite loops
            if ($count >= 1000) {
                break;
            }
        }

        return $occurrences;
    }

    public function getFrequencyDescription(string $frequency): string
    {
        return match ($frequency) {
            'DAILY' => 'Every day',
            'WEEKLY' => 'Every week',
            'BIWEEKLY' => 'Every two weeks',
            'MONTHLY' => 'Every month',
            'QUARTERLY' => 'Every quarter (3 months)',
            'SEMIANNUAL' => 'Every 6 months',
            'ANNUAL' => 'Every year',
            default => 'Unknown frequency',
        };
    }

    public function getOccurrencesPerYear(string $frequency): int
    {
        return match ($frequency) {
            'DAILY' => 365,
            'WEEKLY' => 52,
            'BIWEEKLY' => 26,
            'MONTHLY' => 12,
            'QUARTERLY' => 4,
            'SEMIANNUAL' => 2,
            'ANNUAL' => 1,
            default => 0,
        };
    }
}
