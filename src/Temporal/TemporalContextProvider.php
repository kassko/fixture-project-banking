<?php

declare(strict_types=1);

namespace App\Temporal;

use App\Context\TemporalContext;
use App\Temporal\Calendar\BusinessCalendar;
use DateTimeImmutable;

class TemporalContextProvider
{
    public function __construct(
        private PeriodResolver $periodResolver,
        private BusinessCalendar $businessCalendar
    ) {
    }

    public function createContext(?DateTimeImmutable $dateTime = null): TemporalContext
    {
        $currentDateTime = $dateTime ?? new DateTimeImmutable();
        $period = $this->periodResolver->resolvePeriod($currentDateTime);
        $isBusinessDay = $this->businessCalendar->isBusinessDay($currentDateTime);
        $isHoliday = $this->businessCalendar->isHoliday($currentDateTime);
        $fiscalQuarter = $this->calculateFiscalQuarter($currentDateTime);

        return new TemporalContext(
            $currentDateTime,
            $period,
            $isBusinessDay,
            $isHoliday,
            $fiscalQuarter
        );
    }

    private function calculateFiscalQuarter(DateTimeImmutable $date): string
    {
        $month = (int) $date->format('n');
        $quarter = (int) ceil($month / 3);
        return 'Q' . $quarter;
    }
}
