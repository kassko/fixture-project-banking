<?php

declare(strict_types=1);

namespace App\Enum;

enum CoverageType: string
{
    case BASIC = 'BASIC';
    case EXTENDED = 'EXTENDED';
    case COMPREHENSIVE = 'COMPREHENSIVE';
    case PREMIUM = 'PREMIUM';
}
