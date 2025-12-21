<?php

declare(strict_types=1);

namespace App\Enum;

enum RateType: string
{
    case FIXED = 'FIXED';
    case VARIABLE = 'VARIABLE';
    case MIXED = 'MIXED';
}
