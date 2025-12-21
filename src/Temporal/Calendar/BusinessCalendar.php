<?php

declare(strict_types=1);

namespace App\Temporal\Calendar;

use DateTimeImmutable;

class BusinessCalendar
{
    public function __construct(
        private HolidayProvider $holidayProvider
    ) {
    }

    public function isBusinessDay(DateTimeImmutable $date): bool
    {
        // Check if weekend
        $dayOfWeek = (int) $date->format('N'); // 1 (Monday) to 7 (Sunday)
        if ($dayOfWeek >= 6) {
            return false;
        }

        // Check if holiday
        return !$this->isHoliday($date);
    }

    public function isHoliday(DateTimeImmutable $date): bool
    {
        return $this->holidayProvider->isHoliday($date);
    }

    public function getNextBusinessDay(DateTimeImmutable $date): DateTimeImmutable
    {
        $nextDay = $date->modify('+1 day');
        
        while (!$this->isBusinessDay($nextDay)) {
            $nextDay = $nextDay->modify('+1 day');
        }
        
        return $nextDay;
    }
}
