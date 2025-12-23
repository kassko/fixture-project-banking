<?php

declare(strict_types=1);

namespace App\Service\Payment;

use DateTimeImmutable;

class BankingCalendarService
{
    private array $holidays = [
        '2024-01-01', // New Year
        '2024-04-01', // Easter Monday
        '2024-05-01', // Labour Day
        '2024-05-08', // Victory in Europe Day
        '2024-05-09', // Ascension Day
        '2024-05-20', // Whit Monday
        '2024-07-14', // Bastille Day
        '2024-08-15', // Assumption
        '2024-11-01', // All Saints
        '2024-11-11', // Armistice Day
        '2024-12-25', // Christmas
        '2025-01-01', // New Year
        '2025-04-21', // Easter Monday
        '2025-05-01', // Labour Day
        '2025-05-08', // Victory in Europe Day
        '2025-05-29', // Ascension Day
        '2025-06-09', // Whit Monday
        '2025-07-14', // Bastille Day
        '2025-08-15', // Assumption
        '2025-11-01', // All Saints
        '2025-11-11', // Armistice Day
        '2025-12-25', // Christmas
    ];

    public function isBusinessDay(DateTimeImmutable $date): bool
    {
        // Check if weekend
        $dayOfWeek = (int) $date->format('N');
        if ($dayOfWeek >= 6) { // 6 = Saturday, 7 = Sunday
            return false;
        }

        // Check if holiday
        $dateString = $date->format('Y-m-d');
        if (in_array($dateString, $this->holidays)) {
            return false;
        }

        return true;
    }

    public function getNextBusinessDay(DateTimeImmutable $date): DateTimeImmutable
    {
        $nextDay = $date;
        do {
            $nextDay = $nextDay->modify('+1 day');
        } while (!$this->isBusinessDay($nextDay));

        return $nextDay;
    }

    public function getPreviousBusinessDay(DateTimeImmutable $date): DateTimeImmutable
    {
        $prevDay = $date;
        do {
            $prevDay = $prevDay->modify('-1 day');
        } while (!$this->isBusinessDay($prevDay));

        return $prevDay;
    }

    public function adjustToBusinessDay(DateTimeImmutable $date, string $convention = 'following'): DateTimeImmutable
    {
        if ($this->isBusinessDay($date)) {
            return $date;
        }

        return match ($convention) {
            'following' => $this->getNextBusinessDay($date),
            'preceding' => $this->getPreviousBusinessDay($date),
            'modified_following' => $this->modifiedFollowing($date),
            default => $this->getNextBusinessDay($date),
        };
    }

    private function modifiedFollowing(DateTimeImmutable $date): DateTimeImmutable
    {
        $adjusted = $this->getNextBusinessDay($date);
        
        // If next business day is in next month, use previous business day
        if ($adjusted->format('m') !== $date->format('m')) {
            return $this->getPreviousBusinessDay($date);
        }

        return $adjusted;
    }

    public function getBusinessDaysInMonth(int $year, int $month): int
    {
        $count = 0;
        $date = new DateTimeImmutable("$year-$month-01");
        $lastDay = (int) $date->format('t');

        for ($day = 1; $day <= $lastDay; $day++) {
            $currentDate = new DateTimeImmutable("$year-$month-$day");
            if ($this->isBusinessDay($currentDate)) {
                $count++;
            }
        }

        return $count;
    }
}
