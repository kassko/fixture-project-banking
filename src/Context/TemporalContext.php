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
}
