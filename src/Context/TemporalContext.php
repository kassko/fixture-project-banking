<?php

declare(strict_types=1);

namespace App\Context;

use DateTimeImmutable;

class TemporalContext
{
    public function __construct(
        private DateTimeImmutable $currentDateTime,
        private ?string $period = null,
        private bool $isBusinessDay = true,
        private bool $isHoliday = false,
        private ?string $fiscalQuarter = null
    ) {
    }

    public function getCurrentDateTime(): DateTimeImmutable
    {
        return $this->currentDateTime;
    }

    public function getPeriod(): ?string
    {
        return $this->period;
    }

    public function isBusinessDay(): bool
    {
        return $this->isBusinessDay;
    }

    public function isHoliday(): bool
    {
        return $this->isHoliday;
    }

    public function getFiscalQuarter(): ?string
    {
        return $this->fiscalQuarter;
    }

    public function getCurrentPeriod(): ?string
    {
        return $this->period;
    }

    public function isPromotionalPeriod(): bool
    {
        return in_array($this->period, ['end_of_year_promotion', 'summer_promotion', 'spring_promotion']);
    }

    public function getCurrentPromotion(): ?array
    {
        if (!$this->isPromotionalPeriod()) {
            return null;
        }

        return match($this->period) {
            'end_of_year_promotion' => [
                'code' => 'END_OF_YEAR_2024',
                'name' => 'Promotion de fin d\'année',
                'description' => 'Offre spéciale de fin d\'année',
                'value' => 100.0,
                'valid_days' => 30,
            ],
            'summer_promotion' => [
                'code' => 'SUMMER_2024',
                'name' => 'Promotion d\'été',
                'description' => 'Offre spéciale d\'été',
                'value' => 75.0,
                'valid_days' => 45,
            ],
            'spring_promotion' => [
                'code' => 'SPRING_2024',
                'name' => 'Promotion de printemps',
                'description' => 'Offre spéciale de printemps',
                'value' => 50.0,
                'valid_days' => 30,
            ],
            default => null,
        };
    }
}
