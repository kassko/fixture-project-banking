<?php

declare(strict_types=1);

namespace App\Temporal;

use DateTimeImmutable;

class PeriodResolver
{
    private array $periods = [];

    public function __construct(string $configPath)
    {
        // Load periods configuration
        $filePath = $configPath . '/periods.yaml';
        if (file_exists($filePath)) {
            $this->periods = \Symfony\Component\Yaml\Yaml::parseFile($filePath)['periods'] ?? [];
        }
    }

    public function resolvePeriod(DateTimeImmutable $date): ?string
    {
        $month = (int) $date->format('n');
        $day = (int) $date->format('j');

        // Check for end-of-year promotions (November-December)
        if ($month >= 11) {
            return 'end_of_year_promotion';
        }

        // Check for summer promotions (June-August)
        if ($month >= 6 && $month <= 8) {
            return 'summer_promotion';
        }

        // Check for back-to-school (September)
        if ($month === 9) {
            return 'back_to_school';
        }

        return 'regular';
    }
}
