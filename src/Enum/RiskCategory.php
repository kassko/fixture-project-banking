<?php

declare(strict_types=1);

namespace App\Enum;

enum RiskCategory: string
{
    case LOW = 'low';
    case MODERATE = 'moderate';
    case HIGH = 'high';
    case CRITICAL = 'critical';
}
