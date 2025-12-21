<?php

declare(strict_types=1);

namespace App\Temporal\Calendar;

use DateTimeImmutable;

class HolidayProvider
{
    private array $holidays = [];

    public function __construct()
    {
        // Initialize with common French holidays
        $this->initializeFrenchHolidays();
    }

    public function isHoliday(DateTimeImmutable $date): bool
    {
        $key = $date->format('Y-m-d');
        return isset($this->holidays[$key]);
    }

    private function initializeFrenchHolidays(): void
    {
        // Fixed holidays
        $year = (int) date('Y');
        
        // New Year's Day
        $this->holidays[$year . '-01-01'] = 'New Year\'s Day';
        
        // Labor Day
        $this->holidays[$year . '-05-01'] = 'Labor Day';
        
        // Victory in Europe Day
        $this->holidays[$year . '-05-08'] = 'Victory in Europe Day';
        
        // Bastille Day
        $this->holidays[$year . '-07-14'] = 'Bastille Day';
        
        // Assumption of Mary
        $this->holidays[$year . '-08-15'] = 'Assumption of Mary';
        
        // All Saints' Day
        $this->holidays[$year . '-11-01'] = 'All Saints\' Day';
        
        // Armistice Day
        $this->holidays[$year . '-11-11'] = 'Armistice Day';
        
        // Christmas
        $this->holidays[$year . '-12-25'] = 'Christmas Day';
    }
}
