<?php

declare(strict_types=1);

namespace App\FeatureFlag\Strategy;

use App\FeatureFlag\FeatureFlagContext;
use DateTimeImmutable;

class DateRangeStrategy implements StrategyInterface
{
    public function isEnabled(array $config, FeatureFlagContext $context): bool
    {
        $startDate = isset($config['start_date']) ? new DateTimeImmutable($config['start_date']) : null;
        $endDate = isset($config['end_date']) ? new DateTimeImmutable($config['end_date']) : null;
        $currentDate = $context->getCurrentDate() ?? new DateTimeImmutable();
        
        if ($startDate && $currentDate < $startDate) {
            return false;
        }
        
        if ($endDate && $currentDate > $endDate) {
            return false;
        }
        
        return true;
    }
}
