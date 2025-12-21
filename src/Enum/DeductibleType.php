<?php

declare(strict_types=1);

namespace App\Enum;

enum DeductibleType: string
{
    case FIXED = 'FIXED';
    case PERCENTAGE = 'PERCENTAGE';
    case TIERED = 'TIERED';
}
